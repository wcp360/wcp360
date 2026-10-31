(()=>{
  "use strict";
  if(window.WCPInternalAuthorized!==true)return;
  const storageKey="wcp360.internal.control-plane.v1";
  const defaultState={
    components:[
      {name:"WCP-USER",family:"Public core",repo:"wcp360/core",distribution:"Always public",owner:"Product",selected:true},
      {name:"WCP-ADMIN",family:"Public core",repo:"wcp360/core",distribution:"Always public",owner:"Platform",selected:true},
      {name:"API contracts",family:"Public core",repo:"wcp360/core",distribution:"Always public",owner:"Platform",selected:true},
      {name:"Node daemon",family:"Public core",repo:"wcp360/daemon",distribution:"Always public",owner:"Systems",selected:true},
      {name:"Installer and updater",family:"Public core",repo:"wcp360/installer",distribution:"Always public",owner:"Release",selected:true},
      {name:"DNS module",family:"Licensed module",repo:"wcp360/modules-dns",distribution:"Edition gated",owner:"DNS",selected:true},
      {name:"Mail module",family:"Licensed module",repo:"wcp360/modules-mail",distribution:"Edition gated",owner:"Mail",selected:true},
      {name:"Database module",family:"Licensed module",repo:"wcp360/modules-db",distribution:"Edition gated",owner:"Database",selected:true},
      {name:"Backup module",family:"Licensed module",repo:"wcp360/modules-backup",distribution:"Edition gated",owner:"Storage",selected:true},
      {name:"Monitoring module",family:"Licensed module",repo:"wcp360/modules-monitoring",distribution:"Edition gated",owner:"SRE",selected:true},
      {name:"Application client",family:"Public extension",repo:"wcp360/app-client",distribution:"Optional public",owner:"Ecosystem",selected:true},
      {name:"License verifier",family:"Public client",repo:"wcp360/license-client",distribution:"Always public",owner:"Commerce",selected:true},
      {name:"WCP360 Insights Connector",family:"Public extension",repo:"wcp360/insights-connector",distribution:"Separate signed artifact",owner:"Product Security",selected:false,detached:true},
      {name:"Marketplace publisher",family:"Internal service",repo:"internal/catalog-publisher",distribution:"Never distributed",owner:"Ecosystem",selected:false},
      {name:"License authority",family:"Internal service",repo:"internal/license-server",distribution:"Never distributed",owner:"Commerce",selected:false},
      {name:"Build Factory",family:"Internal service",repo:"internal/build-factory",distribution:"Never distributed",owner:"Release",selected:false},
      {name:"Fleet registry",family:"Internal service",repo:"internal/fleet",distribution:"Never distributed",owner:"SRE",selected:false},
      {name:"Customer administration",family:"Internal service",repo:"internal/customer-ops",distribution:"Never distributed",owner:"Operations",selected:false},
      {name:"Commercial analytics",family:"Internal service",repo:"internal/analytics",distribution:"Never distributed",owner:"Finance",selected:false}
    ],
    builds:[
      {id:"BLD-1294",commit:"8a71f0c",edition:"Business",version:"2.6.0-rc.2",result:"Passed",created:"29 Oct, 14:26"},
      {id:"BLD-1293",commit:"b24d91e",edition:"Community",version:"2.6.0-rc.2",result:"Passed",created:"29 Oct, 13:41"},
      {id:"BLD-1292",commit:"335b8d1",edition:"Enterprise",version:"2.6.0-rc.1",result:"Failed",created:"29 Oct, 11:08"},
      {id:"BLD-1291",commit:"957c2ab",edition:"Business",version:"2.5.9",result:"Passed",created:"28 Oct, 18:12"},
      {id:"BLD-1290",commit:"108fedd",edition:"Community",version:"2.5.9",result:"Passed",created:"28 Oct, 17:54"}
    ],
    releases:[
      {version:"2.5.9",channel:"Stable",status:"Published",coverage:"100%",date:"28 Oct 2026"},
      {version:"2.6.0-rc.2",channel:"RC",status:"Candidate",coverage:"99.4%",date:"29 Oct 2026"},
      {version:"2.6.0-beta.4",channel:"Beta",status:"Superseded",coverage:"98.8%",date:"24 Oct 2026"},
      {version:"2.7.0-alpha.1",channel:"Alpha",status:"Internal",coverage:"96.1%",date:"29 Oct 2026"}
    ],
    artifacts:[
      {name:"wcp360-community-2.5.9.zip",edition:"Community",size:"184 MB",sha:"f6c1...9b72",status:"Signed"},
      {name:"wcp360-business-2.5.9.zip",edition:"Business",size:"231 MB",sha:"71ae...2f08",status:"Signed"},
      {name:"wcp360-enterprise-2.5.9.zip",edition:"Enterprise",size:"268 MB",sha:"4d92...ad11",status:"Signed"},
      {name:"manifest-stable.json",edition:"All",size:"18 KB",sha:"e8d0...60cc",status:"Published"},
      {name:"sbom-2.5.9.cdx.json",edition:"All",size:"1.8 MB",sha:"a17c...8f32",status:"Signed"}
    ],
    detachedArtifacts:[
      {name:"wcp360-insights-connector-1.0.0.zip",extension:"com.wcp360.insights-connector",version:"1.0.0",compatibility:">=2.5.0 <3.0.0",size:"1.4 MB",sha:"9ab4...c821",evidence:"Manifest + SBOM + provenance",status:"Signed",coreIncluded:false}
    ],
    licenses:[
      {edition:"Community",active:"Open",limit:"Unlimited installs",policy:"Offline capable",status:"Active"},
      {edition:"Business",active:"1,842",limit:"3 activations",policy:"7 day grace",status:"Active"},
      {edition:"Enterprise",active:"318",limit:"Contract based",policy:"30 day grace",status:"Active"},
      {edition:"Trial",active:"227",limit:"1 activation / 14 days",policy:"Online required",status:"Active"}
    ],
    apps:[
      {name:"WordPress Toolkit",publisher:"WCP360",version:"4.2.1",compatibility:"2.5+",signature:"Verified",visibility:"Public"},
      {name:"Laravel Deploy",publisher:"WCP360",version:"2.8.0",compatibility:"2.4+",signature:"Verified",visibility:"Public"},
      {name:"Nextcloud",publisher:"Trusted partner",version:"31.0.4",compatibility:"2.5+",signature:"Verified",visibility:"Business+"},
      {name:"Magento Stack",publisher:"Trusted partner",version:"2.4.8",compatibility:"2.5+",signature:"Verified",visibility:"Enterprise"},
      {name:"Node Application",publisher:"WCP360",version:"3.1.2",compatibility:"2.3+",signature:"Verified",visibility:"Public"},
      {name:"Private telemetry",publisher:"WCP360 Internal",version:"1.9.0",compatibility:"Internal",signature:"Verified",visibility:"Never public"}
    ],
    extensions:[
      {id:"ext.backup-s3",name:"Object Storage Backup",publisher:"WCP360",version:"2.5.0",tier:"Business+",permissions:"backup.write, secrets.use",compatibility:"2.5+",stage:"Published"},
      {id:"ext.dns-providers",name:"DNS Provider Connectors",publisher:"WCP360",version:"1.4.2",tier:"Community",permissions:"dns.write, secrets.use",compatibility:"2.4+",stage:"Published"},
      {id:"ext.mailguard",name:"MailGuard Gateway",publisher:"Example Security",version:"5.1.4",tier:"Security add-on",permissions:"mail.route, events.read",compatibility:"2.5+",stage:"Published"},
      {id:"ext.observability",name:"OpenTelemetry Exporter",publisher:"WCP360 Labs",version:"2.2.3",tier:"Community",permissions:"metrics.read, network.out",compatibility:"2.3+",stage:"Published"},
      {id:"ext.billing-bridge",name:"Billing Bridge",publisher:"Example Billing",version:"2.0.0-rc.1",tier:"Business+",permissions:"accounts.read, usage.read",compatibility:"2.6+",stage:"Compatibility hold"},
      {id:"ext.identity-oidc",name:"OIDC Identity Connector",publisher:"WCP360",version:"0.9.0",tier:"Enterprise",permissions:"identity.map, secrets.use",compatibility:"2.6+",stage:"Security review"}
    ],
    customers:[
      {id:"ORG-1042",name:"Northstar Hosting",edition:"Enterprise",subscription:"Active",licenses:24,installations:18,renewal:"18 Dec 2026",contact:"ops@northstar.example"},
      {id:"ORG-1038",name:"Blue Orbit Cloud",edition:"Business",subscription:"Active",licenses:12,installations:9,renewal:"04 Jan 2027",contact:"admin@blueorbit.example"},
      {id:"ORG-1029",name:"Atelier Web",edition:"Business",subscription:"Trial",licenses:3,installations:2,renewal:"12 Nov 2026",contact:"tech@atelierweb.example"},
      {id:"ORG-1014",name:"Cedar Stack",edition:"Enterprise",subscription:"Active",licenses:40,installations:37,renewal:"29 Mar 2027",contact:"platform@cedarstack.example"},
      {id:"ORG-1007",name:"Pixel Harbor",edition:"Community",subscription:"Open",licenses:0,installations:4,renewal:"No renewal",contact:"hello@pixelharbor.example"},
      {id:"ORG-0998",name:"Vertex Managed",edition:"Business",subscription:"Past due",licenses:8,installations:8,renewal:"31 Oct 2026",contact:"finance@vertex.example"}
    ],
    operators:[
      {name:"Yann G.",email:"yann@example.com",role:"Release Manager",mfa:"FIDO2",access:"Standing low-risk",lastSeen:"Now"},
      {name:"Maya Chen",email:"maya@example.com",role:"Security Approver",mfa:"FIDO2",access:"JIT approver",lastSeen:"4 min ago"},
      {name:"Omar Diallo",email:"omar@example.com",role:"Support Engineer",mfa:"FIDO2",access:"No privilege",lastSeen:"18 min ago"},
      {name:"Sofia Martin",email:"sofia@example.com",role:"Commerce Operator",mfa:"FIDO2",access:"Customer records",lastSeen:"1 h ago"},
      {name:"Automation Release",email:"release-bot@internal",role:"Workload identity",mfa:"mTLS",access:"Build metadata only",lastSeen:"2 min ago"}
    ],
    approvals:[
      {id:"APR-2841",type:"Stable promotion",requester:"Yann G.",scope:"Release 2.6.0-rc.2",duration:"One operation",risk:"High",status:"Pending"},
      {id:"APR-2839",type:"JIT support",requester:"Omar Diallo",scope:"Fleet diagnostics / ORG-1038",duration:"30 min",risk:"Medium",status:"Pending"},
      {id:"APR-2836",type:"Artifact revocation",requester:"Maya Chen",scope:"wcp360-business-2.5.8.zip",duration:"One operation",risk:"Critical",status:"Approved"},
      {id:"APR-2831",type:"Signing-key access",requester:"Yann G.",scope:"HSM staging slot",duration:"15 min",risk:"High",status:"Expired"}
    ],
    sessions:[
      {id:"SES-A821",operator:"Yann G.",context:"Release Manager",device:"Managed Windows / FIDO2",started:"12 min ago",expires:"48 min",status:"Active"},
      {id:"SES-A817",operator:"Maya Chen",context:"Security Approver",device:"Managed macOS / FIDO2",started:"34 min ago",expires:"26 min",status:"Active"},
      {id:"SES-A799",operator:"Omar Diallo",context:"Support read-only",device:"Managed Linux / FIDO2",started:"2 h ago",expires:"Expired",status:"Closed"},
      {id:"SES-W144",operator:"Automation Release",context:"Build metadata",device:"Workload identity / mTLS",started:"2 min ago",expires:"8 min",status:"Active"}
    ],
    fleet:[
      {node:"wcp-eu-1042",version:"2.5.9",channel:"Stable",consent:"Diagnostics",seen:"2 min ago",health:"Healthy"},
      {node:"wcp-us-0811",version:"2.5.8",channel:"Stable",consent:"Updates only",seen:"8 min ago",health:"Update due"},
      {node:"wcp-ca-2290",version:"2.6.0-rc.2",channel:"RC",consent:"Diagnostics",seen:"12 min ago",health:"Healthy"},
      {node:"wcp-de-3304",version:"2.5.9",channel:"Stable",consent:"None",seen:"1 hour ago",health:"Private"},
      {node:"wcp-fr-0088",version:"2.5.7",channel:"Stable",consent:"Diagnostics",seen:"2 hours ago",health:"Attention"}
    ],
    activity:[
      {icon:"verified",title:"Stable manifest signed",copy:"2.5.9 promoted by release-manager@example.com",time:"14 min"},
      {icon:"shield",title:"Exclusion audit passed",copy:"No private repository, token or internal hostname detected.",time:"19 min"},
      {icon:"deployed_code",title:"Business artifact built",copy:"BLD-1294 reproduced from commit 8a71f0c.",time:"28 min"},
      {icon:"key",title:"Signing key rotation rehearsed",copy:"HSM key slot 03 verified in staging.",time:"2 h"}
    ],
    revoked:[],
    manifestPublished:false,
    detachedManifestPublished:false
  };
  const load=()=>{
    try{
      const saved=JSON.parse(localStorage.getItem(storageKey)||"null");
      return saved?{...defaultState,...saved}:structuredClone(defaultState);
    }catch(error){return JSON.parse(JSON.stringify(defaultState));}
  };
  let state=load();
  if(!Array.isArray(state.detachedArtifacts))state.detachedArtifacts=structuredClone(defaultState.detachedArtifacts);
  if(typeof state.detachedManifestPublished!=="boolean")state.detachedManifestPublished=false;
  const detachedComponent=defaultState.components.find(component=>component.detached);
  if(detachedComponent&&!state.components.some(component=>component.repo===detachedComponent.repo))state.components.splice(12,0,structuredClone(detachedComponent));
  const save=()=>localStorage.setItem(storageKey,JSON.stringify(state));
  const escapeHtml=value=>String(value).replace(/[&<>"']/g,char=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[char]));
  const icon=name=>`<span class="material-symbols-rounded" aria-hidden="true">${name}</span>`;
  const status=(label,type="")=>`<span class="internal-status ${type}">${escapeHtml(label)}</span>`;
  const button=(label,action,style="",symbol="")=>`<button class="internal-btn ${style}" type="button" data-action="${action}">${symbol?icon(symbol):""}${escapeHtml(label)}</button>`;
  const navItems=[
    {id:"overview",label:"Overview",sub:"Control plane health",icon:"space_dashboard"},
    {id:"boundary",label:"Public boundary",sub:"Allowlist and contracts",icon:"safety_check"},
    {id:"builds",label:"Build Factory",sub:"Reproducible packages",icon:"precision_manufacturing"},
    {id:"releases",label:"Releases",sub:"Channels and promotion",icon:"rocket_launch"},
    {id:"distribution",label:"Distribution",sub:"Downloads and updates",icon:"cloud_download"},
    {id:"licensing",label:"Licensing",sub:"Editions and activation",icon:"license"},
    {id:"marketplace",label:"Marketplace",sub:"Signed application catalog",icon:"storefront"},
    {id:"extensions",label:"Extensions",sub:"Package review and publishing",icon:"extension"},
    {id:"customers",label:"Customers & Commerce",sub:"Organizations and subscriptions",icon:"corporate_fare"},
    {id:"fleet",label:"Fleet",sub:"Consented installations",icon:"dns"},
    {id:"security",label:"Security",sub:"Supply chain controls",icon:"security"}
  ];
  const titles={
    overview:["Internal Control Plane","Private governance for builds, commerce and distribution."],
    boundary:["Public Distribution Boundary","A positive allowlist is the only path from private sources to a public artifact."],
    builds:["Build Factory","Create deterministic artifacts from an approved commit and locked dependencies."],
    releases:["Release Manager","Promote tested artifacts through Alpha, Beta, RC and Stable channels."],
    distribution:["Download and Update Center","Publish signed packages and atomic update manifests with recovery metadata."],
    licensing:["Licensing Center","Operate the private authority while public installations receive only the verifier client."],
    marketplace:["Marketplace Publisher","Review, sign and publish applications to the separate public catalog client."],
    extensions:["Extension Publisher","Review public extension packages, permissions, compatibility and release evidence."],
    customers:["Customers and Commerce","Manage private organizations and subscriptions without accessing hosted content."],
    fleet:["Fleet Registry","Observe registered installations using explicit consent and minimum necessary data."],
    security:["Release Security Center","Protect signing, provenance, dependencies and compromised-package response."]
  };
  const currentTab=()=>{const id=location.hash.slice(1);return navItems.some(item=>item.id===id)?id:"overview";};
  const renderNav=()=>{
    const active=currentTab();
    document.getElementById("internalNav").innerHTML='<div class="internal-nav-kicker">Private vendor plane</div>'+navItems.map(function(item){return '<a class="wcp-nav-item '+(item.id===active?"active":"")+'" href="internal-distribution.html#'+item.id+'" '+(item.id===active?'aria-current="page"':'')+'>'+icon(item.icon)+'<span class="internal-nav-copy"><span class="internal-nav-title">'+escapeHtml(item.label)+'</span><span class="internal-nav-sub">'+escapeHtml(item.sub)+'</span></span></a>';}).join("");
    document.getElementById("internalSidebarFooter").innerHTML='<div class="internal-sidebar-card"><strong>Physically isolated</strong><p>Private services, history and secrets never enter a public repository or ZIP.</p><a href="../WCP360 Design System/software-manager.html">Open public WCP-ADMIN</a></div>';
  };
  const renderHeader=()=>{
    const info=titles[currentTab()];
    const identity=window.WCPInternalIdentity||{};
    const operator=(identity.email||"internal operator").split("@")[0];
    document.getElementById("internalHeader").innerHTML='<div class="wcp-header-left"><button class="wcp-menu-btn internal-mobile-menu" type="button" data-internal-menu aria-label="Open navigation">'+icon("menu")+'</button><div class="internal-header-copy"><h1>'+escapeHtml(info[0])+'</h1><p>'+escapeHtml(info[1])+'</p></div></div><div class="internal-header-actions"><span class="internal-environment">Production private</span><span class="internal-operator-chip">'+icon("passkey")+'<span>'+escapeHtml(operator)+'</span></span>'+button("New build","create-build","primary","add")+'<button class="internal-icon-btn" type="button" data-internal-logout aria-label="Sign out of WCP360 INTERNAL">'+icon("logout")+'</button></div>';
  };
  const pageHead=(eyebrow,title,copy,actions="")=>`<div class="internal-page-head"><div><div class="internal-eyebrow">${eyebrow}</div><h2>${title}</h2><p>${copy}</p></div><div class="internal-actions">${actions}</div></div>`;
  const panel=(title,sub,body,extra="",classes="")=>`<section class="internal-panel ${classes}"><div class="internal-panel-head"><div><div class="internal-panel-title">${title}</div><div class="internal-panel-sub">${sub}</div></div>${extra}</div><div class="internal-panel-body ${classes.includes("flush")?"flush":""}">${body}</div></section>`;
  const metrics=items=>`<div class="internal-metrics">${items.map(item=>`<article class="internal-metric"><div class="internal-metric-top"><span>${item.label}</span><span class="internal-metric-icon">${icon(item.icon)}</span></div><div class="internal-metric-value">${item.value}</div><div class="internal-metric-meta">${item.meta}</div></article>`).join("")}</div>`;
  const table=(headers,rows)=>`<div class="internal-table-wrap"><table class="internal-table"><thead><tr>${headers.map(header=>`<th>${header}</th>`).join("")}</tr></thead><tbody>${rows.join("")}</tbody></table></div>`;
  const addActivity=(iconName,title,copy)=>{
    state.activity.unshift({icon:iconName,title,copy,time:"now"});
    state.activity=state.activity.slice(0,8);
  };
  const activityFeed=()=>`<div class="internal-feed">${state.activity.slice(0,5).map(item=>`<div class="internal-feed-item"><span class="internal-feed-icon">${icon(item.icon)}</span><div><strong>${escapeHtml(item.title)}</strong><p>${escapeHtml(item.copy)}</p></div><time>${escapeHtml(item.time)}</time></div>`).join("")}</div>`;
  const securityChecks=[
    ["Public allowlist enforced","Builds accept only versioned component IDs approved for an edition."],
    ["Reproducible build verified","A second isolated worker must produce the same SHA-256."],
    ["SBOM generated","CycloneDX inventory is stored beside every release artifact."],
    ["Secrets scan passed","Tokens, credentials, private domains and internal endpoints are rejected."],
    ["Provenance attested","Commit, runner image, dependency lock and builder identity are signed."],
    ["Malware scan passed","Artifacts and embedded extensions are scanned before promotion."],
    ["HSM signing online","Release keys cannot be exported and require dual control."],
    ["Rollback tested","The updater validates a recovery path before Stable publication."]
  ];
  const checklist=()=>`<div class="internal-checklist">${securityChecks.map((item,index)=>`<div class="internal-check" data-security-control><span class="internal-check-icon">${icon("check")}</span><div><strong>${item[0]}</strong><p>${item[1]}</p></div>${status(index===7?"Ready":"Passed",index===7?"info":"success")}</div>`).join("")}</div>`;
  const renderOverview=()=>{
    const passed=state.builds.filter(build=>build.result==="Passed").length;
    const publicCount=state.components.filter(component=>component.selected).length;
    const pipeline=["Select","Test","Build","Audit","Sign","Publish"];
    const recentBuildRows=state.builds.slice(0,4).map(build=>`<tr data-build-row><td><strong>${build.id}</strong><br><span class="internal-mono">${build.commit}</span></td><td>${build.version}</td><td>${build.edition}</td><td>${status(build.result,build.result==="Passed"?"success":"danger")}</td><td>${build.created}</td></tr>`);
    return `<div class="internal-page">${pageHead("WCP360 / Internal","Private vendor operations without public-code contamination","This control plane operates licensing, catalog publication, builds, releases, downloads, updates and fleet metadata. It is a separate product boundary, not Linux root and not the technical WCP-ADMIN shipped to customers.",button("Review boundary","go-boundary","","safety_check")+button("Create build","create-build","primary","add"))}
      ${metrics([
        {label:"Stable release",value:"2.5.9",meta:"Signed and published",icon:"verified"},
        {label:"Public allowlist",value:publicCount,meta:"Versioned components selected",icon:"fact_check"},
        {label:"Build pass rate",value:Math.round(passed/state.builds.length*100)+"%",meta:"Latest five build jobs",icon:"precision_manufacturing"},
        {label:"Registered fleet",value:state.fleet.length.toLocaleString(),meta:"Demo installations with consent",icon:"dns"}
      ])}
      ${panel("Signed publication pipeline","Every gate produces evidence. Stable publication is blocked if a gate is missing.",`<div class="internal-pipeline">${pipeline.map((step,index)=>`<div class="internal-pipeline-step"><span>0${index+1}</span><strong>${step}</strong><small>${["Positive allowlist","Tests and policy","Hermetic runner","SBOM and scans","HSM signature","Atomic manifest"][index]}</small></div>`).join("")}</div>`,status("All gates healthy","success"))}
      <div class="internal-grid">
        ${panel("Recent build jobs","Deterministic packages generated from exact commits.",table(["Build","Version","Edition","Result","Created"],recentBuildRows),"","flush")}
        ${panel("Operator activity","Immutable administrative events for the private control plane.",activityFeed(),status("WORM copy","success"))}
      </div>
      <div class="internal-callout warning">${icon("domain_disabled")}<div><strong>No navigation path from public WCP-ADMIN</strong><p>The public product has license, catalog and update clients only. It cannot discover this console, private repositories, signing infrastructure or commercial customer records.</p></div></div>
    </div>`;
  };
  const boundaryItem=component=>`<div class="internal-boundary-item" data-boundary-component data-family="${escapeHtml(component.family)}">${icon(component.selected?"deployed_code":"lock")}<div><strong>${escapeHtml(component.name)}</strong><small>${escapeHtml(component.distribution)} / ${escapeHtml(component.repo)}</small></div></div>`;
  const renderBoundary=()=>{
    const publicComponents=state.components.filter(component=>component.selected);
    const privateComponents=state.components.filter(component=>!component.selected);
    const componentRows=state.components.map((component,index)=>`<tr data-component-row><td><strong>${escapeHtml(component.name)}</strong><br><span class="internal-mono">${escapeHtml(component.repo)}</span></td><td>${escapeHtml(component.family)}</td><td>${escapeHtml(component.owner)}</td><td>${status(component.distribution,component.selected?"info":"warning")}</td><td>${component.selected?status("Allowlisted","success"):status("Private","danger")}</td><td><div class="internal-row-actions"><button class="internal-icon-btn" type="button" data-action="toggle-component" data-index="${index}" aria-label="${component.selected?"Remove from":"Add to"} public allowlist">${icon(component.selected?"remove_circle":"add_circle")}</button></div></td></tr>`);
    return `<div class="internal-page">${pageHead("Architecture / Trust boundary","What can cross into a customer installation","The release builder receives immutable public source archives through an explicit component manifest. It has no checkout access to private repositories and no default include behavior.",button("Export allowlist","export-allowlist","","download"))}
      <div class="internal-boundary">
        <section class="internal-boundary-side private"><div class="internal-boundary-label">${icon("lock")} PRIVATE INTERNAL</div><div class="internal-boundary-list">${privateComponents.map(boundaryItem).join("")}</div></section>
        <div class="internal-boundary-gate"><span class="internal-gate-lock" title="One-way signed publication gate">${icon("encrypted")}</span></div>
        <section class="internal-boundary-side public"><div class="internal-boundary-label">${icon("public")} STABLE PUBLIC</div><div class="internal-boundary-list">${publicComponents.map(boundaryItem).join("")}</div></section>
      </div>
      <div class="internal-callout">${icon("account_tree")}<div><strong>Split-service contract</strong><p>The private license authority exposes a minimal activation API to the public verifier. The private marketplace publisher exports a signed catalog consumed by the public application client. Neither public client contains private business logic.</p></div></div>
      ${panel("Component classification registry","One distribution classification and one accountable owner per component.",
        `<div class="internal-filterbar"><div class="internal-search">${icon("search")}<input id="componentSearch" type="search" placeholder="Search component, repository or owner"></div><select class="internal-select" id="componentFamily" aria-label="Filter component family"><option value="">All families</option><option>Public core</option><option>Licensed module</option><option>Public extension</option><option>Public client</option><option>Internal service</option></select></div>${table(["Component","Family","Owner","Distribution","Boundary",""],componentRows)}`,"","flush")}
      <div class="internal-grid equal">
        ${panel("Public package allowlist","Generated from release policy, never from a directory exclusion list.",`<div class="internal-code">schema: wcp360.release/v1
edition: business
include:
  - core@8a71f0c
  - daemon@2.5.9
  - installer@2.5.9
  - modules/dns@4.3.1
deny_unknown_components: true
private_repository_access: false</div>`)}
        ${panel("Mandatory repository split","Private source and its history are not present in the public monorepo.",`<div class="internal-checklist">
          <div class="internal-check"><span class="internal-check-icon">${icon("check")}</span><div><strong>wcp360/public</strong><p>Core, modules, public extensions, installer, packaging contracts and tests.</p></div>${status("Distributable","success")}</div>
          <div class="internal-check"><span class="internal-check-icon">${icon("check")}</span><div><strong>wcp360-internal/private</strong><p>Licensing authority, catalog publisher, build control, fleet and commercial operations.</p></div>${status("Restricted","warning")}</div>
        </div>`)}
      </div>
    </div>`;
  };
  const renderBuilds=()=>{
    const rows=state.builds.map(build=>{
      const lane=build.lane||"Public core";
      const target=build.target||build.edition;
      return `<tr data-build-row><td><strong>${build.id}</strong><br><span class="internal-mono">${build.commit}</span></td><td>${status(lane,lane==="Detached extension"?"warning":"info")}</td><td>${build.version}</td><td>${escapeHtml(target)}</td><td>${status(build.result,build.result==="Passed"?"success":"danger")}</td><td>${build.created}</td><td><div class="internal-row-actions"><button class="internal-icon-btn" type="button" aria-label="View build ${build.id}">${icon("open_in_new")}</button></div></td></tr>`;
    });
    return `<div class="internal-page">${pageHead("Delivery / Build Factory","Hermetic and reproducible builds","A job pins the Git commit, runner image, dependency lock, edition policy and component allowlist. The output includes package, checksums, SBOM, provenance and audit evidence.",button("Create build","create-build","primary","add"))}
      ${metrics([
        {label:"Queued",value:"2",meta:"Across isolated workers",icon:"pending_actions"},
        {label:"Median duration",value:"11m 42s",meta:"Last 30 successful jobs",icon:"timer"},
        {label:"Reproducibility",value:"100%",meta:"Matching secondary hashes",icon:"fingerprint"},
        {label:"Detached packages",value:state.detachedArtifacts.length,meta:"Never included in core ZIP",icon:"extension_off"}
      ])}
      <div class="internal-grid equal">
        ${panel("Public product lane","Builds the installable WCP360 editions from the positive public allowlist.",`<div class="internal-checklist">
          <div class="internal-check"><span class="internal-check-icon">${icon("inventory_2")}</span><div><strong>Core archive</strong><p>WCP-USER, WCP-ADMIN, API, daemon, installer and edition modules.</p></div>${status("ZIP","info")}</div>
          <div class="internal-check"><span class="internal-check-icon">${icon("verified_user")}</span><div><strong>Hard exclusion</strong><p>INTERNAL services and detached extensions are rejected before source checkout.</p></div>${status("Enforced","success")}</div>
        </div>`)}
        ${panel("Detached extension lane","Builds optional packages independently, with their own compatibility and revocation lifecycle.",`<div class="internal-checklist">
          <div class="internal-check"><span class="internal-check-icon">${icon("extension")}</span><div><strong>Independent artifact</strong><p>WCP360 Insights Connector is signed and published outside every edition ZIP.</p></div>${status("Detached","warning")}</div>
          <div class="internal-check"><span class="internal-check-icon">${icon("rule")}</span><div><strong>Compatibility contract</strong><p>Manifest declares supported core versions, capabilities and upgrade rules.</p></div>${status("Required","success")}</div>
        </div>`)}
      </div>
      ${panel("Build jobs","Core and detached lanes use separate inputs, workers and output registries.",table(["Build","Lane","Version","Target","Result","Created",""],rows),status("Workers healthy","success"),"flush")}
      <div class="internal-grid equal">
        ${panel("Job inputs","All inputs are immutable before execution.",`<div class="internal-checklist">
          ${[["Source commit","Signed commit or protected release tag"],["Dependency graph","Lockfiles and package mirrors pinned by digest"],["Component manifest","Positive allowlist resolved by edition"],["Runner image","Ephemeral worker image verified by signature"]].map(item=>`<div class="internal-check"><span class="internal-check-icon">${icon("lock")}</span><div><strong>${item[0]}</strong><p>${item[1]}</p></div>${status("Pinned","success")}</div>`).join("")}
        </div>`)}
        ${panel("Outputs and evidence","A package cannot exist without its verification material.",`<div class="internal-checklist">
          ${[["Lane-specific package","Core edition ZIP or standalone extension, never both"],["SHA-256 checksums","Machine and operator verification"],["CycloneDX SBOM","Components, licenses and dependencies"],["SLSA-style provenance","Builder identity, inputs and timestamps"]].map(item=>`<div class="internal-check"><span class="internal-check-icon">${icon("description")}</span><div><strong>${item[0]}</strong><p>${item[1]}</p></div>${status("Generated","info")}</div>`).join("")}
        </div>`)}
      </div>
    </div>`;
  };
  const renderReleases=()=>{
    const rows=state.releases.map(release=>`<tr data-release-row><td><strong>${release.version}</strong></td><td>${status(release.channel,release.channel==="Stable"?"success":release.channel==="RC"?"info":"")}</td><td>${release.status}</td><td>${release.coverage}</td><td>${release.date}</td><td><div class="internal-row-actions"><button class="internal-icon-btn" type="button" aria-label="Open release ${release.version}">${icon("open_in_new")}</button></div></td></tr>`);
    return `<div class="internal-page">${pageHead("Delivery / Releases","Controlled channel promotion","Promotion references an already verified artifact; it never rebuilds code. Stable requires dual approval, complete evidence, rollback rehearsal and signed release notes.",button("Promote release","promote-release","primary","upgrade"))}
      ${panel("Release train","Alpha remains private. Beta and RC may be opt-in. Stable is the only default customer channel.",table(["Version","Channel","Status","Test coverage","Date",""],rows),status("2.5.9 Stable","success"),"flush")}
      <div class="internal-grid equal">
        ${panel("Stable promotion policy","All requirements are blocking and recorded.",`<div class="internal-checklist">${[["Artifact identity","Build hash and SBOM match the candidate"],["Quality gates","Unit, integration, migration and recovery suites pass"],["Security approval","No unaccepted critical or high finding"],["Dual control","Release manager and security approver are distinct"]].map(item=>`<div class="internal-check"><span class="internal-check-icon">${icon("check")}</span><div><strong>${item[0]}</strong><p>${item[1]}</p></div>${status("Required","success")}</div>`).join("")}</div>`)}
        ${panel("Channel behavior","Customer choice is bounded by root policy and edition.",`<div class="internal-checklist">${[["Stable","Default production channel with gradual rollout"],["RC","Explicit opt-in for final compatibility testing"],["Beta","Non-production preview with telemetry consent"],["Alpha","Internal fleet only; never exported publicly"]].map((item,index)=>`<div class="internal-check"><span class="internal-check-icon">${icon(index?"science":"verified")}</span><div><strong>${item[0]}</strong><p>${item[1]}</p></div>${status(index===3?"Private":"Defined",index===3?"warning":"info")}</div>`).join("")}</div>`)}
      </div>
    </div>`;
  };
  const renderDistribution=()=>{
    const rows=state.artifacts.map((artifact,index)=>`<tr data-artifact-row><td><strong>${escapeHtml(artifact.name)}</strong></td><td>${artifact.edition}</td><td>${artifact.size}</td><td class="internal-mono">${artifact.sha}</td><td>${status(state.revoked.includes(artifact.name)?"Revoked":artifact.status,state.revoked.includes(artifact.name)?"danger":"success")}</td><td><div class="internal-row-actions"><button class="internal-icon-btn" type="button" data-action="revoke-artifact" data-index="${index}" aria-label="Revoke ${escapeHtml(artifact.name)}">${icon("block")}</button><button class="internal-icon-btn" type="button" aria-label="Download ${escapeHtml(artifact.name)}">${icon("download")}</button></div></td></tr>`);
    const detachedRows=state.detachedArtifacts.map((artifact,index)=>`<tr data-detached-artifact-row><td><strong>${escapeHtml(artifact.name)}</strong><br><span class="internal-mono">${escapeHtml(artifact.extension)}</span></td><td>${escapeHtml(artifact.compatibility)}</td><td>${artifact.size}</td><td class="internal-mono">${artifact.sha}</td><td>${escapeHtml(artifact.evidence)}</td><td>${status(state.revoked.includes(artifact.name)?"Revoked":artifact.status,state.revoked.includes(artifact.name)?"danger":"success")}</td><td><div class="internal-row-actions"><button class="internal-icon-btn" type="button" data-action="revoke-detached-artifact" data-index="${index}" aria-label="Revoke ${escapeHtml(artifact.name)}">${icon("block")}</button><button class="internal-icon-btn" type="button" aria-label="Download ${escapeHtml(artifact.name)}">${icon("download")}</button></div></td></tr>`);
    const headActions=button(state.manifestPublished?"Core manifest published":"Publish core manifest","publish-manifest",state.manifestPublished?"":"primary","publish")+button(state.detachedManifestPublished?"Extension index published":"Publish extension index","publish-extension-index",state.detachedManifestPublished?"":"primary","extension");
    return `<div class="internal-page">${pageHead("Delivery / Distribution","Independent signed delivery channels","Core editions and optional extensions have different manifests, compatibility rules, rollout controls and revocation records. Neither channel can silently absorb the other.",headActions)}
      ${metrics([
        {label:"Core artifacts",value:state.artifacts.length,meta:"Edition ZIP, manifest and SBOM",icon:"inventory_2"},
        {label:"Detached artifacts",value:state.detachedArtifacts.length,meta:"Independent install and update",icon:"extension"},
        {label:"Signing key",value:"HSM-03",meta:"Ed25519 / dual control",icon:"key"},
        {label:"Rollback window",value:"30 days",meta:"Core and extensions retained",icon:"restore"}
      ])}
      ${panel("Core public channel","Only the positive public allowlist can produce these edition packages.",table(["Artifact","Edition","Size","SHA-256","Status",""],rows),status("Core origin healthy","success"),"flush")}
      ${panel("Detached extension channel","Each extension ships as a separately signed package with its own manifest, SBOM, provenance, compatibility range and deny record.",`<div class="internal-callout warning">${icon("extension_off")}<div><strong>Not embedded in WCP360 core</strong><p>The build and distribution services reject this extension from Community, Business and Enterprise ZIP files. ROOT installs it only by explicit package selection.</p></div></div>${table(["Artifact","Compatible core","Size","SHA-256","Evidence","Status",""],detachedRows)}`,status(state.detachedManifestPublished?"Index published":"Index draft",state.detachedManifestPublished?"success":"warning"))}
      <div class="internal-grid equal">
        ${panel("Update manifest contract","The public updater verifies signature and compatibility before download.",`<div class="internal-code">{
  "schema": "wcp360.update/v1",
  "channel": "stable",
  "version": "2.5.9",
  "artifact_sha256": "f6c1...9b72",
  "min_version": "2.4.0",
  "preflight": ["disk", "database", "services"],
  "rollback": "2.5.8",
  "signature": "ed25519:..."
}</div>`)}
        ${panel("Failure-safe updater","Core service is available even when the update service is unavailable.",`<div class="internal-checklist">${[["Preflight","Disk, database, service and extension compatibility"],["Transaction","Stage, verify, switch and run migrations atomically"],["Health gate","Check daemon, API and panel after the switch"],["Recovery","Automatically return to last known good version"]].map(item=>`<div class="internal-check"><span class="internal-check-icon">${icon("shield")}</span><div><strong>${item[0]}</strong><p>${item[1]}</p></div>${status("Built in","success")}</div>`).join("")}</div>`)}
      </div>
    </div>`;
  };
  const renderLicensing=()=>{
    const rows=state.licenses.map(license=>`<tr data-license-row><td><strong>${license.edition}</strong></td><td>${license.active}</td><td>${license.limit}</td><td>${license.policy}</td><td>${status(license.status,"success")}</td><td><div class="internal-row-actions"><button class="internal-icon-btn" type="button" aria-label="Configure ${license.edition} license policy">${icon("settings")}</button></div></td></tr>`);
    return `<div class="internal-page">${pageHead("Commerce / Licensing","Private authority, minimal public verifier","Commercial rules, customer identities, keys, renewals and revocations stay in the private service. A customer server receives a verifier library, a signed entitlement and a documented offline grace policy.",button("Issue license","issue-license","primary","add_card"))}
      ${panel("Edition policies","Activation counts represent the private authority, not data embedded in the public product.",table(["Edition","Active","Activation limit","Continuity policy","Status",""],rows),status("Authority online","success"),"flush")}
      <div class="internal-grid equal">
        ${panel("Private license authority","Strictly INTERNAL and never packaged.",`<div class="internal-checklist">${[["Customer and subscription records","Billing references, renewals, suspension and revocation"],["Activation ledger","Installation fingerprint, consent and activation history"],["Entitlement engine","Edition, modules, limits, trial and contract overrides"],["Signing authority","Short signed capability document with key rotation"]].map(item=>`<div class="internal-check"><span class="internal-check-icon">${icon("lock")}</span><div><strong>${item[0]}</strong><p>${item[1]}</p></div>${status("Private","warning")}</div>`).join("")}</div>`)}
        ${panel("Public license client","Small, auditable component shipped with WCP360.",`<div class="internal-checklist">${[["Signature verification","Trusts pinned rotating public keys, never a shared secret"],["Grace continuity","Hosting continues during a bounded authority outage"],["Cached entitlement","Stores minimum capability data with expiry"],["Privacy floor","No account content, domains, email or database data sent"]].map(item=>`<div class="internal-check"><span class="internal-check-icon">${icon("verified_user")}</span><div><strong>${item[0]}</strong><p>${item[1]}</p></div>${status("Public","info")}</div>`).join("")}</div>`)}
      </div>
      <div class="internal-callout warning">${icon("gpp_maybe")}<div><strong>License failure is never a destructive event</strong><p>Expired or unreachable licensing enters a visible grace state, blocks paid-feature expansion after policy expiry, preserves customer data and never deletes accounts or disables recovery access.</p></div></div>
    </div>`;
  };
  const renderMarketplace=()=>{
    const rows=state.apps.map(app=>`<tr data-app-row><td><strong>${escapeHtml(app.name)}</strong><br><span class="internal-mono">${escapeHtml(app.version)}</span></td><td>${escapeHtml(app.publisher)}</td><td>${escapeHtml(app.compatibility)}</td><td>${status(app.signature,app.signature==="Verified"?"success":"warning")}</td><td>${status(app.visibility,app.visibility==="Never public"?"danger":"info")}</td><td><div class="internal-row-actions"><button class="internal-icon-btn" type="button" aria-label="Review ${escapeHtml(app.name)}">${icon("rate_review")}</button></div></td></tr>`);
    return `<div class="internal-page">${pageHead("Ecosystem / Marketplace","Signed catalog publication","The private publisher ingests packages, runs policy and malware checks, validates compatibility and signs catalog records. The public client only discovers and installs entries allowed by ROOT policy, edition and account capabilities.",button("Submit application","submit-application","primary","add_box"))}
      ${metrics([
        {label:"Catalog entries",value:state.apps.filter(app=>app.visibility!=="Never public").length,meta:"Eligible for publication",icon:"apps"},
        {label:"Pending review",value:"3",meta:"Security and compatibility",icon:"rate_review"},
        {label:"Publishers",value:"8",meta:"Verified organizations",icon:"domain"},
        {label:"Signature policy",value:"100%",meta:"Unsigned packages rejected",icon:"verified"}
      ])}
      ${panel("Application catalog","Visibility is explicit; INTERNAL entries cannot be exported by the publisher.",table(["Application","Publisher","Compatibility","Signature","Visibility",""],rows),status("Catalog signed","success"),"flush")}
      <div class="internal-grid equal">
        ${panel("Private publisher workflow","The review pipeline is a private vendor capability.",`<div class="internal-pipeline" style="grid-template-columns:repeat(4,minmax(110px,1fr))">${[["01","Ingest","Source and manifest"],["02","Analyze","Malware, SBOM, license"],["03","Validate","Install, update, remove"],["04","Sign","Catalog and package"]].map(item=>`<div class="internal-pipeline-step"><span>${item[0]}</span><strong>${item[1]}</strong><small>${item[2]}</small></div>`).join("")}</div>`)}
        ${panel("Public catalog client","This remains functional when the marketplace is offline.",`<div class="internal-checklist">${[["Cached catalog","Previously verified records remain visible"],["ROOT policy","Admin enables providers, applications and editions"],["USER scope","User installs only inside owned hosting accounts"],["Isolation","Failed application extension cannot break the panel core"]].map(item=>`<div class="internal-check"><span class="internal-check-icon">${icon("check")}</span><div><strong>${item[0]}</strong><p>${item[1]}</p></div>${status("Contract","info")}</div>`).join("")}</div>`)}
      </div>
    </div>`;
  };
  const renderExtensions=()=>{
    const published=state.extensions.filter(extension=>extension.stage==="Published").length;
    const rows=state.extensions.map((extension,index)=>'<tr data-extension-row><td><strong>'+escapeHtml(extension.name)+'</strong><br><span class="internal-mono">'+escapeHtml(extension.id)+' @ '+escapeHtml(extension.version)+'</span></td><td>'+escapeHtml(extension.publisher)+'</td><td>'+escapeHtml(extension.tier)+'</td><td>'+escapeHtml(extension.compatibility)+'</td><td><span class="internal-mono">'+escapeHtml(extension.permissions)+'</span></td><td>'+status(extension.stage,extension.stage==="Published"?"success":extension.stage.includes("hold")?"danger":"warning")+'</td><td><div class="internal-row-actions"><button class="internal-icon-btn" type="button" data-action="review-extension" data-index="'+index+'" aria-label="Review '+escapeHtml(extension.name)+'">'+icon("rate_review")+'</button></div></td></tr>');
    const privateChecks=[["Publisher identity","Organization verification, signing identity and incident contact"],["Permission diff","New filesystem, network, secret, account and job capabilities"],["Conformance kit","Install, upgrade, timeout, crash, disable, rollback and uninstall"],["Release evidence","Package digest, SBOM, provenance, compatibility and support window"]].map(item=>'<div class="internal-check"><span class="internal-check-icon">'+icon("policy")+'</span><div><strong>'+item[0]+'</strong><p>'+item[1]+'</p></div>'+status("Blocking","warning")+'</div>').join("");
    const rootChecks=[["Local trust policy","Choose allowed publishers, editions and release channels"],["Permission approval","Accept the exact manifest before installation"],["Staged activation","Canary, health checks and reversible slot switch"],["Failure isolation","Withdraw extension routes while the hosting core remains healthy"]].map(item=>'<div class="internal-check"><span class="internal-check-icon">'+icon("admin_panel_settings")+'</span><div><strong>'+item[0]+'</strong><p>'+item[1]+'</p></div>'+status("Public WCP-ADMIN","info")+'</div>').join("");
    return '<div class="internal-page">'+pageHead("Ecosystem / Extensions","Public extension publishing","This registry owns package ingestion, publisher verification, permission review, compatibility evidence and signed publication. Customer WCP-ADMIN owns only local installation, enablement and rollback.",button("Submit extension","submit-extension","primary","extension"))
      +metrics([{label:"Published",value:published,meta:"Signed public extensions",icon:"verified"},{label:"Under review",value:state.extensions.length-published,meta:"No public visibility",icon:"rate_review"},{label:"Permission changes",value:"2",meta:"Require explicit approval",icon:"key"},{label:"Conformance pass",value:"96%",meta:"Install, failure and removal",icon:"fact_check"}])
      +panel("Extension publication registry","Applications installed into websites belong in Marketplace; packages extending WCP360 belong here.",table(["Extension","Publisher","Edition","Core","Permissions","Stage",""],rows),status("Signed registry","success"),"flush")
      +'<div class="internal-grid equal">'+panel("Private publisher responsibilities","INTERNAL decides whether a package is eligible for public discovery.",'<div class="internal-checklist">'+privateChecks+'</div>')+panel("Customer ROOT responsibilities","The public manager cannot publish or modify upstream packages.",'<div class="internal-checklist">'+rootChecks+'</div>')+'</div>'
      +'<div class="internal-callout">'+icon("difference")+'<div><strong>Marketplace application versus WCP360 extension</strong><p>An application deploys inside a customer website account. An extension contributes routes, jobs or adapters to WCP360 itself and therefore requires capability review, process isolation and a deterministic core fallback.</p></div></div></div>';
  };
  const renderCustomers=()=>{
    const rows=state.customers.map((customer,index)=>'<tr data-customer-row><td><strong>'+escapeHtml(customer.name)+'</strong><br><span class="internal-mono">'+customer.id+'</span></td><td>'+escapeHtml(customer.edition)+'</td><td>'+status(customer.subscription,customer.subscription==="Active"?"success":customer.subscription==="Past due"||customer.subscription==="Suspended"?"danger":"info")+'</td><td>'+customer.licenses+'</td><td>'+customer.installations+'</td><td>'+escapeHtml(customer.renewal)+'</td><td>'+escapeHtml(customer.contact)+'</td><td><div class="internal-row-actions"><button class="internal-icon-btn" type="button" data-action="suspend-customer" data-index="'+index+'" aria-label="Suspend license for '+escapeHtml(customer.name)+'">'+icon("pause_circle")+'</button></div></td></tr>');
    const automation=[["Trial conversion","Offer edition and preserve the same installation identity"],["Renewal reminders","Verified billing and technical contacts with delivery evidence"],["Past-due policy","Grace, human review and entitlement notice before restriction"],["Cancellation","Revoke future paid expansion while preserving export and recovery"]].map(item=>'<div class="internal-check"><span class="internal-check-icon">'+icon("automation")+'</span><div><strong>'+item[0]+'</strong><p>'+item[1]+'</p></div>'+status("Policy","info")+'</div>').join("");
    const ownership='<div class="internal-checklist"><div class="internal-check"><span class="internal-check-icon">'+icon("corporate_fare")+'</span><div><strong>Customers owns organization and subscription</strong><p>Legal identity, billing reference, contacts, edition and renewal state.</p></div>'+status("Here","success")+'</div><div class="internal-check"><span class="internal-check-icon">'+icon("license")+'</span><div><strong>Licensing owns entitlements and keys</strong><p>Activation limits, signed capabilities, grace and revocation ledger.</p></div>'+status("Linked","info")+'</div><div class="internal-check"><span class="internal-check-icon">'+icon("dns")+'</span><div><strong>Fleet owns registered installations</strong><p>Version, consent, update outcome and optional diagnostics.</p></div>'+status("Linked","info")+'</div><div class="internal-check"><span class="internal-check-icon">'+icon("storage")+'</span><div><strong>Customer WCP-ADMIN owns hosting data</strong><p>Accounts, sites, mail, databases, files and infrastructure never enter this plane.</p></div>'+status("External boundary","warning")+'</div></div>';
    return '<div class="internal-page">'+pageHead("Commerce / Customers","Organizations and subscriptions","This center owns legal organizations, billing references, subscription state, contacts and renewal workflow. It never stores hosted domains, files, mail or database content.",button("Create organization","create-customer","primary","add_business"))
      +metrics([{label:"Organizations",value:state.customers.length,meta:"Private commercial records",icon:"corporate_fare"},{label:"Annual recurring",value:"EUR 1.01M",meta:"Contracted run rate",icon:"payments"},{label:"Trials",value:state.customers.filter(customer=>customer.subscription==="Trial").length,meta:"Conversion workflow active",icon:"hourglass_top"},{label:"Renewals due",value:"3",meta:"Within next 45 days",icon:"event_repeat"}])
      +panel("Customer organizations","Commercial suspension changes entitlement state only; it never terminates a hosting account.",table(["Organization","Edition","Subscription","Licenses","Installs","Renewal","Primary contact",""],rows),status("Private CRM boundary","warning"),"flush")
      +'<div class="internal-grid equal">'+panel("Single-owner commercial model","Every record links to, but does not duplicate, another authoritative inventory.",ownership)+panel("Commercial automation","Events are auditable requests, not hidden remote server actions.",'<div class="internal-checklist">'+automation+'</div>')+'</div>'
      +'<div class="internal-callout warning">'+icon("do_not_disturb_on")+'<div><strong>No destructive billing automation</strong><p>Commerce may suspend new paid capabilities after grace. Account termination, data deletion or remote server shutdown require separate customer-side workflows and can never be triggered directly from this private record.</p></div></div></div>';
  };
  const readConnectorExchange=()=>{
    try{return JSON.parse(localStorage.getItem("wcp360.insights.exchange.v1")||"{}");}catch(error){return {};}
  };
  const renderFleet=()=>{
    const exchange=readConnectorExchange();
    const installations=Array.isArray(exchange.installations)?exchange.installations:[];
    const connectorReports=Array.isArray(exchange.reports)?exchange.reports:[];
    const connectorFindings=Array.isArray(exchange.findings)?exchange.findings:[];
    const connectorBugs=Array.isArray(exchange.bugs)?exchange.bugs:[];
    const rows=state.fleet.map(function(node){return '<tr data-fleet-row><td><strong>'+escapeHtml(node.node)+'</strong></td><td>'+escapeHtml(node.version)+'</td><td>'+status(node.channel,node.channel==="Stable"?"success":"info")+'</td><td>'+escapeHtml(node.consent)+'</td><td>'+escapeHtml(node.seen)+'</td><td>'+status(node.health,node.health==="Healthy"?"success":node.health==="Private"?"":"warning")+'</td><td><div class="internal-row-actions"><button class="internal-icon-btn" type="button" aria-label="Open fleet node '+escapeHtml(node.node)+'">'+icon("open_in_new")+'</button></div></td></tr>';});
    const signalRows=connectorReports.map(function(report){return '<tr data-connector-report><td><strong>'+escapeHtml(report.id)+'</strong><br><span class="internal-mono">Audit report</span></td><td>'+escapeHtml(report.installationId||"Unknown")+'</td><td>'+escapeHtml(report.summary||report.status||"Completed")+'</td><td>'+status(report.status||"Completed","success")+'</td><td>'+escapeHtml(report.created||"Just now")+'</td></tr>';}).concat(connectorFindings.map(function(finding){var kind=finding.severity==="Critical"?"danger":finding.severity==="High"?"warning":"info";return '<tr data-connector-finding><td><strong>'+escapeHtml(finding.id)+'</strong><br><span class="internal-mono">Finding</span></td><td>'+escapeHtml(finding.installationId||"Unknown")+'</td><td>'+escapeHtml(finding.title)+'</td><td>'+status(finding.severity,kind)+'</td><td>'+escapeHtml(finding.detected||"Just now")+'</td></tr>';})).slice(0,10);
    const bugRows=connectorBugs.slice(0,6).map(function(bug){return '<tr data-connector-bug><td><strong>'+escapeHtml(bug.id)+'</strong></td><td>'+escapeHtml(bug.installationId||"Unknown")+'</td><td>'+escapeHtml(bug.title)+'</td><td>'+status(bug.priority||"Normal",bug.priority==="Urgent"?"danger":"info")+'</td><td>'+escapeHtml(bug.status||"New")+'</td></tr>';});
    const signals=signalRows.length?table(["Evidence","Installation","Summary","Severity / status","Received"],signalRows):'<div class="internal-empty">No connector evidence received. The optional plugin is detached by default and sends nothing until customer ROOT grants consent.</div>';
    const bugs=bugRows.length?table(["Bug","Installation","Title","Priority","Status"],bugRows):'<div class="internal-empty">No diagnostic bug reports have been submitted.</div>';
    return '<div class="internal-page">'+pageHead("Operations / Fleet","Registered installations and consented insights","Fleet Management is not remote Linux root. The detachable Insights Connector sends only approved inventory, health, failure and bug evidence. It cannot execute commands or collect hosted content.",button("Refresh connector data","refresh-connector","","sync")+button("Export inventory","export-fleet","","download"))
      +metrics([
        {label:"Registered",value:state.fleet.length,meta:"Minimal vendor inventory",icon:"dns"},
        {label:"Insights connected",value:installations.length,meta:"Explicit customer consent",icon:"hub"},
        {label:"Open findings",value:connectorFindings.length,meta:"Sanitized diagnostic evidence",icon:"bug_report"},
        {label:"Bug reports",value:connectorBugs.length,meta:"Customer-submitted context",icon:"feedback"}
      ])
      +panel("Installation registry","Consent level is visible and enforced for every fleet action.",table(["Installation","Version","Channel","Consent","Last seen","Health",""],rows),status("Consent enforced","success"),"flush")
      +'<div class="internal-grid equal">'+panel("Connector audit and failure evidence","Reports arrive through a separate signed extension and remain scoped to the installation consent.",signals,status(installations.length?"Ingestion active":"Detached",installations.length?"success":"warning"),"flush")+panel("Diagnostic bug intake","Bug context is minimized, reviewed and correlated without copying secrets, files, database rows or message content.",bugs,status(connectorBugs.length+" reports","info"),"flush")+'</div>'
      +'<div class="internal-grid equal">'
        +panel("Data minimization","What the private fleet may store.",'<div class="internal-checklist">'+[["Installation ID","Random identifier, edition and version"],["Compatibility facts","OS family, architecture and enabled public modules"],["Operational health","Only when diagnostics consent is granted"],["Update outcome","Manifest version, status and recovery result"]].map(function(item){return '<div class="internal-check"><span class="internal-check-icon">'+icon("database")+'</span><div><strong>'+item[0]+'</strong><p>'+item[1]+'</p></div>'+status("Allowed","success")+'</div>';}).join("")+'</div>')
        +panel("Never collected or executed","Hosting content and control remain on the customer server.",'<div class="internal-checklist">'+[["Hosted content","No domains, files, messages, database rows or credentials"],["Remote administration","No hidden shell, universal root or unattended backdoor"],["Automatic remediation","No vendor-side command execution; customer ROOT approves local jobs"],["Disabled connector","No heartbeat, inventory, audit or bug transmission"]].map(function(item){return '<div class="internal-check"><span class="internal-check-icon internal-check-danger">'+icon("block")+'</span><div><strong>'+item[0]+'</strong><p>'+item[1]+'</p></div>'+status("Forbidden","danger")+'</div>';}).join("")+'</div>')
      +'</div><div class="internal-callout warning">'+icon("extension_off")+'<div><strong>Detached distribution boundary</strong><p>The connector is not part of the WCP360 core ZIP. It is built, signed, installed, updated, disabled and uninstalled as an independent extension. INTERNAL remains inaccessible when the connector is removed.</p></div></div></div>';
  };
  const renderSecurityAdvanced=()=>{
    const approvalRows=state.approvals.map((approval,index)=>'<tr data-internal-approval><td><strong>'+approval.id+'</strong><br><span class="internal-mono">'+escapeHtml(approval.type)+'</span></td><td>'+escapeHtml(approval.requester)+'</td><td>'+escapeHtml(approval.scope)+'</td><td>'+escapeHtml(approval.duration)+'</td><td>'+status(approval.risk,approval.risk==="Critical"?"danger":approval.risk==="High"?"warning":"info")+'</td><td>'+status(approval.status,approval.status==="Approved"?"success":approval.status==="Pending"?"warning":"")+'</td><td><div class="internal-row-actions">'+(approval.status==="Pending"?'<button class="internal-icon-btn" type="button" data-action="approve-access" data-index="'+index+'" aria-label="Approve '+approval.id+'">'+icon("approval")+'</button>':'')+'</div></td></tr>');
    const operatorRows=state.operators.map(operator=>'<tr data-internal-operator><td><strong>'+escapeHtml(operator.name)+'</strong><br><span class="internal-mono">'+escapeHtml(operator.email)+'</span></td><td>'+escapeHtml(operator.role)+'</td><td>'+status(operator.mfa,"success")+'</td><td>'+escapeHtml(operator.access)+'</td><td>'+escapeHtml(operator.lastSeen)+'</td></tr>');
    const sessionRows=state.sessions.map(session=>'<tr data-internal-session><td><strong>'+session.id+'</strong></td><td>'+escapeHtml(session.operator)+'</td><td>'+escapeHtml(session.context)+'</td><td>'+escapeHtml(session.device)+'</td><td>'+escapeHtml(session.expires)+'</td><td>'+status(session.status,session.status==="Active"?"success":"")+'</td></tr>');
    const accessChecks=[["Separate workforce identity","No customer identity or Linux account can authenticate here"],["Phishing-resistant MFA","FIDO2 for people and mTLS workload identities"],["JIT privilege","High-risk roles require scope, purpose, duration and approval"],["Separation of duties","Requester cannot approve their own elevation or release"],["Session recording","Commands, API calls and UI mutations share one correlation"],["Automatic expiry","Tokens, roles and support consent are revoked at deadline"]].map(item=>'<div class="internal-check"><span class="internal-check-icon">'+icon("admin_panel_settings")+'</span><div><strong>'+item[0]+'</strong><p>'+item[1]+'</p></div>'+status("Enforced","success")+'</div>').join("");
    return '<div class="internal-page">'+pageHead("Trust / Security Center","Zero-trust operator access","Security covers source and release integrity plus the people and workload identities allowed to operate the private plane. Standing privilege is minimized; high-risk access is scoped, approved, recorded and automatically expired.",button("Request JIT access","request-access","","admin_panel_settings")+button("Run release audit","run-security-audit","primary","policy"))
      +metrics([{label:"Operators",value:state.operators.length,meta:"People and workloads",icon:"group"},{label:"Pending approvals",value:state.approvals.filter(approval=>approval.status==="Pending").length,meta:"Independent decision required",icon:"approval"},{label:"Active sessions",value:state.sessions.filter(session=>session.status==="Active").length,meta:"Short-lived and recorded",icon:"login"},{label:"Blocking controls",value:securityChecks.length,meta:"All release gates passing",icon:"shield"}])
      +panel("JIT and high-risk approval queue","Each request records purpose, scope, expiry, requester, approver and final execution evidence.",table(["Request","Requester","Scope","Duration","Risk","Status",""],approvalRows),status("Dual control","success"),"flush")
      +'<div class="internal-grid equal">'+panel("Internal operators","Roles are job functions, not permanent super-admin accounts.",table(["Operator","Role","MFA","Effective access","Last seen"],operatorRows),status("FIDO2 required","success"),"flush")+panel("Privileged sessions","Every active context has a bounded role and expiry.",table(["Session","Operator","Context","Device / auth","Expires","Status"],sessionRows),status("Recorded","info"),"flush")+'</div>'
      +'<div class="internal-grid">'+panel("Stable release security gates","A failed blocking control removes promotion eligibility.",checklist(),status("8 / 8 passing","success"))+panel("Operator access contract","INTERNAL access remains independent from Linux root and customer WCP-ADMIN.",'<div class="internal-checklist">'+accessChecks+'</div>')+'</div>'
      +'<div class="internal-callout warning">'+icon("warning")+'<div><strong>Approval is not execution</strong><p>An approved request creates a short-lived capability token. The target operation still revalidates policy, artifact identity, customer consent and current service state before it can run.</p></div></div>'
      +panel("Emergency response sequence","Compromised packages are contained without disabling healthy customer installations.",'<div class="internal-code">01 freeze affected channel\\n02 revoke compromised artifact hash\\n03 rotate signing key if required\\n04 publish fixed release and advisory\\n05 offer verified rollback or safe update\\n06 preserve WORM evidence\\n07 complete root-cause review</div>')
      +'</div>';
  };
  const renderers={overview:renderOverview,boundary:renderBoundary,builds:renderBuilds,releases:renderReleases,distribution:renderDistribution,licensing:renderLicensing,marketplace:renderMarketplace,extensions:renderExtensions,customers:renderCustomers,fleet:renderFleet,security:renderSecurityAdvanced};
  const render=()=>{
    renderNav();
    renderHeader();
    const host=document.getElementById("internalControlPlane");
    host.innerHTML=renderers[currentTab()]();
    if(currentTab()==="boundary"){
      const detachedIndex=state.components.findIndex(component=>component.detached);
      const detachedRow=document.querySelectorAll("[data-component-row]")[detachedIndex];
      if(detachedRow){
        detachedRow.children[4].innerHTML=status("Detached artifact","warning");
        const control=detachedRow.querySelector("[data-action='toggle-component']");
        if(control)control.setAttribute("aria-label","Built and distributed as a separate extension artifact");
      }
    }
    document.title=`${titles[currentTab()][0]} - WCP360 INTERNAL`;
    bindDynamicFilters();
    window.scrollTo({top:0,behavior:"instant"});
  };
  const bindDynamicFilters=()=>{
    const search=document.getElementById("componentSearch");
    const family=document.getElementById("componentFamily");
    const apply=()=>{
      const query=(search?.value||"").trim().toLowerCase();
      const selected=family?.value||"";
      document.querySelectorAll("[data-component-row]").forEach((row,index)=>{
        const component=state.components[index];
        const matchText=!query||[component.name,component.repo,component.owner].join(" ").toLowerCase().includes(query);
        const matchFamily=!selected||component.family===selected;
        row.hidden=!(matchText&&matchFamily);
      });
    };
    search?.addEventListener("input",apply);
    family?.addEventListener("change",apply);
  };
  const toast=message=>{
    const stack=document.getElementById("wcpToastStack");
    const node=document.createElement("div");
    node.className="internal-toast";
    node.textContent=message;
    stack.appendChild(node);
    setTimeout(()=>node.remove(),3600);
  };
  const closeDrawer=()=>{document.getElementById("internalDrawer").innerHTML="";};
  const field=(label,id,type="text",value="",help="")=>`<div class="internal-field"><label for="${id}">${label}</label><input class="internal-input" id="${id}" name="${id}" type="${type}" value="${escapeHtml(value)}" required>${help?`<small>${help}</small>`:""}</div>`;
  const selectField=(label,id,options,help="")=>`<div class="internal-field"><label for="${id}">${label}</label><select class="internal-select" id="${id}" name="${id}">${options.map(option=>`<option>${escapeHtml(option)}</option>`).join("")}</select>${help?`<small>${help}</small>`:""}</div>`;
  const openDrawer=({title,sub,kind,body,submit="Save",danger=false,confirmWord="REVOKE"})=>{
    document.getElementById("internalDrawer").innerHTML=`<div class="internal-drawer-shell" role="dialog" aria-modal="true" aria-labelledby="internalDrawerTitle"><button class="internal-drawer-backdrop" type="button" data-action="close-drawer" aria-label="Close dialog"></button><section class="internal-drawer"><div class="internal-drawer-head"><div><h2 id="internalDrawerTitle">${title}</h2><p>${sub}</p></div><button class="internal-icon-btn" type="button" data-action="close-drawer" aria-label="Close dialog">${icon("close")}</button></div><form class="internal-drawer-body" id="internalDrawerForm" data-form-kind="${kind}">${body}</form><div class="internal-drawer-foot"><button class="internal-btn" type="button" data-action="close-drawer">Cancel</button><button class="internal-btn ${danger?"danger":"primary"}" id="internalDrawerSubmit" type="submit" form="internalDrawerForm" ${danger?"disabled":""}>${submit}</button></div></section></div>`;
    document.querySelector("#internalDrawer input, #internalDrawer select")?.focus();
    if(danger){
      document.getElementById("confirmToken")?.addEventListener("input",event=>{
        document.getElementById("internalDrawerSubmit").disabled=event.target.value!==confirmWord;
      });
    }
  };
  const openCreateBuild=()=>openDrawer({
    title:"Create reproducible build",
    sub:"Select one isolated output lane. Cross-lane inclusion is blocked.",
    kind:"create-build",
    submit:"Queue build",
    body:`${selectField("Build target","buildTarget",["WCP360 public edition","WCP360 Insights Connector (detached)"],"The detached target never enters an edition archive.")}<div class="internal-form-grid">${field("Version","buildVersion","text","2.6.0-rc.3")}${field("Git commit","buildCommit","text","8a71f0c","Protected commit or signed tag")}${selectField("Edition","buildEdition",["Community","Business","Enterprise"],"Used only for the public product lane.")}${field("Compatible core range","buildCompatibility","text",">=2.5.0 <3.0.0","Required for detached extensions.")}${selectField("Runner image","buildRunner",["wcp-builder:v2.6.14","wcp-builder:v2.6.13"])}</div><div class="internal-callout">${icon("fact_check")}<div><strong>Positive selection and output isolation</strong><p>The core worker receives ${state.components.filter(component=>component.selected&&!component.detached&&component.family!=="Internal service").length} approved components. Detached source is mounted only in its dedicated worker and produces a separate package, manifest, SBOM and provenance.</p></div></div>`
  });
  const openPromote=()=>openDrawer({
    title:"Promote release candidate",
    sub:"Promotion references an existing verified build.",
    kind:"promote-release",
    submit:"Request promotion",
    body:`${selectField("Verified core build","releaseBuild",state.builds.filter(build=>build.result==="Passed"&&(build.lane||"Public core")==="Public core").map(build=>`${build.id} / ${build.version} / ${build.edition}`))}${selectField("Target channel","releaseChannel",["Beta","RC","Stable"],"Detached extensions use their independent extension index.")}${field("Change ticket","releaseTicket","text","REL-2026-1042")}<div class="internal-callout warning">${icon("approval")}<div><strong>Dual control for Stable</strong><p>The requesting operator cannot approve their own Stable promotion. Detached builds cannot be promoted into a core channel.</p></div></div>`
  });
  const openRevoke=(index,lane="core")=>{
    const artifact=lane==="detached"?state.detachedArtifacts[index]:state.artifacts[index];
    openDrawer({
      title:lane==="detached"?"Revoke detached extension":"Revoke core public artifact",
      sub:artifact.name,
      kind:"revoke-artifact",
      submit:"Revoke artifact",
      danger:true,
      body:`<input type="hidden" name="artifactIndex" value="${index}"><input type="hidden" name="artifactLane" value="${lane}"><div class="internal-callout warning">${icon("warning")}<div><strong>Lane-scoped emergency change</strong><p>A signed deny record blocks only this artifact identity. The other distribution lane remains unchanged and existing systems receive recovery guidance.</p></div></div>${field("Incident or change ticket","revokeTicket","text","SEC-2026-")}${field("Reason","revokeReason","text","Compromised or invalid artifact")}<div class="internal-field"><label for="confirmToken">Type <span class="internal-confirm-token">REVOKE</span> to confirm</label><input class="internal-input" id="confirmToken" name="confirmToken" autocomplete="off" required><small>This action requires a second approver in production.</small></div>`
    });
  };
  const openIssueLicense=()=>openDrawer({
    title:"Issue signed entitlement",
    sub:"Commercial data remains in the private authority.",
    kind:"issue-license",
    submit:"Issue entitlement",
    body:`<div class="internal-form-grid">${field("Customer reference","licenseCustomer","text","CUS-")}${selectField("Edition","licenseEdition",["Business","Enterprise","Trial"])}${field("Activation limit","licenseLimit","number","1")}${field("Validity end","licenseExpiry","date","2027-10-29")}</div><div class="internal-callout">${icon("privacy_tip")}<div><strong>Minimum disclosure</strong><p>The public entitlement contains edition, capabilities, expiry and signature. It does not contain billing details.</p></div></div>`
  });
  const openApplication=()=>openDrawer({
    title:"Submit application package",
    sub:"Package remains private until all publisher gates pass.",
    kind:"submit-application",
    submit:"Create review",
    body:`<div class="internal-form-grid">${field("Application name","appName","text","")}${field("Version","appVersion","text","1.0.0")}${field("Publisher ID","appPublisher","text","PUB-")}${selectField("Target visibility","appVisibility",["Public","Business+","Enterprise","Internal only"])}</div>${field("Package digest","appDigest","text","sha256:","The upload endpoint validates this digest before analysis.")}`
  });
  const openExtension=()=>openDrawer({
    title:"Submit WCP360 extension",
    sub:"Submission creates a private review record only.",
    kind:"submit-extension",
    submit:"Create extension review",
    body:'<div class="internal-form-grid">'+field("Extension ID","extensionId","text","publisher.extension")+field("Version","extensionVersion","text","1.0.0")+field("Publisher","extensionPublisher","text","PUB-")+selectField("Edition tier","extensionTier",["Community","Business+","Enterprise","Security add-on"])+'</div>'+field("Requested capabilities","extensionPermissions","text","events.read","Comma-separated versioned capabilities")+field("Package digest","extensionDigest","text","sha256:","The package remains private until every blocking gate passes.")
  });
  const openExtensionReview=index=>{
    const extension=state.extensions[index];
    openDrawer({
      title:"Review extension stage",
      sub:extension.name+" / "+extension.id,
      kind:"review-extension",
      submit:"Save review decision",
      body:'<input type="hidden" name="extensionIndex" value="'+index+'">'+selectField("Review decision","extensionStage",["Security review","Compatibility hold","Approved for signing"],"Publication still requires an independently signed release job.")+field("Review ticket","extensionTicket","text","EXT-2026-")+field("Decision note","extensionNote","text","Evidence reviewed")
    });
  };
  const openCustomer=()=>openDrawer({
    title:"Create customer organization",
    sub:"Create commercial identity without hosting-content access.",
    kind:"create-customer",
    submit:"Create organization",
    body:'<div class="internal-form-grid">'+field("Organization name","customerName","text","")+field("Billing reference","customerBilling","text","BILL-")+selectField("Edition","customerEdition",["Community","Business","Enterprise","Trial"])+field("License allocation","customerLicenses","number","1")+'</div>'+field("Primary contact","customerContact","email","admin@example.com")+field("Renewal date","customerRenewal","date","2027-10-29")
  });
  const openCustomerSuspend=index=>{
    const customer=state.customers[index];
    openDrawer({
      title:"Suspend commercial entitlement",
      sub:customer.name+" / "+customer.id,
      kind:"suspend-customer",
      submit:"Suspend entitlement",
      danger:true,
      confirmWord:"SUSPEND",
      body:'<input type="hidden" name="customerIndex" value="'+index+'"><div class="internal-callout warning">'+icon("warning")+'<div><strong>This does not terminate hosting</strong><p>The public verifier receives a signed commercial status with grace. Existing workloads, login, export and recovery continue.</p></div></div>'+field("Case or invoice reference","customerCase","text","BILL-2026-")+field("Reason","customerReason","text","Past due after reviewed grace")+'<div class="internal-field"><label for="confirmToken">Type <span class="internal-confirm-token">SUSPEND</span> to confirm</label><input class="internal-input" id="confirmToken" name="confirmToken" autocomplete="off" required><small>Production also requires an independent approver.</small></div>'
    });
  };
  const openAccessRequest=()=>openDrawer({
    title:"Request temporary privilege",
    sub:"Standing privilege is not granted for high-risk operations.",
    kind:"request-access",
    submit:"Submit JIT request",
    body:selectField("Privilege type","accessType",["Fleet diagnostics","Stable release approval","Artifact revocation","Signing-key ceremony","Customer support read-only"])+field("Exact scope","accessScope","text","ORG-1038 / diagnostics")+selectField("Maximum duration","accessDuration",["15 min","30 min","60 min","One operation"])+field("Change or incident ticket","accessTicket","text","CHG-2026-")+field("Business purpose","accessPurpose","text","Diagnose customer-approved incident")
  });
  const openAccessApproval=index=>{
    const approval=state.approvals[index];
    openDrawer({
      title:"Approve JIT privilege",
      sub:approval.id+" / "+approval.requester,
      kind:"approve-access",
      submit:"Approve temporary access",
      danger:true,
      confirmWord:approval.id,
      body:'<input type="hidden" name="approvalIndex" value="'+index+'"><div class="internal-callout warning">'+icon("approval")+'<div><strong>Independent approval required</strong><p>The request is limited to '+escapeHtml(approval.scope)+' for '+escapeHtml(approval.duration)+'. Approval creates an expiring capability, not a standing role.</p></div></div>'+field("Approval ticket","approvalTicket","text","APR-DECISION-")+field("Decision reason","approvalReason","text","Scope and evidence verified")+'<div class="internal-field"><label for="confirmToken">Type <span class="internal-confirm-token">'+approval.id+'</span> to confirm</label><input class="internal-input" id="confirmToken" name="confirmToken" autocomplete="off" required><small>The requester cannot approve this operation.</small></div>'
    });
  };
  const downloadJson=(name,data)=>{
    const blob=new Blob([JSON.stringify(data,null,2)],{type:"application/json"});
    const url=URL.createObjectURL(blob);
    const anchor=document.createElement("a");
    anchor.href=url;
    anchor.download=name;
    anchor.click();
    setTimeout(()=>URL.revokeObjectURL(url),1000);
  };
  const handleForm=form=>{
    const data=Object.fromEntries(new FormData(form).entries());
    switch(form.dataset.formKind){
      case "create-build":{
        const number=1300+state.builds.length;
        const detached=data.buildTarget.includes("(detached)");
        const build={id:`BLD-${number}`,commit:data.buildCommit,edition:detached?"Independent":data.buildEdition,version:data.buildVersion,lane:detached?"Detached extension":"Public core",target:detached?"WCP360 Insights Connector":data.buildEdition,result:"Passed",created:"now"};
        state.builds.unshift(build);
        if(detached){
          const artifactName=`wcp360-insights-connector-${build.version}.zip`;
          state.detachedArtifacts.unshift({name:artifactName,extension:"com.wcp360.insights-connector",version:build.version,compatibility:data.buildCompatibility,size:"Pending",sha:`pending-${number}`,evidence:"Manifest + SBOM + provenance",status:"Signed",coreIncluded:false});
          addActivity("extension","Detached package built",`${artifactName} produced outside all WCP360 edition archives from ${build.commit}.`);
          toast(`${build.id} completed in the detached extension worker.`);
        }else{
          addActivity("precision_manufacturing","Core build queued",`${build.id} will produce ${build.edition} ${build.version} from ${build.commit}.`);
          toast(`${build.id} queued on the isolated core worker.`);
        }
        break;
      }
      case "promote-release":{
        const source=data.releaseBuild.split(" / ");
        state.releases.unshift({version:source[1],channel:data.releaseChannel,status:data.releaseChannel==="Stable"?"Approval required":"Candidate",coverage:"Pending",date:"29 Oct 2026"});
        addActivity("rocket_launch","Promotion requested",`${source[1]} requested for ${data.releaseChannel} under ${data.releaseTicket}.`);
        toast(data.releaseChannel==="Stable"?"Stable promotion awaits a second approver.":"Release candidate promoted.");
        break;
      }
      case "revoke-artifact":{
        const lane=data.artifactLane==="detached"?"detached":"core";
        const artifact=lane==="detached"?state.detachedArtifacts[Number(data.artifactIndex)]:state.artifacts[Number(data.artifactIndex)];
        if(data.confirmToken!=="REVOKE"){toast("Confirmation token does not match.");return;}
        if(!state.revoked.includes(artifact.name))state.revoked.push(artifact.name);
        addActivity("block",lane==="detached"?"Detached extension revoked":"Core artifact revoked",`${artifact.name} blocked under ${data.revokeTicket}; the other lane remains unchanged.`);
        toast(`Signed ${lane} revocation record queued for publication.`);
        break;
      }
      case "issue-license":
        addActivity("license","Entitlement issued",`${data.licenseEdition} entitlement created for ${data.licenseCustomer}.`);
        toast("Signed entitlement issued by the private authority.");
        break;
      case "submit-application":
        state.apps.push({name:data.appName||"Untitled application",publisher:data.appPublisher,version:data.appVersion,compatibility:"Review pending",signature:"Pending",visibility:data.appVisibility});
        addActivity("rate_review","Application review created",`${data.appName} ${data.appVersion} entered private analysis.`);
        toast("Application submitted to private review.");
        break;
      case "submit-extension":
        state.extensions.push({id:data.extensionId,name:data.extensionId.split(".").pop().replace(/-/g," "),publisher:data.extensionPublisher,version:data.extensionVersion,tier:data.extensionTier,permissions:data.extensionPermissions,compatibility:"Pending",stage:"Security review"});
        addActivity("extension","Extension review created",data.extensionId+" "+data.extensionVersion+" entered private conformance.");
        toast("Extension package registered for private review.");
        break;
      case "review-extension":{
        const extension=state.extensions[Number(data.extensionIndex)];
        extension.stage=data.extensionStage;
        addActivity("rate_review","Extension decision saved",extension.id+" moved to "+data.extensionStage+" under "+data.extensionTicket+".");
        toast("Extension review decision saved.");
        break;
      }
      case "create-customer":{
        const nextId="ORG-"+String(1100+state.customers.length);
        state.customers.unshift({id:nextId,name:data.customerName,edition:data.customerEdition,subscription:data.customerEdition==="Trial"?"Trial":"Active",licenses:Number(data.customerLicenses),installations:0,renewal:data.customerRenewal,contact:data.customerContact});
        addActivity("corporate_fare","Organization created",nextId+" / "+data.customerName+" created without hosting-content access.");
        toast("Private customer organization created.");
        break;
      }
      case "suspend-customer":{
        if(data.confirmToken!=="SUSPEND"){toast("Confirmation token does not match.");return;}
        const customer=state.customers[Number(data.customerIndex)];
        customer.subscription="Suspended";
        addActivity("pause_circle","Commercial entitlement suspended",customer.id+" suspended under "+data.customerCase+"; hosting remains online.");
        toast("Commercial entitlement suspended; hosting was not terminated.");
        break;
      }
      case "request-access":{
        const requestId="APR-"+String(2900+state.approvals.length);
        const highRisk=/release|revocation|signing/i.test(data.accessType);
        state.approvals.unshift({id:requestId,type:data.accessType,requester:"Omar Diallo",scope:data.accessScope,duration:data.accessDuration,risk:highRisk?"High":"Medium",status:"Pending"});
        addActivity("admin_panel_settings","JIT access requested",requestId+" / "+data.accessType+" / "+data.accessTicket+".");
        toast(requestId+" submitted for independent approval.");
        break;
      }
      case "approve-access":{
        const approval=state.approvals[Number(data.approvalIndex)];
        if(data.confirmToken!==approval.id){toast("Approval identifier does not match.");return;}
        if(approval.requester==="Maya Chen"){toast("Separation of duties blocked self-approval.");return;}
        approval.status="Approved";
        state.sessions.unshift({id:"SES-J"+String(900+state.sessions.length),operator:approval.requester,context:approval.type,device:"Managed device / FIDO2",started:"Now",expires:approval.duration,status:"Active"});
        addActivity("approval","JIT privilege approved",approval.id+" approved under "+data.approvalTicket+" with automatic expiry.");
        toast("Temporary capability issued and session recording enabled.");
        break;
      }
    }
    save();
    closeDrawer();
    render();
  };
  const handleAction=(action,target)=>{
    switch(action){
      case "create-build":openCreateBuild();break;
      case "promote-release":openPromote();break;
      case "revoke-artifact":openRevoke(Number(target.dataset.index));break;
      case "revoke-detached-artifact":openRevoke(Number(target.dataset.index),"detached");break;
      case "issue-license":openIssueLicense();break;
      case "submit-application":openApplication();break;
      case "submit-extension":openExtension();break;
      case "review-extension":openExtensionReview(Number(target.dataset.index));break;
      case "create-customer":openCustomer();break;
      case "suspend-customer":openCustomerSuspend(Number(target.dataset.index));break;
      case "request-access":openAccessRequest();break;
      case "approve-access":openAccessApproval(Number(target.dataset.index));break;
      case "close-drawer":closeDrawer();break;
      case "go-boundary":location.hash="boundary";break;
      case "publish-manifest":
        state.manifestPublished=true;
        addActivity("publish","Stable manifest published","Signed stable manifest propagated atomically to six CDN regions.");
        save();render();toast("Signed manifest published atomically.");break;
      case "publish-extension-index":
        state.detachedManifestPublished=true;
        addActivity("extension","Extension index published","Detached package identities, compatibility ranges and deny records published independently.");
        save();render();toast("Signed detached-extension index published.");break;
      case "toggle-component":{
        const component=state.components[Number(target.dataset.index)];
        if(component.family==="Internal service"){toast("Blocked: INTERNAL services can never enter a public allowlist.");break;}
        if(component.detached){toast("Detached extensions are built, signed and distributed as independent artifacts.");break;}
        if(component.family==="Public core"||component.family==="Public client"){toast("Required public components cannot be removed from the product core.");break;}
        component.selected=!component.selected;
        save();render();
        toast(`${component.name} ${component.selected?"added to":"removed from"} the edition allowlist.`);
        break;
      }
      case "export-allowlist":
        downloadJson("wcp360-public-allowlist.json",{schema:"wcp360.release/v1",generated_at:new Date().toISOString(),components:state.components.filter(component=>component.selected&&!component.detached&&component.family!=="Internal service").map(component=>({name:component.name,repo:component.repo,family:component.family}))});
        toast("Public allowlist exported.");break;
      case "export-fleet":
        downloadJson("wcp360-fleet-minimal.json",state.fleet);
        toast("Minimal fleet inventory exported.");break;
      case "refresh-connector":
        render();toast("Consented connector reports refreshed.");break;
      case "run-security-audit":
        addActivity("policy","Release audit completed","All eight blocking controls passed for the current Stable artifact.");
        save();render();toast("Release security audit passed: 8 / 8 controls.");break;
    }
  };
  document.addEventListener("click",event=>{
    const menu=event.target.closest("[data-internal-menu]");
    if(menu){
      document.querySelector(".internal-sidebar")?.classList.toggle("open");
      document.querySelector(".internal-overlay")?.classList.toggle("open");
      return;
    }
    const trigger=event.target.closest("[data-action]");
    if(trigger)handleAction(trigger.dataset.action,trigger);
  });
  document.addEventListener("submit",event=>{
    if(event.target.matches("[data-form-kind]")){
      event.preventDefault();
      handleForm(event.target);
    }
  });
  document.addEventListener("keydown",event=>{
    if(event.key==="Escape")closeDrawer();
  });
  window.addEventListener("hashchange",()=>{
    document.querySelector(".internal-sidebar")?.classList.remove("open");
    document.querySelector(".internal-overlay")?.classList.remove("open");
    render();
  });
  render();
})();
