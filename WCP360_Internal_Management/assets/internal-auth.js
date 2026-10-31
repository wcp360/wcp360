(function(){
  "use strict";
  var key="wcp360.internal.session.v1",allowedDomain="@internal.example.com",fifteenMinutes=15*60*1000;
  function read(){try{return JSON.parse(sessionStorage.getItem(key)||"null");}catch(error){return null;}}
  function valid(session){return Boolean(session&&session.email&&session.email.toLowerCase().endsWith(allowedDomain)&&session.authStrength==="phishing-resistant"&&Number(session.expiresAt)>Date.now());}
  function safeReturn(){var requested=new URLSearchParams(location.search).get("return")||"internal-distribution.html";return /^[a-z0-9-]+\\.html(?:#[a-z0-9-]+)?$/i.test(requested)?requested:"internal-distribution.html";}
  function createSession(email,method){var now=Date.now(),session={id:"INT-"+now.toString(36).toUpperCase(),email:email.toLowerCase(),displayName:email.split("@")[0].replace(/[._-]+/g," "),method:method,authStrength:"phishing-resistant",issuedAt:now,expiresAt:now+fifteenMinutes,deviceTrust:"Managed device"};sessionStorage.setItem(key,JSON.stringify(session));return session;}
  var login=document.querySelector("[data-internal-login]");
  if(login){
    var existing=read(),notice=document.querySelector("[data-login-session]");
    if(valid(existing)&&notice){notice.hidden=false;notice.textContent="An active INTERNAL session already exists for "+existing.email+". Signing in again replaces it with a new short-lived session.";}
    var form=document.getElementById("internalLoginForm"),email=document.getElementById("internalEmail"),error=document.getElementById("internalLoginError");
    function signIn(method){var value=(email.value||"").trim().toLowerCase();if(!value.endsWith(allowedDomain)){error.hidden=false;error.textContent="Access denied. Only identities issued by internal.example.com can enter this private control plane.";email.setAttribute("aria-invalid","true");email.focus();return;}error.hidden=true;email.removeAttribute("aria-invalid");createSession(value,method);location.href=safeReturn();}
    form.addEventListener("submit",function(event){event.preventDefault();signIn("FIDO2 passkey");});
    document.querySelector("[data-internal-sso]").addEventListener("click",function(){signIn("Corporate SSO + FIDO2");});
    return;
  }
  var controlPlane=document.querySelector("[data-internal-control-plane]");
  if(controlPlane){
    var session=read();
    if(!valid(session)){window.WCPInternalAuthorized=false;var target=location.pathname.split("/").pop()+(location.hash||"");location.replace("index.html?reason=session-required&return="+encodeURIComponent(target));return;}
    window.WCPInternalAuthorized=true;window.WCPInternalIdentity=session;document.body.removeAttribute("data-auth-pending");
    document.addEventListener("click",function(event){if(!event.target.closest("[data-internal-logout]"))return;sessionStorage.removeItem(key);location.replace("index.html?reason=signed-out");});
  }
})();
