(function(){
  'use strict';

  var STORAGE_KEY='n360_cookie_consent_v1';
  var GA_ID='G-1LHH5DVGRX';
  var GTM_ID='GTM-M2TXPFV';
  var trackingLoaded=false;

  window.dataLayer=window.dataLayer||[];
  window.gtag=window.gtag||function(){window.dataLayer.push(arguments);};
  window.gtag('consent','default',{
    ad_storage:'denied',
    ad_user_data:'denied',
    ad_personalization:'denied',
    analytics_storage:'denied',
    functionality_storage:'granted',
    security_storage:'granted',
    wait_for_update:500
  });

  function readChoice(){
    try{
      var choice=JSON.parse(localStorage.getItem(STORAGE_KEY));
      var savedAt=choice&&Date.parse(choice.updatedAt);
      if(!savedAt||Date.now()-savedAt>15552000000){localStorage.removeItem(STORAGE_KEY);return null;}
      return choice;
    }catch(error){return null;}
  }

  function saveChoice(analytics){
    var choice={analytics:!!analytics,updatedAt:new Date().toISOString(),version:1};
    try{localStorage.setItem(STORAGE_KEY,JSON.stringify(choice));}catch(error){}
    applyChoice(choice);
    return choice;
  }

  function loadTracking(){
    if(trackingLoaded)return;
    trackingLoaded=true;
    window.gtag('js',new Date());
    window.gtag('config',GA_ID);

    var ga=document.createElement('script');
    ga.async=true;
    ga.src='https://www.googletagmanager.com/gtag/js?id='+encodeURIComponent(GA_ID);
    document.head.appendChild(ga);

    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!=='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer',GTM_ID);
  }

  function applyChoice(choice){
    var status=choice&&choice.analytics?'granted':'denied';
    window.gtag('consent','update',{analytics_storage:status,ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied'});
    if(status==='granted')loadTracking();
  }

  var savedChoice=readChoice();
  if(savedChoice)applyChoice(savedChoice);

  function buildInterface(){
    var modal=document.createElement('div');
    modal.className='n360-consent';
    modal.id='n360-cookie-consent';
    modal.setAttribute('role','dialog');
    modal.setAttribute('aria-modal','true');
    modal.setAttribute('aria-labelledby','n360-consent-title');
    modal.innerHTML='<div class="n360-consent__panel"><div class="n360-consent__intro"><h2 class="n360-consent__title" id="n360-consent-title">Votre vie privée, votre choix</h2><p class="n360-consent__text">Nous utilisons des cookies nécessaires au fonctionnement du site et, avec votre accord, des cookies de mesure d\u2019audience Google Analytics. Vous pouvez accepter, refuser ou personnaliser votre choix. <a href="/politique-cookies">En savoir plus</a></p><div class="n360-consent__actions"><button class="n360-consent__button n360-consent__button--primary" type="button" data-consent="accept">Tout accepter</button><button class="n360-consent__button n360-consent__button--primary" type="button" data-consent="reject">Tout refuser</button><button class="n360-consent__button" type="button" data-consent="settings">Personnaliser</button></div></div><div class="n360-consent__details"><h2 class="n360-consent__title">Personnaliser les cookies</h2><p class="n360-consent__text">Vous pouvez modifier ce choix à tout moment grâce à l’icône de préférences placée en bas de page.</p><div class="n360-consent__choice"><div><strong>Cookies nécessaires</strong><small>Indispensables au fonctionnement et à la sécurité du site.</small></div><span class="n360-consent__status">Toujours actifs</span></div><div class="n360-consent__choice"><div><strong>Mesure d\u2019audience</strong><small>Permet de comprendre l\u2019utilisation du site avec Google Analytics.</small></div><label class="n360-consent__toggle"><input type="checkbox" data-consent-analytics aria-label="Autoriser les cookies de mesure d\u2019audience"><span aria-hidden="true"></span></label></div><div class="n360-consent__actions"><button class="n360-consent__button n360-consent__button--primary" type="button" data-consent="save">Enregistrer mes choix</button><button class="n360-consent__button" type="button" data-consent="back">Retour</button></div></div></div>';

    var settingsButton=document.createElement('button');
    settingsButton.type='button';
    settingsButton.className='n360-cookie-settings';
    settingsButton.innerHTML='<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M21 12.4A9 9 0 1 1 11.6 3 4.5 4.5 0 0 0 17 8.4a4.5 4.5 0 0 0 4 4Z"/><circle cx="8.2" cy="9" r=".8"/><circle cx="7.5" cy="14.5" r=".8"/><circle cx="13" cy="15.5" r=".8"/></svg><span class="n360-cookie-settings__label">Préférences cookies</span>';
    settingsButton.setAttribute('aria-label','Modifier mes préférences de cookies');
    settingsButton.hidden=!savedChoice;

    function show(view){
      modal.dataset.view=view||'main';
      modal.hidden=false;
      settingsButton.hidden=true;
      var current=readChoice();
      modal.querySelector('[data-consent-analytics]').checked=!!(current&&current.analytics);
      document.documentElement.style.overflow='hidden';
      setTimeout(function(){modal.querySelector(view==='settings'?'[data-consent="save"]':'[data-consent="accept"]').focus();},0);
    }

    function hide(){
      modal.hidden=true;
      settingsButton.hidden=false;
      document.documentElement.style.overflow='';
      settingsButton.focus();
    }

    modal.addEventListener('click',function(event){
      var button=event.target.closest('[data-consent]');
      if(!button)return;
      var action=button.getAttribute('data-consent');
      if(action==='accept'){saveChoice(true);hide();}
      if(action==='reject'){saveChoice(false);hide();}
      if(action==='settings')show('settings');
      if(action==='back')show('main');
      if(action==='save'){saveChoice(modal.querySelector('[data-consent-analytics]').checked);hide();}
    });
    settingsButton.addEventListener('click',function(){show('settings');});
    document.body.appendChild(modal);
    document.body.appendChild(settingsButton);
    if(savedChoice)modal.hidden=true;else show('main');
  }

  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',buildInterface);else buildInterface();
})();
