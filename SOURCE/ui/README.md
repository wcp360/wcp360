# WCP360 UI candidate

Dependency-free static implementation of the active visual shell concepts:

- separate WCP-USER and WCP-ADMIN information architecture;
- context rail + workspace sidebar + command header;
- neutral cards and thin borders;
- light/dark themes;
- responsive/mobile navigation;
- Action Center focused on items requiring attention.

This is a UI implementation candidate only. It is not wired to authentication, sessions, cookies or production APIs. The real web runtime must preserve the Security v1.0 session split (`__Host-wcp_user_session` vs `__Host-wcp_admin_session`) and separate origins/runtime identities.
