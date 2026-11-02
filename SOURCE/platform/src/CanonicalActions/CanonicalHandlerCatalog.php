<?php

declare(strict_types=1);

namespace Wcp360\CanonicalActions;

use ReflectionClass;
use Wcp360\Application\ActionHandler;
use Wcp360\Contracts\ApiContractDefinition;
use Wcp360\Contracts\ApiContractRegistry;
use Wcp360\Shared\ContractViolation;

final class CanonicalHandlerCatalog
{
    /**
     * @return array<string,array{class:class-string<ActionHandler>,profile:CanonicalPortProfile}>
     */
    public static function definitions(): array
    {
        return [
            'api.tokens.manage' => ['class' => ApiTokensManageHandler::class, 'profile' => CanonicalPortProfile::Managed],
            'applications.create' => ['class' => ApplicationsCreateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'applications.deploy' => ['class' => ApplicationsDeployHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'applications.maintenance.set' => ['class' => ApplicationsMaintenanceSetHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'applications.read' => ['class' => ApplicationsReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'applications.rollback' => ['class' => ApplicationsRollbackHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'applications.runtime.update' => ['class' => ApplicationsRuntimeUpdateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'approvals.decide' => ['class' => ApprovalsDecideHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'approvals.request' => ['class' => ApprovalsRequestHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'audit.export' => ['class' => AuditExportHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'audit.read' => ['class' => AuditReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'automations.manage' => ['class' => AutomationsManageHandler::class, 'profile' => CanonicalPortProfile::Managed],
            'backups.create' => ['class' => BackupsCreateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'backups.delete' => ['class' => BackupsDeleteHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'backups.policy.update' => ['class' => BackupsPolicyUpdateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'backups.read' => ['class' => BackupsReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'backups.restore' => ['class' => BackupsRestoreHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'break_glass.start' => ['class' => BreakGlassStartHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'clusters.failover' => ['class' => ClustersFailoverHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'clusters.read' => ['class' => ClustersReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'databases.create' => ['class' => DatabasesCreateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'databases.delete' => ['class' => DatabasesDeleteHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'databases.export' => ['class' => DatabasesExportHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'databases.grants.set' => ['class' => DatabasesGrantsSetHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'databases.import' => ['class' => DatabasesImportHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'databases.read' => ['class' => DatabasesReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'databases.restore' => ['class' => DatabasesRestoreHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'databases.users.manage' => ['class' => DatabasesUsersManageHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'domains.create' => ['class' => DomainsCreateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'domains.dnssec.set' => ['class' => DomainsDnssecSetHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'domains.read' => ['class' => DomainsReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'domains.record.create' => ['class' => DomainsRecordCreateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'domains.record.delete' => ['class' => DomainsRecordDeleteHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'domains.record.update' => ['class' => DomainsRecordUpdateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'email.aliases.manage' => ['class' => EmailAliasesManageHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'email.antispam.update' => ['class' => EmailAntispamUpdateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'email.auth.apply' => ['class' => EmailAuthApplyHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'email.domains.manage' => ['class' => EmailDomainsManageHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'email.mailboxes.manage' => ['class' => EmailMailboxesManageHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'email.read' => ['class' => EmailReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'email.settings.update' => ['class' => EmailSettingsUpdateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'files.access.manage' => ['class' => FilesAccessManageHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'files.delete' => ['class' => FilesDeleteHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'files.permissions.update' => ['class' => FilesPermissionsUpdateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'files.read' => ['class' => FilesReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'files.write' => ['class' => FilesWriteHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'iam.account_overrides.manage' => ['class' => IamAccountOverridesManageHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'iam.features.manage' => ['class' => IamFeaturesManageHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'iam.plans.manage' => ['class' => IamPlansManageHandler::class, 'profile' => CanonicalPortProfile::Managed],
            'iam.resellers.manage' => ['class' => IamResellersManageHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'iam.roles.manage' => ['class' => IamRolesManageHandler::class, 'profile' => CanonicalPortProfile::Managed],
            'iam.users.manage' => ['class' => IamUsersManageHandler::class, 'profile' => CanonicalPortProfile::Managed],
            'impersonation.start' => ['class' => ImpersonationStartHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'incidents.manage' => ['class' => IncidentsManageHandler::class, 'profile' => CanonicalPortProfile::Managed],
            'jobs.cancel' => ['class' => JobsCancelHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'jobs.read' => ['class' => JobsReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'monitoring.read' => ['class' => MonitoringReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'network.firewall.apply' => ['class' => NetworkFirewallApplyHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'network.read' => ['class' => NetworkReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'network.routes.update' => ['class' => NetworkRoutesUpdateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'notifications.manage' => ['class' => NotificationsManageHandler::class, 'profile' => CanonicalPortProfile::Managed],
            'privilege.elevate' => ['class' => PrivilegeElevateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'processes.kill' => ['class' => ProcessesKillHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'processes.read' => ['class' => ProcessesReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'processes.terminate' => ['class' => ProcessesTerminateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'security.keys.manage' => ['class' => SecurityKeysManageHandler::class, 'profile' => CanonicalPortProfile::Managed],
            'security.network.update' => ['class' => SecurityNetworkUpdateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'security.sessions.manage' => ['class' => SecuritySessionsManageHandler::class, 'profile' => CanonicalPortProfile::Managed],
            'security.tokens.manage' => ['class' => SecurityTokensManageHandler::class, 'profile' => CanonicalPortProfile::Managed],
            'security.waf.update' => ['class' => SecurityWafUpdateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'servers.read' => ['class' => ServersReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'servers.reboot' => ['class' => ServersRebootHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'servers.service.reload' => ['class' => ServersServiceReloadHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'servers.service.restart' => ['class' => ServersServiceRestartHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'service_config.apply' => ['class' => ServiceConfigApplyHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'service_config.read' => ['class' => ServiceConfigReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'settings.profile.update' => ['class' => SettingsProfileUpdateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'settings.system.update' => ['class' => SettingsSystemUpdateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'ssl.manage' => ['class' => SslManageHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'ssl.read' => ['class' => SslReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'storage.expand' => ['class' => StorageExpandHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'storage.read' => ['class' => StorageReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'support.bundle.create' => ['class' => SupportBundleCreateHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'updates.apply' => ['class' => UpdatesApplyHandler::class, 'profile' => CanonicalPortProfile::Mutation],
            'updates.read' => ['class' => UpdatesReadHandler::class, 'profile' => CanonicalPortProfile::Read],
            'webhooks.manage' => ['class' => WebhooksManageHandler::class, 'profile' => CanonicalPortProfile::Managed],
        ];
    }

    /** @return list<string> */
    public static function actionIds(): array
    {
        return array_keys(self::definitions());
    }

    public static function count(): int
    {
        return count(self::definitions());
    }

    public static function validateAgainst(ApiContractRegistry $contracts): void
    {
        $definitions = self::definitions();
        if (count($definitions) !== 86 || $contracts->count() !== 86) {
            throw new ContractViolation('canonical handler/API contract count mismatch');
        }

        $expected = [];
        foreach ($contracts->all() as $contract) {
            $expected[$contract->actionId] = self::profileFor($contract);
        }
        ksort($expected);

        $actual = [];
        $classes = [];
        foreach ($definitions as $actionId => $definition) {
            $class = $definition['class'];
            $profile = $definition['profile'];
            if (isset($classes[$class])) {
                throw new ContractViolation('canonical handler class reused by multiple actions');
            }
            $classes[$class] = true;
            if (!class_exists($class) || !is_subclass_of($class, ActionHandler::class)) {
                throw new ContractViolation('canonical action handler class missing/invalid: ' . $actionId);
            }
            if ($class::ACTION_ID !== $actionId) {
                throw new ContractViolation('canonical handler ACTION_ID mismatch: ' . $actionId);
            }

            $reflection = new ReflectionClass($class);
            $constructor = $reflection->getConstructor();
            $parameters = $constructor?->getParameters() ?? [];
            if (count($parameters) !== 1 || $parameters[0]->getType() === null) {
                throw new ContractViolation('canonical handler must have exactly one typed port: ' . $actionId);
            }
            $expectedPort = match ($profile) {
                CanonicalPortProfile::Read => ReadOperationPort::class,
                CanonicalPortProfile::Mutation => MutationOperationPort::class,
                CanonicalPortProfile::Managed => ManagedOperationPort::class,
            };
            if ((string) $parameters[0]->getType() !== $expectedPort) {
                throw new ContractViolation('canonical handler port profile mismatch: ' . $actionId);
            }
            $actual[$actionId] = $profile;
        }
        ksort($actual);

        if (array_keys($actual) !== array_keys($expected)) {
            throw new ContractViolation('canonical handler action set drifted from API registry');
        }
        foreach ($expected as $actionId => $profile) {
            if ($actual[$actionId] !== $profile) {
                throw new ContractViolation('canonical handler runtime profile drift: ' . $actionId);
            }
        }
    }

    private static function profileFor(ApiContractDefinition $contract): CanonicalPortProfile
    {
        return match ($contract->retry . '|' . $contract->idempotency) {
            'RR|none' => CanonicalPortProfile::Read,
            'RK|key-required', 'RN|key-required' => CanonicalPortProfile::Mutation,
            'RK|key when mutating' => CanonicalPortProfile::Managed,
            default => throw new ContractViolation('unsupported canonical handler runtime profile'),
        };
    }
}
