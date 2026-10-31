(()=> {
  const $=(selector,context=document)=>context.querySelector(selector);
  const $$=(selector,context=document)=>[...context.querySelectorAll(selector)];
  const navWrap=$('#navWrap');
  const menuBtn=$('#menuBtn');
  const navigation=$('.nav-official');
  const siteHeader=$('.site-header');
  const mobileQuery=matchMedia('(max-width: 1050px)');
  const hoverQuery=matchMedia('(hover: hover) and (pointer: fine)');
  const reducedMotionQuery=matchMedia('(prefers-reduced-motion: reduce)');
  let scrollLock=null;
  let temporarilyInert=[];

  siteHeader?.classList.add('menu-ready');

  const menuName=toggle=>{
    if(toggle.dataset.menuName) return toggle.dataset.menuName;
    const label=toggle.getAttribute('aria-label')||'';
    const fallback=toggle.closest('.mega')?.querySelector('.mega-main-link')?.textContent?.trim()||'navigation';
    toggle.dataset.menuName=label.replace(/^(Ouvrir|Fermer) le sous-menu\s+/i,'').trim()||fallback;
    return toggle.dataset.menuName;
  };

  const setMegaState=(mega,open)=>{
    const toggle=mega.querySelector(':scope > .mega-nav-head > .mega-trigger');
    const panel=mega.querySelector(':scope > .mega-panel');
    mega.classList.toggle('open-menu',open);
    if(toggle){
      toggle.setAttribute('aria-expanded',open?'true':'false');
      toggle.setAttribute('aria-label',`${open?'Fermer':'Ouvrir'} le sous-menu ${menuName(toggle)}`);
    }
    if(panel) panel.setAttribute('aria-hidden',open?'false':'true');
  };

  const closeMegas=(except=null)=>{
    $$('.mega.open-menu').forEach(mega=>{
      if(mega!==except) setMegaState(mega,false);
    });
  };

  const syncMobileNavPosition=()=>{
    if(!navWrap||!siteHeader||!mobileQuery.matches) return;
    const headerRect=siteHeader.getBoundingClientRect();
    const wrapRect=navWrap.getBoundingClientRect();
    const viewportWidth=document.documentElement.clientWidth;
    const viewportHeight=window.visualViewport?.height||window.innerHeight;
    const viewportTop=window.visualViewport?.offsetTop||0;
    const visibleBottom=viewportTop+viewportHeight;
    navWrap.style.setProperty('--mobile-nav-offset',`${Math.max(0,Math.round(headerRect.bottom-wrapRect.top))}px`);
    navWrap.style.setProperty('--mobile-nav-height',`${Math.max(0,Math.round(visibleBottom-headerRect.bottom))}px`);
    navWrap.style.setProperty('--mobile-nav-right',`${Math.max(0,Math.round(viewportWidth-wrapRect.right))}px`);
    navWrap.style.setProperty('--mobile-nav-left',`${Math.max(0,Math.round(wrapRect.left))}px`);
    navWrap.style.setProperty('--mobile-viewport-width',`${viewportWidth}px`);
  };

  const setElementInert=(element)=>{
    if(!element) return;
    temporarilyInert.push({element,hadAttribute:element.hasAttribute('inert')});
    element.setAttribute('inert','');
  };

  const isolateMobileMenu=()=>{
    temporarilyInert=[];
    [...document.body.children].forEach(element=>{
      if(element!==siteHeader&&!['SCRIPT','STYLE'].includes(element.tagName)) setElementInert(element);
    });
    if(!navWrap) return;
    [...navWrap.children].forEach(element=>{
      if(element===navigation) return;
      if(element.contains(menuBtn)){
        [...element.children].forEach(child=>{
          if(child!==menuBtn&&!child.contains(menuBtn)) setElementInert(child);
        });
      }else{
        setElementInert(element);
      }
    });
  };

  const restorePageAccess=()=>{
    const records=temporarilyInert;
    temporarilyInert=[];
    records.forEach(({element,hadAttribute})=>{
      if(!hadAttribute) element.removeAttribute('inert');
    });
  };

  const lockPageScroll=()=>{
    if(scrollLock) return;
    const root=document.documentElement;
    const scrollbarWidth=Math.max(0,window.innerWidth-root.clientWidth);
    scrollLock={
      rootOverflow:root.style.overflow,
      bodyOverflow:document.body.style.overflow,
      bodyPaddingRight:document.body.style.paddingRight,
      bodyPaddingTop:document.body.style.paddingTop,
      scrollX:window.scrollX,
      scrollY:window.scrollY
    };
    const currentPaddingTop=parseFloat(getComputedStyle(document.body).paddingTop)||0;
    const headerHeight=siteHeader?.getBoundingClientRect().height||0;
    document.body.style.paddingTop=`${currentPaddingTop+headerHeight}px`;
    if(scrollbarWidth){
      const currentPadding=parseFloat(getComputedStyle(document.body).paddingRight)||0;
      document.body.style.paddingRight=`${currentPadding+scrollbarWidth}px`;
    }
    root.style.overflow='hidden';
    document.body.style.overflow='hidden';
    root.classList.add('menu-open');
    document.body.classList.add('menu-open');
  };

  const unlockPageScroll=()=>{
    if(!scrollLock) return;
    const root=document.documentElement;
    const saved=scrollLock;
    root.style.overflow=saved.rootOverflow;
    document.body.style.overflow=saved.bodyOverflow;
    document.body.style.paddingRight=saved.bodyPaddingRight;
    document.body.style.paddingTop=saved.bodyPaddingTop;
    root.classList.remove('menu-open');
    document.body.classList.remove('menu-open');
    scrollLock=null;
    const previousScrollBehavior=root.style.scrollBehavior;
    root.style.scrollBehavior='auto';
    window.scrollTo({left:saved.scrollX,top:saved.scrollY,behavior:'instant'});
    root.style.scrollBehavior=previousScrollBehavior;
  };

  const syncNavigationAccessibility=()=>{
    if(!navigation) return;
    if(mobileQuery.matches){
      navigation.setAttribute('aria-hidden',navWrap?.classList.contains('open')?'false':'true');
    }else{
      navigation.removeAttribute('aria-hidden');
    }
  };

  const clearMobilePosition=()=>{
    if(!navWrap) return;
    ['--mobile-nav-offset','--mobile-nav-height','--mobile-nav-right','--mobile-nav-left','--mobile-viewport-width']
      .forEach(property=>navWrap.style.removeProperty(property));
    siteHeader?.style.removeProperty('--mobile-header-top');
  };

  const pinMobileHeader=()=>{
    if(!siteHeader) return;
    const currentTop=Math.max(0,Math.round(siteHeader.getBoundingClientRect().top));
    siteHeader.style.setProperty('--mobile-header-top',`${currentTop}px`);
  };

  const closeMobileNav=({restoreFocus=false}={})=>{
    if(!navWrap||!menuBtn) return;
    const wasOpen=navWrap.classList.contains('open');
    navWrap.classList.remove('open');
    menuBtn.setAttribute('aria-expanded','false');
    menuBtn.setAttribute('aria-label','Ouvrir le menu');
    closeMegas();
    restorePageAccess();
    unlockPageScroll();
    clearMobilePosition();
    syncNavigationAccessibility();
    if(restoreFocus&&wasOpen&&mobileQuery.matches) menuBtn.focus({preventScroll:true});
  };

  const openMobileNav=()=>{
    if(!navWrap||!menuBtn||!navigation||!mobileQuery.matches) return;
    pinMobileHeader();
    lockPageScroll();
    isolateMobileMenu();
    navWrap.classList.add('open');
    menuBtn.setAttribute('aria-expanded','true');
    menuBtn.setAttribute('aria-label','Fermer le menu');
    navigation.setAttribute('aria-hidden','false');
    syncMobileNavPosition();
    navigation.scrollTop=0;
    menuBtn.focus({preventScroll:true});
    requestAnimationFrame(()=>{
      if(document.activeElement!==menuBtn) menuBtn.focus({preventScroll:true});
    });
  };

  if(menuBtn&&navWrap){
    if(navigation&&!navigation.id) navigation.id='primaryNavigation';
    if(navigation) menuBtn.setAttribute('aria-controls',navigation.id);
    menuBtn.setAttribute('aria-expanded','false');
    menuBtn.addEventListener('click',()=>{
      if(navWrap.classList.contains('open')) closeMobileNav({restoreFocus:true});
      else openMobileNav();
    });
  }

  $$('.mega').forEach((mega,index)=>{
    const toggle=mega.querySelector(':scope > .mega-nav-head > .mega-trigger');
    const panel=mega.querySelector(':scope > .mega-panel');
    if(!toggle||!panel) return;

    const currentMenuLink=
      mega.querySelector(':scope > .mega-nav-head > .mega-main-link[aria-current="page"]')||
      mega.querySelector(':scope > .mega-panel > .mega-col a[aria-current="page"]')||
      mega.querySelector(':scope > .mega-panel.mega-panel-about > a[aria-current="page"]');
    mega.classList.toggle('current-section',Boolean(currentMenuLink));

    if(!panel.id) panel.id=`mega-panel-${index+1}`;
    toggle.removeAttribute('aria-haspopup');
    toggle.setAttribute('aria-controls',panel.id);
    toggle.setAttribute('aria-expanded','false');
    toggle.setAttribute('aria-label',`Ouvrir le sous-menu ${menuName(toggle)}`);
    panel.setAttribute('aria-hidden','true');

    toggle.addEventListener('click',event=>{
      event.preventDefault();
      event.stopPropagation();
      const willOpen=!mega.classList.contains('open-menu');
      closeMegas(mega);
      setMegaState(mega,willOpen);
      if(willOpen&&mobileQuery.matches){
        requestAnimationFrame(()=>{
          const navRect=navigation?.getBoundingClientRect();
          const megaRect=mega.getBoundingClientRect();
          const headingHeight=navigation?.querySelector('.mobile-menu-heading')?.getBoundingClientRect().height||0;
          if(!navigation||!navRect) return;
          const targetTop=navigation.scrollTop+megaRect.top-navRect.top-headingHeight-8;
          navigation.scrollTo({
            top:Math.max(0,targetTop),
            behavior:reducedMotionQuery.matches?'auto':'smooth'
          });
        });
      }
    });

    mega.addEventListener('mouseenter',()=>{
      if(!mobileQuery.matches&&hoverQuery.matches){
        closeMegas(mega);
        setMegaState(mega,true);
      }
    });
    mega.addEventListener('mouseleave',()=>{
      if(!mobileQuery.matches&&hoverQuery.matches&&!mega.contains(document.activeElement)) setMegaState(mega,false);
    });
    mega.addEventListener('focusin',()=>{
      if(!mobileQuery.matches){
        closeMegas(mega);
        setMegaState(mega,true);
      }
    });
    mega.addEventListener('focusout',()=>{
      setTimeout(()=>{
        if(!mobileQuery.matches&&!mega.contains(document.activeElement)&&!mega.matches(':hover')) setMegaState(mega,false);
      },0);
    });
  });

  document.addEventListener('click',event=>{
    if(!event.target.closest('.mega')) closeMegas();
    if(mobileQuery.matches&&navWrap?.classList.contains('open')&&!event.target.closest('.nav-official')&&!event.target.closest('#menuBtn')){
      closeMobileNav({restoreFocus:true});
    }
  });

  $$('.faq-row').forEach((row,index)=>{
    const btn=row.querySelector('button');
    const answer=row.querySelector('.answer');
    if(!btn||!answer) return;
    if(!answer.id) answer.id=`faq-answer-${index+1}`;
    btn.setAttribute('aria-controls',answer.id);
    btn.setAttribute('aria-expanded',row.classList.contains('open')?'true':'false');
    btn.addEventListener('click',()=>{
      const open=row.classList.toggle('open');
      btn.setAttribute('aria-expanded',open?'true':'false');
    });
  });

  const footerCompactQuery=matchMedia('(max-width: 720px)');
  const footerToggles=$$('.footer-col-toggle');
  const setFooterColumn=(button,open)=>{
    const panel=document.getElementById(button.getAttribute('aria-controls'));
    if(!panel) return;
    const title=button.querySelector('span')?.textContent?.trim()||'la rubrique';
    button.setAttribute('aria-expanded',open?'true':'false');
    button.setAttribute('aria-label',`${open?'Fermer':'Ouvrir'} la rubrique ${title}`);
    panel.hidden=!open;
  };
  const syncFooterColumns=()=>{
    const compact=footerCompactQuery.matches;
    footerToggles.forEach(button=>{
      button.tabIndex=compact?0:-1;
      setFooterColumn(button,!compact);
    });
  };
  footerToggles.forEach(button=>button.addEventListener('click',()=>{
    if(!footerCompactQuery.matches) return;
    const willOpen=button.getAttribute('aria-expanded')!=='true';
    footerToggles.forEach(other=>setFooterColumn(other,false));
    if(willOpen) setFooterColumn(button,true);
  }));
  footerCompactQuery.addEventListener?.('change',syncFooterColumns);

  $$('.nav-official a,a[href^="#"]').forEach(link=>link.addEventListener('click',()=>{
    if(mobileQuery.matches) closeMobileNav();
  }));

  const visibleMenuControls=()=>{
    if(!navigation||!menuBtn) return [];
    const controls=[
      menuBtn,
      ...navigation.querySelectorAll('a[href],button:not([disabled]),[tabindex]:not([tabindex="-1"])')
    ];
    return controls.filter(node=>node.getClientRects().length&&getComputedStyle(node).visibility!=='hidden');
  };

  document.addEventListener('keydown',event=>{
    if(event.key==='Tab'&&mobileQuery.matches&&navWrap?.classList.contains('open')&&navigation){
      const focusable=visibleMenuControls();
      if(!focusable.length) return;
      const currentIndex=focusable.indexOf(document.activeElement);
      const nextIndex=currentIndex<0
        ? (event.shiftKey?focusable.length-1:0)
        : (currentIndex+(event.shiftKey?-1:1)+focusable.length)%focusable.length;
      event.preventDefault();
      focusable[nextIndex].focus();
      return;
    }
    if(event.key==='Escape'){
      const mobileWasOpen=mobileQuery.matches&&navWrap?.classList.contains('open');
      closeMegas();
      if(mobileWasOpen) closeMobileNav({restoreFocus:true});
      const dialog=document.querySelector('dialog[open]');
      if(dialog) dialog.close();
    }
  });

  const handleViewportChange=()=>{
    if(!navWrap?.classList.contains('open')) return;
    if(!mobileQuery.matches) closeMobileNav();
    else syncMobileNavPosition();
  };

  mobileQuery.addEventListener?.('change',()=>{
    if(!mobileQuery.matches) closeMobileNav();
    syncNavigationAccessibility();
  });
  addEventListener('resize',handleViewportChange,{passive:true});
  addEventListener('orientationchange',handleViewportChange,{passive:true});
  window.visualViewport?.addEventListener('resize',handleViewportChange,{passive:true});
  addEventListener('pagehide',()=>closeMobileNav(),{once:true});

  syncNavigationAccessibility();
  syncFooterColumns();
})();
