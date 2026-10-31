(function(){
  "use strict";
  var key="wcp360-insights-connector-v1";
  var exchangeKey="wcp360.insights.exchange.v1";
  var defaults={
    tab:"overview",connected:false,
    endpoint:"https://insights.internal.example.com",
    installationId:"wcp-example-01",organization:"Example Hosting",
    schedule:"Daily at 03:00",retention:"30 days",lastSync:"Never",
    consent:{inventory:true,health:true,failures:true,security:false,bugContext:true},
    reports:[],findings:[],bugs:[]
  };
  function read(name){try{return JSON.parse(localStorage.getItem(name)||"null");}catch(error){return null;}}
  var saved=read(key)||{};
  var state=Object.assign({},defaults,saved,{
    consent:Object.assign({},defaults.consent,saved.consent||{}),
    reports:Array.isArray(saved.reports)?saved.reports:[],
    findings:Array.isArray(saved.findings)?saved.findings:[],
    bugs:Array.isArray(saved.bugs)?saved.bugs:[]
  });
  function esc(value){return String(value==null?"":value).replace(/[&<>"']/g,function(char){return {"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[char];});}
  function icon(name){return '<span class="material-symbols-rounded" aria-hidden="true">'+name+'</span>';}
  function status(label,type){return '<span class="ic-status '+(type||"neutral")+'">'+esc(label)+'</span>';}
  function button(label,action,style,symbol,disabled){return '<button class="ic-button '+(style||"")+'" type="button" data-ic-action="'+action+'" '+(disabled?"disabled":"")+'>'+(symbol?icon(symbol):"")+esc(label)+'</button>';}
  function head(eyebrow,title,copy,actions){return '<div class="ic-page-head"><div><div class="ic-eyebrow">'+eyebrow+'</div><h1>'+title+'</h1><p>'+copy+'</p></div><div class="ic-actions">'+(actions||"")+'</div></div>';}
  function metrics(items){return '<div class="ic-metrics">'+items.map(function(item){return '<article class="ic-metric"><div class="ic-metric-top"><span>'+esc(item.label)+'</span><span class="ic-metric-icon">'+icon(item.icon)+'</span></div><strong>'+esc(item.value)+'</strong><small>'+esc(item.meta)+'</small></article>';}).join("")+'</div>';}
  function panel(title,sub,body,extra,flush){return '<section class="ic-panel"><div class="ic-panel-head"><div><div class="ic-panel-title">'+title+'</div><div class="ic-panel-sub">'+sub+'</div></div>'+(extra||"")+'</div><div class="ic-panel-body '+(flush?"flush":"")+'">'+body+'</div></section>';}
  function table(headers,rows){return '<div class="ic-table-wrap"><table class="ic-table"><thead><tr>'+headers.map(function(item){return '<th>'+item+'</th>';}).join("")+'</tr></thead><tbody>'+rows.join("")+'</tbody></table></div>';}
  function rule(symbol,title,copy,label,type){return '<div class="ic-check"><span class="ic-check-icon '+(type==="danger"?"block":"")+'">'+icon(symbol)+'</span><div><strong>'+title+'</strong><small>'+copy+'</small></div>'+status(label,type||"success")+'</div>';}
  function save(){localStorage.setItem(key,JSON.stringify(state));}
  function toast(message){var node=document.createElement("div");node.className="ic-toast";node.textContent=message;document.getElementById("connectorToasts").appendChild(node);setTimeout(function(){node.remove();},4000);}
  function sync(){
    var exchange=read(exchangeKey)||{installations:[],reports:[],findings:[],bugs:[]};
    ["installations","reports","findings","bugs"].forEach(function(name){
      if(!Array.isArray(exchange[name]))exchange[name]=[];
      exchange[name]=exchange[name].filter(function(item){return item.installationId!==state.installationId;});
    });
    function stamp(items){return items.map(function(item){return Object.assign({},item,{installationId:state.installationId});});}
    if(state.connected){
      exchange.installations.unshift({installationId:state.installationId,organization:state.organization,endpoint:state.endpoint,consent:Object.keys(state.consent).filter(function(name){return state.consent[name];}),lastSync:state.lastSync,status:"Connected"});
      exchange.reports=stamp(state.reports).concat(exchange.reports);
      exchange.findings=stamp(state.findings).concat(exchange.findings);
      exchange.bugs=stamp(state.bugs).concat(exchange.bugs);
    }
    exchange.updatedAt=new Date().toISOString();
    localStorage.setItem(exchangeKey,JSON.stringify(exchange));
  }
  function severity(value){return value==="Critical"||value==="High"?"danger":value==="Medium"?"warning":"neutral";}
  function actions(){return state.connected?button("Run local audit","run-audit","primary","fact_check")+button("Report a bug","open-bug","","bug_report")+button("Detach","open-detach","danger","link_off"):button("Connect to INTERNAL","open-connect","primary","link");}
  window.WCPInsights={state:state,read:read,esc:esc,icon:icon,status:status,button:button,head:head,metrics:metrics,panel:panel,table:table,rule:rule,save:save,toast:toast,sync:sync,severity:severity,actions:actions};
})();
