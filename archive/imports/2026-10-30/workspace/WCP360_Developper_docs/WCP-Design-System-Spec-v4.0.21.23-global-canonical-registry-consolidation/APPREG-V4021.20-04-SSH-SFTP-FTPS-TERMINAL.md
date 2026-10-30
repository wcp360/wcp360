# V4.0.21.20 --- SSH / SFTP / FTPS / Terminal Access

Access modes are capability/policy values: `DISABLED`, `SFTP_ONLY`,
`RESTRICTED_SHELL`, `FULL_SHELL_ALLOWED_BY_ADMIN_POLICY`.

Operations: `SSH-KEY-ADD`, `SSH-KEY-REVOKE`, `ACCESS-MODE-SET`,
`ACCESS-LOCK`, `ACCESS-UNLOCK`, `TERMINAL-SESSION-START`.

Password auth, root login, port policy and shell availability are Host
Profile / security-floor decisions. WCP-USER cannot grant itself a
stronger shell mode.

Browser terminal, if enabled, uses short-lived session authorization,
origin/ CSRF protections, idle/max duration, audit metadata and tenant
OS identity. It is not a root daemon terminal.

Legacy FTP is not a baseline requirement; FTPS is
adapter/capability-gated and SFTP is preferred.
