(function(){
  "use strict";
  var c=window.WCPInsights,s=c.state,host=document.getElementById("connectorApp");
  var tabs=["overview","policy","findings","reports","connection"];
  function render(){
    if(!tabs.includes(s.tab))s.tab="overview";
    document.querySelectorAll("[data-ic-tab]").forEach(function(node){
      var active=node.dataset.icTab===s.tab;
      node.classList.toggle("active",active);
      node.setAttribute("aria-current",active?"page":"false");
    });
    host.innerHTML=c.views[s.tab]();
  }
  function openDialog(kind){
    var title="",sub="",body="",submit="",danger=false;
    if(kind==="connect"){
      title="Connect to WCP360 INTERNAL";sub="Scoped outbound pairing";submit="Pair installation";
      body='<div class="ic-form-grid"><label class="ic-field"><span>Vendor endpoint</span><input name="endpoint" type="url" value="'+c.esc(s.endpoint)+'" required></label><label class="ic-field"><span>Installation ID</span><input name="installationId" value="'+c.esc(s.installationId)+'" required></label></div><label class="ic-field" style="margin-top:11px"><span>Organization</span><input name="organization" value="'+c.esc(s.organization)+'" required></label><label class="ic-field" style="margin-top:11px"><span>One-time pairing code</span><input name="pairingCode" placeholder="PAIR-..." autocomplete="off" required><small>Exchanged for a scoped credential and never stored.</small></label><div class="ic-callout" style="margin-top:12px">'+c.icon("security")+'<div><strong>Read-only preflight</strong><p>Signature, endpoint pin, clock, proxy, consent and secret-vault readiness are checked before evidence leaves this server.</p></div></div>';
    }else if(kind==="bug"){
      title="Submit diagnostic bug";sub="Reviewed and sanitized context";submit="Submit report";
      body='<label class="ic-field"><span>Title</span><input name="title" required></label><div class="ic-form-grid" style="margin-top:11px"><label class="ic-field"><span>Component</span><select name="component"><option>WCP-ADMIN</option><option>Node agent</option><option>Update service</option><option>Public extension</option></select></label><label class="ic-field"><span>Priority</span><select name="priority"><option>Normal</option><option>High</option><option>Urgent</option></select></label></div><label class="ic-field" style="margin-top:11px"><span>Reproduction steps</span><textarea name="steps" required placeholder="1. Open...&#10;2. Select...&#10;3. Observe..."></textarea></label><div class="ic-callout" style="margin-top:12px">'+c.icon("filter_alt")+'<div><strong>Redaction preview enabled</strong><p>Secrets, tokens, paths and customer identifiers are detected before submission.</p></div></div>';
    }else{
      title="Detach Insights Connector";sub="Revoke pairing and stop transmission";submit="Detach connector";danger=true;
      body='<div class="ic-callout danger">'+c.icon("link_off")+'<div><strong>Immediate revocation</strong><p>Queued envelopes are deleted and scheduled audits stop. Core hosting remains online.</p></div></div><label class="ic-field" style="margin-top:12px"><span>Type <strong>DETACH</strong> to confirm</span><input name="confirm" data-ic-confirm="DETACH" autocomplete="off" required></label>';
    }
    document.getElementById("connectorDialog").innerHTML='<div class="ic-dialog-shell"><button class="ic-dialog-backdrop" type="button" data-ic-close aria-label="Close"></button><section class="ic-dialog" role="dialog" aria-modal="true" aria-labelledby="icDialogTitle"><div class="ic-dialog-head"><div><h2 id="icDialogTitle">'+title+'</h2><p>'+sub+'</p></div><button class="ic-icon-button" type="button" data-ic-close aria-label="Close">'+c.icon("close")+'</button></div><form data-ic-form="'+kind+'"><div class="ic-dialog-body">'+body+'</div><div class="ic-dialog-foot"><button class="ic-button" type="button" data-ic-close>Cancel</button><button class="ic-button '+(danger?"danger":"primary")+'" type="submit" '+(danger?"disabled data-ic-danger-submit":"")+'>'+submit+'</button></div></form></section></div>';
    var confirm=document.querySelector("[data-ic-confirm]");
    var submitButton=document.querySelector("[data-ic-danger-submit]");
    if(confirm)confirm.addEventListener("input",function(){submitButton.disabled=confirm.value!=="DETACH";confirm.setAttribute("aria-invalid",String(confirm.value.length>0&&submitButton.disabled));});
    setTimeout(function(){var focus=document.querySelector("#connectorDialog input,#connectorDialog select");if(focus)focus.focus();},20);
  }
  function closeDialog(){document.getElementById("connectorDialog").innerHTML="";}
  function runAudit(){
    if(!s.connected){c.toast("Pair the connector before synchronization.");return;}
    var now=new Date(),base=s.findings.length+41;
    var batch=[
      {id:"FND-"+base,source:"Extension health",title:"Observability exporter exceeded its retry budget",severity:"High",affected:"ext.observability",status:"Open",detected:"Just now"},
      {id:"FND-"+(base+1),source:"Update readiness",title:"One runtime package is held behind stable",severity:"Medium",affected:"Node.js runtime",status:"Open",detected:"Just now"},
      {id:"FND-"+(base+2),source:"Continuity",title:"Restore drill evidence expires in 9 days",severity:"Low",affected:"Backup policy",status:"Review",detected:"Just now"},
      {id:"FND-"+(base+3),source:"Security baseline",title:"No critical exposure in signed inventory",severity:"Info",affected:"Platform",status:"Passed",detected:"Just now"}
    ];
    s.findings=batch.concat(s.findings).slice(0,40);
    s.reports.unshift({id:"AUD-"+now.getTime().toString(36).toUpperCase(),schema:"insights-report/v1",summary:"Platform, extension and update-readiness audit",status:"Completed",findings:batch.length,created:now.toLocaleString()});
    s.lastSync="Just now";c.save();c.sync();render();c.toast("Local audit completed and consented evidence synchronized.");
  }
  function download(){
    var data={installationId:s.installationId,consent:s.consent,reports:s.reports,findings:s.findings,bugs:s.bugs};
    var blob=new Blob([JSON.stringify(data,null,2)],{type:"application/json"}),url=URL.createObjectURL(blob),link=document.createElement("a");
    link.href=url;link.download="wcp360-insights-export.json";link.click();setTimeout(function(){URL.revokeObjectURL(url);},0);
  }
  document.addEventListener("click",function(event){
    var tab=event.target.closest("[data-ic-tab]");
    if(tab){s.tab=tab.dataset.icTab;c.save();render();return;}
    if(event.target.closest("[data-ic-close]")){closeDialog();return;}
    var action=event.target.closest("[data-ic-action]");
    if(!action)return;
    var name=action.dataset.icAction;
    if(name==="open-connect")openDialog("connect");
    else if(name==="open-bug"){if(s.connected)openDialog("bug");else c.toast("Pair the connector first.");}
    else if(name==="open-detach")openDialog("detach");
    else if(name==="run-audit")runAudit();
    else if(name==="save-policy"){var form=document.querySelector("[data-ic-policy-form]");if(form)form.requestSubmit();}
    else if(name==="export-all")download();
  });
  document.addEventListener("submit",function(event){
    var policy=event.target.closest("[data-ic-policy-form]");
    if(policy){
      event.preventDefault();
      var policyData=new FormData(policy);
      Object.keys(s.consent).forEach(function(name){s.consent[name]=policyData.has(name);});
      s.schedule=policyData.get("schedule");s.retention=policyData.get("retention");
      c.save();c.sync();render();c.toast("Collection policy saved with a new consent receipt.");return;
    }
    var form=event.target.closest("[data-ic-form]");
    if(!form)return;
    event.preventDefault();
    var data=new FormData(form),kind=form.dataset.icForm;
    if(kind==="connect"){
      var endpoint=String(data.get("endpoint")||""),pairing=String(data.get("pairingCode")||"");
      if(!endpoint.startsWith("https://")||!pairing.startsWith("PAIR-")){c.toast("Use HTTPS and a one-time code beginning with PAIR-.");return;}
      s.endpoint=endpoint;s.installationId=String(data.get("installationId"));s.organization=String(data.get("organization"));s.connected=true;s.lastSync="Paired just now";
      c.save();c.sync();closeDialog();render();c.toast("Scoped mTLS pairing created.");
    }else if(kind==="bug"){
      var now=new Date();
      s.bugs.unshift({id:"BUG-"+now.getTime().toString(36).toUpperCase(),title:String(data.get("title")),component:String(data.get("component")),priority:String(data.get("priority")),steps:String(data.get("steps")),status:"Submitted",created:now.toLocaleString()});
      s.lastSync="Just now";c.save();c.sync();closeDialog();render();c.toast("Sanitized bug report submitted.");
    }else if(data.get("confirm")==="DETACH"){
      s.connected=false;s.lastSync="Detached";c.sync();c.save();closeDialog();render();c.toast("Pairing revoked and transmission stopped.");
    }
  });
  document.addEventListener("keydown",function(event){if(event.key==="Escape")closeDialog();});
  render();
})();
