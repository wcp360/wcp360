# V4.0.21.19 --- Mail AutoConfig / AutoDiscover

Canonical client profile contains IMAP/SMTP hostnames, ports, TLS modes,
authentication method and username template.

Operations generate/publish Thunderbird-style autoconfig and supported
AutoDiscover-compatible responses according to Host Profile.
Apple/mobile configuration profiles, if added, are separately
signed/secured artifacts.

No endpoint returns mailbox passwords. TLS names must correspond to
deployed certificates before readiness is reported.
