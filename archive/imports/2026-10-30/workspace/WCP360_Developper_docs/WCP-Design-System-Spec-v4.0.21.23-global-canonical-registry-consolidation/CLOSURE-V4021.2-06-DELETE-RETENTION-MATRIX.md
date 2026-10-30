# V4.0.21.2 --- Plan/Demo/Trial Delete & Retention Matrix

## Statut

**NORMATIVE**

  -------------------------------------------------------------------------------------------
  Resource              Delete behavior   Children          Retention
  --------------------- ----------------- ----------------- ---------------------------------
  HostingPlan           archive by        no cascade        audit retained
                        default; hard                       
                        delete only                         
                        unused draft                        

  PlanAssignment        replaced/closed   none              history retained
                        on reassignment                     

  EntitlementOverride   reset/delete      none              audit retained
                        override only                       

  DemoPolicy            archive if        no cascade        versions retained
                        referenced                          

  DEMO Account          delete Demo Store simulated         configurable demo audit retention
                        namespace only    children only     

  TRIAL Account         suspend → grace → explicit resource `TrialPolicy.retention_seconds`
                        retention → purge inventory         
                        eligible                            

  Reseller Ceiling      not deletable     none              history retained
                        independently;                      
                        update/version                      
  -------------------------------------------------------------------------------------------

No plan, ceiling or entitlement deletion implicitly deletes customer
data.
