# V4.0.21.15 --- P0 End-to-End Scenario Gate

Scénarios obligatoires : 1. install → finalize → first admin MFA →
READY; 2. reseller → plan → account → domain → PHP site → TLS → DNS; 3.
mailbox → DKIM/SPF/DMARC → delivery trace; 4. DB + cache + application
runtime; 5. quota/isolation exhaustion without cross-tenant impact; 6.
backup verified → destructive failure → selective/full restore; 7.
compromised file → malware finding → quarantine → recovery; 8. bad web
config/update → validation failure/rollback; 9. suspended account → no
unintended service mutation; 10. plan downgrade → NONCOMPLIANT
remediation, no silent data loss; 11. admin impersonation → preserved
initiator audit; 12. DEMO → no real infrastructure mutation; 13. IP
migration → DNS/TLS/web/mail reconciliation; 14. failed package/WCP
update → recovery; 15. server reboot/power loss during durable Job →
reconciliation/resume.
