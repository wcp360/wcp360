# Browser and accessibility tests

**Status: two real-browser candidate journeys are implemented; complete accessibility and production qualification remain open.**

| Source | Scope |
| :--- | :--- |
| [AdministratorIdentityJourneyTest](AdministratorIdentityJourneyTest.php) | Real administrator password login, MFA enrollment, one-time backup-code display, password/MFA reconfirmation, replay refusal, global own-session revocation and logout. |
| [HostingJourneyTest](HostingJourneyTest.php) | USER login, no administrator confirmation link, runtime selector, theme/page navigation, Turbo continuity and idempotent submission. |

Authentication forms use Turbo to preserve exact Origin with the required no-referrer policy. The authentication frame renders enrollment and one-time recovery-code responses without storing their secrets in a redirect URL or session. Login, reconfirmation, account mutations and logout target the complete page. Missing JavaScript or an invalid Origin never relaxes the server checks. Shared private pages disable Turbo snapshot caching, including enrollment and one-time backup-code views, alongside the server no-store policy.

Run from Core with the documented Chrome/WebDriver environment:

~~~sh
composer test:browser
~~~

The administrator test runs in a separate PHP process with its own SQLite fixture database and HTTP port 9081; the USER test uses port 9080. These databases and browser artifacts belong under ignored var directories. Do not point the fixture initializer at a preview or customer database. Both tests use fixed synthetic identities and the shared fixture source.

Record source revision, environment, commands and outcomes. Local CDP verification and CI Panther/WebDriver are distinct execution environments; a local screenshot or HTTP test does not establish a passing Panther run. Browser and database-role journeys do not replace production authorization or independent security acceptance.

- [Test-suite map](../README.md)
- [Core navigation](../../NAVIGATION.md)
- [Testing configuration and environments](../../../documentation/development/TESTING.md)
