# Generated Service Configuration Locations

## Principle

WCP-generated configuration must be isolated in predictable include directories wherever upstream software allows.

## Nginx

Preferred logical structure:

```text
/etc/nginx/wcp360/
├── sites/
├── upstreams/
├── snippets/
└── tls/
```

Native distro include integration may differ, but WCP-owned files remain segregated.

## PHP-FPM

```text
/etc/php/<version>/fpm/pool.d/wcp360-*.conf
```

or distro-equivalent managed include directory.

## Mail

Use dedicated WCP-managed include/maps directories where the chosen MTA/IMAP stack supports them.

## Firewall

WCP must manage a dedicated ruleset/table/chain namespace where supported.

Do not rewrite unrelated administrator rules blindly.

## Databases

Prefer generated include fragments rather than replacing monolithic upstream configuration when supported.

## Provenance header

Every generated text config should identify:
- WCP360 ownership;
- resource ID;
- revision;
- generated timestamp;
- warning against unmanaged direct edits where reconciliation applies.