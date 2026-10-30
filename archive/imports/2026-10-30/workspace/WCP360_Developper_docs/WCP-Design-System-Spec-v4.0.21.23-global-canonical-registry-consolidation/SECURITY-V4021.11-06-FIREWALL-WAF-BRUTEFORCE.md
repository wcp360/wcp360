# V4.0.21.11 --- Firewall / WAF / Fail2ban Security Contract

## Couches

1.  host firewall ;
2.  service exposure policy ;
3.  anti-bruteforce/rate controls ;
4.  optional WAF ;
5.  application-level security.

## Firewall

Backend adapter nftables/firewalld where certified. Rules are typed:
service/port/protocol/source/action/priority/expiry. Pas de règle
shell/raw.

## Lockout safety

Toute modification d'accès administration effectue preflight,
management-path protection, staged apply, verification et rollback timer
si nécessaire.

## Fail2ban / abuse

Jails/profiles gouvernés ; ban/unban audités ; durée et source de
décision observables. Un ban automatique n'est pas confondu avec une
firewall policy permanente.

## WAF

ModSecurity/Coraza/other certified adapter possible. Rule sets et
exclusions sont versionnés ; exclusions globales dangereuses exigent
step-up/admin scope.
