(function(){
  "use strict";
  var c=window.WCPInsights,s=c.state;
  function findings(){
    var rows=s.findings.map(function(item){return '<tr data-ic-finding><td><strong>'+c.esc(item.id)+'</strong><br><span class="ic-mono">'+c.esc(item.source)+'</span></td><td>'+c.esc(item.title)+'</td><td>'+c.status(item.severity,c.severity(item.severity))+'</td><td>'+c.esc(item.affected)+'</td><td>'+c.esc(item.status)+'</td><td>'+c.esc(item.detected)+'</td></tr>';});
    return '<div class="ic-page">'+c.head("Diagnostics / Findings","Local failures, weaknesses and compatibility gaps","Findings contain normalized identifiers and remediation state, never raw customer content.",c.button("Run local audit","run-audit","primary","fact_check",!s.connected))
      +c.metrics([
        {label:"Critical",value:s.findings.filter(function(item){return item.severity==="Critical";}).length,meta:"Immediate review",icon:"crisis_alert"},
        {label:"High",value:s.findings.filter(function(item){return item.severity==="High";}).length,meta:"Prioritized",icon:"warning"},
        {label:"Open",value:s.findings.filter(function(item){return item.status==="Open";}).length,meta:"Not resolved",icon:"pending_actions"},
        {label:"Last audit",value:s.reports[0]?s.reports[0].created:"Never",meta:"Local execution",icon:"schedule"}
      ])
      +c.panel("Finding inventory","Transmission follows the exact consent category.",rows.length?c.table(["Finding","Summary","Severity","Affected","Status","Detected"],rows):'<div class="ic-empty">No findings yet.</div>',c.status(rows.length+" findings"),true)+'</div>';
  }
  function reports(){
    var reportRows=s.reports.map(function(item){return '<tr data-ic-report><td><strong>'+c.esc(item.id)+'</strong><br><span class="ic-mono">'+c.esc(item.schema)+'</span></td><td>'+c.esc(item.summary)+'</td><td>'+c.status(item.status,"success")+'</td><td>'+c.esc(item.findings)+'</td><td>'+c.esc(item.created)+'</td></tr>';});
    var bugRows=s.bugs.map(function(item){return '<tr data-ic-bug><td><strong>'+c.esc(item.id)+'</strong></td><td>'+c.esc(item.title)+'</td><td>'+c.status(item.priority,item.priority==="Urgent"?"danger":"neutral")+'</td><td>'+c.esc(item.component)+'</td><td>'+c.esc(item.status)+'</td><td>'+c.esc(item.created)+'</td></tr>';});
    return '<div class="ic-page">'+c.head("Evidence / Reports","Audits and customer-submitted bugs","Reports include schema version, consent receipt, redaction result and evidence hashes. Bug context is always reviewed before submission.",c.button("Report a bug","open-bug","primary","bug_report",!s.connected)+c.button("Export all","export-all","","download"))
      +c.panel("Audit reports","Independent JSON evidence remains exportable during vendor outages.",reportRows.length?c.table(["Report","Summary","Status","Findings","Created"],reportRows):'<div class="ic-empty">No audit report generated.</div>',c.status(reportRows.length+" reports"),true)
      +c.panel("Bug reports","Only sanitized, explicitly submitted context appears here.",bugRows.length?c.table(["Bug","Title","Priority","Component","Status","Created"],bugRows):'<div class="ic-empty">No bug report submitted.</div>',c.status(bugRows.length+" bugs"),true)+'</div>';
  }
  function connection(){
    var scopes=Object.keys(s.consent).filter(function(name){return s.consent[name];});
    var identity='<div class="ic-checks">'+c.rule("fingerprint","Installation ID",c.esc(s.installationId),s.connected?"Active":"Local","neutral")+c.rule("corporate_fare","Organization",c.esc(s.organization),"Customer-owned","neutral")+c.rule("https","Endpoint",c.esc(s.endpoint),s.connected?"Pinned":"Inactive",s.connected?"success":"neutral")+c.rule("key","Client credential","Stored in the WCP360 secret vault.","Never displayed","success")+'</div>';
    var network='<div class="ic-checks">'+c.rule("north_east","Outbound TCP 443","Only insights.internal.example.com.","Restricted")+c.rule("sync_lock","Mutual TLS","Both endpoints authenticate each envelope.","Required")+c.rule("replay","Replay protection","Nonce, envelope ID and bounded timestamp.","Required")+c.rule("terminal","Remote jobs","No command or remediation API.","Forbidden","danger")+'</div>';
    return '<div class="ic-page">'+c.head("Transport / Connection","Pair without exposing ROOT","Pairing creates a dedicated outbound mTLS identity. INTERNAL receives evidence but never gains an inbound administration path.",c.actions())
      +c.metrics([
        {label:"State",value:s.connected?"Connected":"Detached",meta:s.connected?"mTLS active":"No vendor link",icon:s.connected?"link":"link_off"},
        {label:"Endpoint",value:s.connected?"Pinned":"None",meta:s.endpoint,icon:"location_on"},
        {label:"Scopes",value:scopes.length,meta:"Consent receipt v1",icon:"verified_user"},
        {label:"Last sync",value:s.lastSync,meta:"Structured evidence",icon:"sync"}
      ])
      +'<div class="ic-grid equal">'+c.panel("Installation identity","Credentials are installation-specific, rotatable and hidden after enrollment.",identity)+c.panel("Network contract","One restricted outbound destination; no inbound listener.",network)+'</div>'
      +'<div class="ic-callout warning">'+c.icon("extension_off")+'<div><strong>Complete detachment</strong><p>Disable or uninstall revokes pairing, stops collection and transmission, removes routes and preserves WCP360 core availability.</p></div></div></div>';
  }
  c.views.findings=findings;
  c.views.reports=reports;
  c.views.connection=connection;
})();
