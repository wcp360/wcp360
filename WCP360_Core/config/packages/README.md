# Framework and library settings

Reserve this location for actual Symfony/package configuration created by the approved bootstrap: framework, security, Doctrine, Messenger, Twig, cache and other selected components. This README supplies no production defaults or enabled bundles.

Database connection configuration here concerns the panel's runtime identities. Creating a customer's MySQL/PostgreSQL database is a Data use case; managing the database service's Linux files uses resources/services and the accepted agent path.

Configuration imports and environment overrides must be explicit and tested. Avoid copying the same credentials or unrelated business defaults into each package file.

- [Configuration boundary](../README.md)
- [Context isolation](../contexts/README.md)
