# V4.0.21.17 --- Web/PHP/TLS/DNS Dependency Closure

hosting_account ACTIVE → system_identity/managed_root READY →
domain/site → web_vhost + runtime → certificate/DNS.

TLS issuance may require web or DNS challenge capability. DNS deletion
checks mail and certificate dependencies. Account suspension propagates
governed service state without deleting desired configuration. Plan
downgrade can make runtime/site NONCOMPLIANT without silently changing
application data.

Backup captures relevant desired configuration and secret references
according to backup policy; restore revalidates capabilities on
destination.
