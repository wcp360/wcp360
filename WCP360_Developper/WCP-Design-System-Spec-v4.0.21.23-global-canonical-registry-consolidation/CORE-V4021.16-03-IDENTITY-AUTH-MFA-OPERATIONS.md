# V4.0.21.16 --- Identity / Authentication / MFA Operations

  --------------------------------------------------------------------------------------------------------------------------------------------
  Operation                   Resource     Permission            Scope               Job      Step-up         Audit                  Closure
  --------------------------- ------------ --------------------- ------------------- -------- --------------- ---------------------- ---------
  `IDENTITY-READ`             principal    identity.read         self/scoped         NO_JOB   no              read-sensitive         READY

  `IDENTITY-UPDATE`           principal    identity.update       self/scoped         NO_JOB   conditional     identity.updated       READY

  `CREDENTIAL-ROTATE`         credential   credential.rotate     self/scoped         NO_JOB   yes             credential.rotated     READY

  `SESSION-LIST`              session      session.read          self/scoped         NO_JOB   no              sensitive-read         READY

  `SESSION-REVOKE`            session      session.revoke        self/scoped         NO_JOB   conditional     session.revoked        READY

  `SESSION-REVOKE-ALL`        session      session.revoke_all    self/scoped         NO_JOB   yes             sessions.revoked_all   READY

  `MFA-ENROLL`                mfa_factor   mfa.enroll            self                NO_JOB   authenticated   mfa.enrolled           READY

  `MFA-VERIFY`                mfa_factor   mfa.verify            self                NO_JOB   n/a             mfa.verified           READY

  `MFA-REVOKE`                mfa_factor   mfa.revoke            self/scoped-admin   NO_JOB   yes             mfa.revoked            READY

  `MFA-RECOVERY-REGENERATE`   credential   mfa.recovery.rotate   self                NO_JOB   yes             mfa.recovery_rotated   READY
  --------------------------------------------------------------------------------------------------------------------------------------------

Authentication verification itself is not modeled as a privileged system
Job. Rate-limit, lockout, replay prevention, secure recovery and session
rotation remain mandatory security policies.
