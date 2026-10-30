# V4.0.1 --- Mandatory Scenarios & Edge Cases

Avant code, documenter au minimum : création complète
compte+domaine+web+PHP+DNS+TLS+DB+mail ; suspension/réactivation ;
downgrade sous usage courant ; TLS renewal failure ; DNS externe ;
migration interrompue ; backup checksum failure ; restore conflict ;
mailbox compromise/spam ; disque ou inodes pleins ; control-plane DB
down ; wcpd restart ; privd timeout après side effect ; concurrence de
deux admins ; reseller over-entitlement ; impersonation destructive ;
CMS vulnérable pendant deploy ; upgrade interrompu ; reboot avec Jobs ;
clock incorrecte ; IPv6-only/IPv4-only ; provider DNS/registrar/S3 down
; suppression domaine avec dépendances ; migration/rename Linux account
; restauration control-plane sur serveur neuf.

Chaque scénario précise préconditions, états, permissions, Jobs, locks,
side effects, compensation/reconcile, audit, notifications et critères
de succès.
