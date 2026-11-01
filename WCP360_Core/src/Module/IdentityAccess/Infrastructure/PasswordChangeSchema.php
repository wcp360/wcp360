<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

/** Frozen candidate migration input: two surface-bound PostgreSQL operations, no generic writer. */
final class PasswordChangeSchema
{
    public static function name(string $surface): string
    {
        return match ($surface) {
            'user' => 'public.wcp_candidate_user_password_change',
            'admin' => 'public.wcp_candidate_admin_password_change',
            default => throw new \InvalidArgumentException('Unknown credential surface.'),
        };
    }

    public static function signature(string $surface): string
    {
        return self::name($surface).'(uuid,text,text,text,uuid,text,integer,bigint)';
    }

    /** @return list<string> */
    public static function statements(Connection $db): array
    {
        if (!$db->getDatabasePlatform() instanceof PostgreSQLPlatform) return [];
        $sql = [];
        foreach (['user', 'admin'] as $surface) {
            $sql[] = 'CREATE FUNCTION '.self::name($surface).'(
                p_actor uuid, p_username text, p_expected text, p_replacement text,
                p_selector uuid, p_session_id text, p_revision integer, p_proved_at bigint
            ) RETURNS boolean LANGUAGE plpgsql VOLATILE SECURITY DEFINER
            SET search_path TO pg_catalog, pg_temp SET lock_timeout TO \'2s\'
            AS $wcp_password$'.self::body($surface).'$wcp_password$';
            $sql[] = 'REVOKE ALL ON FUNCTION '.self::signature($surface).' FROM PUBLIC';
        }
        return $sql;
    }

    /** @return list<string> */
    public static function fixtureDrops(Connection $db): array
    {
        if (!$db->getDatabasePlatform() instanceof PostgreSQLPlatform) return [];
        return array_map(static fn (string $surface): string => 'DROP FUNCTION IF EXISTS '.self::signature($surface), ['user', 'admin']);
    }

    public static function body(string $surface): string
    {
        self::name($surface);
        $body = <<<'SQL'

DECLARE
    v_user public.wcp_candidate_users%ROWTYPE;
    v_session public.wcp_candidate___SURFACE___sessions%ROWTYPE;
    v_now bigint;
    v_revision integer;
    v_strength integer;
    v_parts text[];
    v_changed integer;
    v_audit_at text;
BEGIN
    IF session_user IS DISTINCT FROM (CASE pg_catalog.current_database()
        WHEN 'wcp360_test' THEN 'wcp360_test___SURFACE__'
        WHEN 'wcp360_workbench' THEN 'wcp360_dev___SURFACE__'
        ELSE NULL END) OR pg_catalog.current_setting('role') <> 'none' THEN
        RAISE EXCEPTION USING ERRCODE = '42501', MESSAGE = 'Candidate credential change refused.';
    END IF;
    IF p_actor IS NULL OR p_username IS NULL OR p_expected IS NULL OR p_replacement IS NULL
        OR p_selector IS NULL OR p_session_id IS NULL
        OR pg_catalog.length(p_username) NOT BETWEEN 3 AND 32
        OR pg_catalog.length(p_expected) NOT BETWEEN 1 AND 255
        OR pg_catalog.length(p_replacement) NOT BETWEEN 1 AND 255
        OR pg_catalog.length(p_session_id) NOT BETWEEN 16 AND 256
        OR p_session_id COLLATE "C" ~ '[^A-Za-z0-9,-]' THEN
        RAISE EXCEPTION USING ERRCODE = '42501', MESSAGE = 'Candidate credential change refused.';
    END IF;
    v_parts := pg_catalog.regexp_match(p_replacement COLLATE "C",
        '^\$argon2id\$v=19\$m=([0-9]{1,6}),t=([0-9]{1,2}),p=1\$[A-Za-z0-9+/]{22}\$[A-Za-z0-9+/]{43}$');
    IF v_parts IS NULL OR v_parts[1]::integer NOT BETWEEN 16384 AND 262144
        OR v_parts[2]::integer NOT BETWEEN 3 AND 8 OR p_replacement = p_expected THEN
        RAISE EXCEPTION USING ERRCODE = '42501', MESSAGE = 'Candidate credential change refused.';
    END IF;
    SELECT * INTO v_user FROM public.wcp_candidate_users
        WHERE id = p_actor AND context = '__SURFACE__' AND active = 1 FOR UPDATE;
    IF NOT FOUND OR v_user.username IS DISTINCT FROM p_username
        OR v_user.password_hash IS DISTINCT FROM p_expected THEN
        RAISE EXCEPTION USING ERRCODE = '42501', MESSAGE = 'Candidate credential change refused.';
    END IF;
    SELECT * INTO v_session FROM public.wcp_candidate___SURFACE___sessions
        WHERE id = p_selector AND user_id = p_actor FOR UPDATE;
    IF NOT FOUND THEN
        RAISE EXCEPTION USING ERRCODE = '42501', MESSAGE = 'Candidate credential change refused.';
    END IF;
    v_now := pg_catalog.floor(EXTRACT(epoch FROM pg_catalog.clock_timestamp()))::bigint;
    v_strength := 1;
    IF '__SURFACE__' = 'admin' THEN
        SELECT COALESCE((SELECT revision FROM public.wcp_candidate_mfa WHERE user_id = p_actor), 0),
            CASE WHEN EXISTS (SELECT 1 FROM public.wcp_candidate_mfa WHERE user_id = p_actor AND sealed_secret IS NOT NULL) THEN 2 ELSE 1 END
            INTO v_revision, v_strength;
        IF p_revision IS NULL OR p_proved_at IS NULL OR p_revision <> v_revision
            OR p_proved_at > v_now OR p_proved_at < v_now - 299 THEN
            RAISE EXCEPTION USING ERRCODE = '42501', MESSAGE = 'Candidate credential change refused.';
        END IF;
    END IF;
    IF v_session.revoked_at IS NOT NULL OR v_session.surface_claim <> '__CLAIM__'
        OR v_session.auth_strength < v_strength
        OR v_session.reauth_attempts NOT BETWEEN 1 AND 5
        OR v_session.reauth_window_started > v_now OR v_now >= v_session.reauth_window_started + 900
        OR v_session.created_at > v_session.last_seen_at OR v_session.last_seen_at > v_now
        OR v_now >= v_session.last_seen_at + __IDLE__
        OR v_session.expires_at <> v_session.created_at + __ABSOLUTE__
        OR v_now >= v_session.expires_at
        OR v_session.secret_verifier IS DISTINCT FROM pg_catalog.encode(pg_catalog.sha256(
            pg_catalog.convert_to('wcp360.candidate.session/1|__SURFACE__|' || p_actor::text || '|' || p_session_id, 'UTF8')), 'hex') THEN
        RAISE EXCEPTION USING ERRCODE = '42501', MESSAGE = 'Candidate credential change refused.';
    END IF;
    UPDATE public.wcp_candidate_users SET password_hash = p_replacement
        WHERE id = p_actor AND context = '__SURFACE__' AND active = 1 AND password_hash = p_expected;
    GET DIAGNOSTICS v_changed = ROW_COUNT;
    IF v_changed <> 1 THEN
        RAISE EXCEPTION USING ERRCODE = '42501', MESSAGE = 'Candidate credential change refused.';
    END IF;
    v_audit_at := pg_catalog.to_char(pg_catalog.to_timestamp(v_now) AT TIME ZONE 'UTC', 'YYYY-MM-DD"T"HH24:MI:SS"Z"');
    WITH revoked AS (
        UPDATE public.wcp_candidate___SURFACE___sessions
        SET revoked_at = GREATEST(last_seen_at, v_now)
        WHERE user_id = p_actor AND revoked_at IS NULL RETURNING id
    )
    INSERT INTO public.wcp_candidate_audit(id, actor_id, tenant_id, kind, resource_id, created_at)
        SELECT pg_catalog.gen_random_uuid(), p_actor, NULL, 'candidate.session.revoked', id, v_audit_at FROM revoked;
    INSERT INTO public.wcp_candidate_audit(id, actor_id, tenant_id, kind, resource_id, created_at)
        VALUES (pg_catalog.gen_random_uuid(), p_actor, NULL, 'candidate.account.password_changed', p_actor, v_audit_at);
    RETURN true;
END;

SQL;
        return strtr($body, [
            '__SURFACE__' => $surface, '__CLAIM__' => $surface === 'admin' ? 'WCP-ADMIN' : 'WCP-USER',
            '__IDLE__' => $surface === 'admin' ? '900' : '1800',
            '__ABSOLUTE__' => $surface === 'admin' ? '28800' : '43200',
        ]);
    }
}
