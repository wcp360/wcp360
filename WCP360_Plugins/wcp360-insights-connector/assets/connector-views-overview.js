(function(){
  "use strict";
  var c=window.WCPInsights,s=c.state;
  function overview(){
    var rows=s.findings.slice(0,5).map(function(item){return '<tr data-ic-finding><td><strong>'+c.esc(item.id)+'</strong></td><td>'+c.esc(item.title)+'</td><td>'+c.status(item.severity,c.severity(item.severity))+'</td><td>'+c.esc(item.status)+'</td></tr>';});
    return '<div class="ic-page">'+c.head("Optional extension / Overview","Consent-first diagnostic bridge","Audit health, compatibility and extension failures locally, then share only customer-approved structured evidence. The plugin has no remote command path and is not bundled in the public core archive.",c.actions())
      +c.metrics([
        {label:"Connection",value:s.connected?"Connected":"Detached",meta:s.connected?s.endpoint:"No transmission",icon:s.connected?"hub":"link_off"},
        {label:"Consent scopes",value:Object.values(s.consent).filter(Boolean).length+" / 5",meta:"Customer-controlled",icon:"privacy_tip"},
        {label:"Open findings",value:s.findings.filter(function(item){return item.status==="Open";}).length,meta:"Local evidence",icon:"bug_report"},
        {label:"Reports",value:s.reports.length,meta:"Sanitized envelopes",icon:"assessment"}
      ])
      +'<div class="ic-grid">'+c.panel("Capability boundary","The extension remains useful without becoming a vendor backdoor.",'<div class="ic-checks">'
        +c.rule("verified_user","Signed, isolated package","Out-of-process worker with CPU, memory, network and API limits.","Verified")
        +c.rule("lock","Explicit consent","Collection and transmission stop when consent or connection is removed.","Enforced")
        +c.rule("terminal","No remote execution","No shell, node-agent command, impersonation or remediation capability.","Forbidden","danger")
        +c.rule("extension_off","Core independent","Crash, disable or uninstall never blocks hosting operations.","Tested")+'</div>',c.status(s.connected?"Connected":"Detached",s.connected?"success":"warning"))
        +c.panel("Never collected","The contract cannot be expanded from this screen.",'<div class="ic-checks">'
        +c.rule("folder_off","Hosted files","No file contents, paths or access credentials.","Forbidden","danger")
        +c.rule("database_off","Database contents","No schema dumps, queries or rows.","Forbidden","danger")
        +c.rule("mail_lock","Mail content","No messages, mailbox addresses or passwords.","Forbidden","danger")
        +c.rule("key_off","Secrets","No tokens, private keys or secret values.","Forbidden","danger")+'</div>')+'</div>'
      +c.panel("Recent local findings","Evidence stays local until connection and matching consent are both active.",rows.length?c.table(["Finding","Summary","Severity","Status"],rows):'<div class="ic-empty">No finding yet. Pair the installation and run a local audit.</div>',c.status(s.findings.length+" total"),true)
      +'<div class="ic-callout warning">'+c.icon("info")+'<div><strong>Prototype transport</strong><p>Browser storage simulates the exchange. Production requires mTLS, endpoint pinning, signed envelopes, nonces, replay protection and rotating installation credentials.</p></div></div></div>';
  }
  function policy(){
    function choice(name,title,copy){return '<label class="ic-switch"><input type="checkbox" name="'+name+'" '+(s.consent[name]?"checked":"")+'><span><strong>'+title+'</strong><small>'+copy+'</small></span></label>';}
    var choices='<div class="ic-policy">'
      +choice("inventory","System inventory","Version, OS family, architecture and public module identifiers.")
      +choice("health","Service health summary","Desired/observed state and standardized failure codes.")
      +choice("failures","Extension failures","Crash, timeout, circuit-breaker and rollback results.")
      +choice("security","Security metadata","Finding ID, severity, component and remediation state.")
      +choice("bugContext","Reviewed bug context","Route, correlation ID and selected sanitized diagnostics.")+'</div>';
    var settings='<div class="ic-form-grid"><label class="ic-field"><span>Audit schedule</span><select name="schedule"><option '+(s.schedule==="Manual only"?"selected":"")+'>Manual only</option><option '+(s.schedule==="Daily at 03:00"?"selected":"")+'>Daily at 03:00</option><option '+(s.schedule==="Weekly on Sunday"?"selected":"")+'>Weekly on Sunday</option></select></label><label class="ic-field"><span>Vendor retention</span><select name="retention"><option '+(s.retention==="7 days"?"selected":"")+'>7 days</option><option '+(s.retention==="30 days"?"selected":"")+'>30 days</option><option '+(s.retention==="90 days"?"selected":"")+'>90 days</option></select></label></div><div class="ic-checks" style="margin-top:12px">'+c.rule("encrypted","Encrypted bounded queue","Outage buffering has a size cap and backoff.","Required")+c.rule("preview","Redaction preview","Customer reviews the exact bug payload.","Required")+c.rule("delete_sweep","Retention enforcement","Expired evidence is deleted automatically.","Required")+'</div>';
    var form='<form data-ic-policy-form class="ic-grid equal">'+c.panel("Approved evidence categories","Every category is deny-by-default and versioned in a consent receipt.",choices)+c.panel("Schedule and retention","Local collection and vendor retention remain independently controlled.",settings)+'</form>';
    return '<div class="ic-page">'+c.head("Privacy / Collection policy","Choose exactly what may leave this server","Disabling a scope stops new collection and removes it from the next envelope. No scope can grant shell access, secret export or hosted-content access.",c.button("Save policy","save-policy","primary","save"))+form+'<div class="ic-callout danger">'+c.icon("gpp_bad")+'<div><strong>Non-configurable prohibition</strong><p>Hosted files, messages, database rows, credentials, tokens and vendor-triggered server actions are always outside this extension contract.</p></div></div></div>';
  }
  c.views={overview:overview,policy:policy};
})();
