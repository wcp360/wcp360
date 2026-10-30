# V4.0.21.9 --- Mail Deliverability / Autoconfig Wizard

## Wizard

1.  verify domain ownership/scope;
2.  inspect authoritative DNS capability;
3.  provision mail domain;
4.  generate DKIM key reference;
5.  propose/publish SPF;
6.  propose/publish DKIM;
7.  configure DMARC;
8.  optionally configure MTA-STS/TLS-RPT;
9.  publish Autoconfig/Autodiscover where supported;
10. observe DNS propagation/validation;
11. test SMTP/IMAP endpoints;
12. show desired vs observed status.

## Status vocabulary

`NOT_CONFIGURED`, `DESIRED`, `PUBLISHED`, `OBSERVED`, `VALIDATED`,
`DEGRADED`, `ERROR`.

The wizard must distinguish local configuration from external DNS
propagation and cannot promise inbox placement.
