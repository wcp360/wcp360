# V4.0.21.17 --- Webserver Adapter / VHost Contract

Canonical `WebserverProvider` hides Nginx/Apache-specific syntax.

Pipeline: desired vhost → render in staging → syntax validate →
semantic/path validate → atomic publish → reload/graceful apply → health
probe → observed state → commit or rollback.

Operations: `VHOST-RECONCILE`, `VHOST-VALIDATE`, `VHOST-RELOAD`,
`WEB-CONFIG-ROLLBACK`.

No UI/API operation accepts raw privileged server configuration.
Advanced directives are typed/capability-gated policy objects. Backend
reload failure must not destroy last-known-good configuration.
