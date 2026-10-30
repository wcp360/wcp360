# V4.0.21.18 --- phpMyAdmin / pgAdmin / Adminer SSO Contract

SSO is implemented through a short-lived, single-use scoped launch
ticket or equivalent adapter mechanism. WCP does not place plaintext DB
passwords in URLs.

Ticket binds user/session, tenant, DB principal, allowed target/tool,
expiry and nonce. Consumption is audited and replay denied. Step-up can
be required for sensitive principals.

The external admin tool never receives WCP root/daemon credentials.
