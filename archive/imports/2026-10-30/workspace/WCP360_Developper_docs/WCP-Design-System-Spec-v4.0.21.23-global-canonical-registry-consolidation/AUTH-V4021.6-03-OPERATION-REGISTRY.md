# V4.0.21.6 --- Exact Identity/Auth Operation Registry

## Statut

**NORMATIVE B2 --- IDENTITY/AUTH**

  -----------------------------------------------------------------------------------------------------------------
  Operation ID              Surface      Capability               Scope              Step-up            Execution
                            action                                                                      
  ------------------------- ------------ ------------------------ ------------------ ------------------ -----------
  AUTH-LOGIN                login        auth.login               public/tenant      policy             NO_JOB

  AUTH-LOGOUT               logout       session.revoke.self      self               no                 NO_JOB
                            current                                                                     

  SESSION-LIST              list own     session.read.self        self               no                 NO_JOB
                            sessions                                                                    

  SESSION-REVOKE            revoke       session.revoke           self/admin-scope   conditional        NO_JOB
                            session                                                                     

  SESSION-REVOKE-ALL        revoke all   session.revoke.all       self/admin-scope   required for admin NO_JOB
                                                                                     target             

  MFA-ENROLL-BEGIN          start        mfa.manage.self          self               recent-auth        NO_JOB
                            enrollment                                                                  

  MFA-ENROLL-COMPLETE       complete     mfa.manage.self          self               recent-auth        NO_JOB
                            enrollment                                                                  

  MFA-FACTOR-REMOVE         remove       mfa.manage               self/admin-scope   REQUIRED           NO_JOB
                            factor                                                                      

  MFA-RECOVERY-ROTATE       rotate       mfa.manage.self          self               REQUIRED           NO_JOB
                            recovery set                                                                

  PRINCIPAL-CREATE          create human identity.manage          tenant/reseller    policy             NO_JOB
                            principal                                                                   

  PRINCIPAL-UPDATE          update       identity.manage          resource           conditional        NO_JOB
                            principal                                                                   

  PRINCIPAL-SUSPEND         suspend      identity.suspend         resource           REQUIRED           NO_JOB
                                                                                     privileged         

  PRINCIPAL-RESUME          resume       identity.suspend         resource           REQUIRED           NO_JOB
                                                                                     privileged         

  PRINCIPAL-DISABLE         disable      identity.disable         resource           REQUIRED           NO_JOB

  API-TOKEN-CREATE          create token api_token.manage         self/resource      recent-auth        NO_JOB

  API-TOKEN-REVOKE          revoke token api_token.manage         self/resource      conditional        NO_JOB

  SERVICE-ACCOUNT-CREATE    create       service_account.manage   tenant             REQUIRED           NO_JOB
                            service                                                                     
                            account                                                                     

  SERVICE-ACCOUNT-DISABLE   disable      service_account.manage   resource           REQUIRED           NO_JOB
                            service                                                                     
                            account                                                                     

  RECOVERY-BEGIN            begin        auth.recovery            public             anti-abuse         NO_JOB
                            recovery                                                                    

  RECOVERY-COMPLETE         complete     auth.recovery            challenge          challenge+policy   NO_JOB
                            recovery                                                                    
  -----------------------------------------------------------------------------------------------------------------

All mutations require audit actions and optimistic version checks where
a mutable resource already exists.
