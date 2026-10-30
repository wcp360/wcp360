# V4.0.21.1 --- RBAC Explicit Actor Corrections

## Statut

**NORMATIVE CORRECTION**

The following replace shortcut rows in V4.0.21:

  ------------------------------------------------------------------------
  Capability               Explicit eligible       Scope
                           actors                  
  ------------------------ ----------------------- -----------------------
  `site.runtime.manage`    `platform_admin`,       owned site/account
                           `tenant_admin`,         
                           `reseller_admin`,       
                           `account_admin`         

  `database.user.manage`   `platform_admin`,       owned database/account
                           `tenant_admin`,         
                           `reseller_admin`,       
                           `account_admin`         

  `mailbox.manage`         `platform_admin`,       owned mail-domain; self
                           `tenant_admin`,         only for own mailbox
                           `reseller_admin`,       
                           `account_admin`,        
                           restricted              
                           `mailbox_self`          

  `backup.restore`         `platform_admin`,       owned restore target
                           `tenant_admin`,         
                           `reseller_admin`,       
                           `account_admin` **only  
                           when an explicit        
                           capability binding      
                           exists**                
  ------------------------------------------------------------------------

There is no inheritance by prose such as `same as X`. Every capability
must be independently present in the canonical matrix.

`if granted` is replaced by the objective condition: an active,
non-expired capability binding for `backup.restore` whose resolved scope
contains the target resource.
