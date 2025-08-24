/** @format */

// scb-script.js - Vanilla JS cookie consent
(function () {
  const name = (window.scbSettings && scbSettings.cookieName) || "scb_consent";
  const expiryDays = (window.scbSettings && scbSettings.expiryDays) || 365;
  const isPrivacyPage = (window.scbSettings && scbSettings.isPrivacyPage) || false;

  // Prevent multiple initializations
  if (window.scbInitialized) return;
  window.scbInitialized = true;

  function saveConsent(obj) {
    try {
      localStorage.setItem(name, JSON.stringify(obj));
    } catch (e) {
      // fallback to cookie
      const v = encodeURIComponent(JSON.stringify(obj));
      document.cookie =
        name + "=" + v + ";path=/;max-age=" + expiryDays * 24 * 60 * 60;
    }
  }

  function getConsent() {
    try {
      const v = localStorage.getItem(name);
      if (v) {
        const parsed = JSON.parse(v);
        // Validate that consent has required fields
        if (parsed && typeof parsed === 'object' && parsed.essential === true) {
          return parsed;
        }
      }
    } catch (e) {
      console.warn('SCB Debug - Error reading localStorage:', e);
    }
    
    // fallback read cookie
    try {
      const match = document.cookie.match(
        new RegExp("(^| )" + name + "=([^;]+)")
      );
      if (match) {
        const parsed = JSON.parse(decodeURIComponent(match[2]));
        // Validate that consent has required fields
        if (parsed && typeof parsed === 'object' && parsed.essential === true) {
          return parsed;
        }
      }
    } catch (e) {
      console.warn('SCB Debug - Error reading cookie:', e);
    }
    
    return null;
  }

  function consentGivenFor(key) {
    const c = getConsent();
    return c && c[key] === true;
  }

  // Replace <script type="text/plain" data-consent="analytics" src="..."></script>
  function activateDataScripts(type) {
    const nodes = document.querySelectorAll(
      'script[type="text/plain"][data-consent="' + type + '"]'
    );
    nodes.forEach((n) => {
      const s = document.createElement("script");
      if (n.src) s.src = n.src;
      if (n.textContent) s.textContent = n.textContent;
      // copy attributes except type
      for (let i = 0; i < n.attributes.length; i++) {
        const a = n.attributes[i];
        if (a.name === "type" || a.name === "data-consent") continue;
        s.setAttribute(a.name, a.value);
      }
      n.parentNode.replaceChild(s, n);
    });
    
    // Remove blocking for this type
    removeTrackingBlocking(type);
    
    // Restore tracking functions when analytics or marketing consent is given
    if (type === 'analytics' || type === 'marketing') {
      restoreTrackingFunctions();
    }
    
    // Special handling for GTM - restore any blocked GTM scripts
    if (type === 'analytics') {
      restoreGTMScripts();
    }
  }
  
  // Restore GTM scripts that were blocked
  function restoreGTMScripts() {
    // Find all GTM scripts that were blocked
    const blockedGTMScripts = document.querySelectorAll('script[src*="googletagmanager"], script[src*="gtm.js"]');
    blockedGTMScripts.forEach(script => {
      if (script.src && script.src.includes('data:text/javascript')) {
        const originalSrc = script.getAttribute('data-original-src');
        if (originalSrc) {
          script.src = originalSrc;
          
          // Trigger GTM initialization if it's the main GTM script
          if (originalSrc.includes('gtm.js')) {
            setTimeout(() => {
            }, 100);
          }
        }
      }
    });
  }
  
  // Remove tracking script blocking after consent
  function removeTrackingBlocking(type) {
    
    if (type === 'analytics') {
      // Remove all blocking for analytics
      
      // Reset override flags to allow re-blocking if needed
      if (window._scbCreateElementOverridden) {
        window._scbCreateElementOverridden = false;
      }
      if (window._scbCreateElementNSOverridden) {
        window._scbCreateElementNSOverridden = false;
      }
      if (window._scbImgBlockingOverridden) {
        window._scbImgBlockingOverridden = false;
      }
      if (window._scbXHROverridden) {
        window._scbXHROverridden = false;
      }
      if (window._scbFetchOverridden) {
        window._scbFetchOverridden = false;
      }
      
      // Restore original DOM methods for analytics
      if (window._scbOriginalCreateElement) {
        document.createElement = window._scbOriginalCreateElement;
      }
      
      // Restore original tracking functions
      if (window.gtag && window.gtag._scbBlocked) {
        if (window.gtag._original) {
          window.gtag = window.gtag._original;
        } else {
          delete window.gtag;
        }
      }
      
      if (window.ga && window.ga._scbBlocked) {
        if (window.ga._original) {
          window.ga = window.ga._original;
        } else {
          delete window.ga;
        }
      }
      
      if (window.dataLayer && window.dataLayer.push && window.dataLayer.push._scbBlocked) {
        if (window.dataLayer._originalPush) {
          window.dataLayer.push = window.dataLayer._originalPush;
        }
      }
      
      // Special handling for GTM
      const gtmScripts = document.querySelectorAll('script[src*="googletagmanager"], script[src*="gtm.js"]');
      gtmScripts.forEach(script => {
        if (script.src && script.src.includes('data:text/javascript')) {
          // Restore original GTM script
          const originalSrc = script.getAttribute('data-original-src') || script.src.replace('data:text/javascript,console.log("SCB: Script blocked - no consent given")', '');
          if (originalSrc && !originalSrc.includes('data:text/javascript')) {
            script.src = originalSrc;
          }
        }
      });
      
    }
    
    if (type === 'marketing') {
      // Remove marketing blocking
      
      if (window.fbq && window.fbq._scbBlocked) {
        if (window.fbq._original) {
          window.fbq = window.fbq._original;
        } else {
          delete window.fbq;
        }
      }
      
      if (window._gaq && window._gaq.push && window._gaq.push.toString().includes('SCB: _gaq.push() blocked')) {
        if (window._gaq._originalPush) {
          window._gaq.push = window._gaq._originalPush;
        }
      }
      
    }
    
    // Force remove all blocking completely
    
    // Remove all override flags
    delete window._scbCreateElementOverridden;
    delete window._scbCreateElementNSOverridden;
    delete window._scbImgBlockingOverridden;
    delete window._scbXHROverridden;
    delete window._scbFetchOverridden;
    
    // Restore all blocked functions to their originals
    if (window.gtag && window.gtag._scbBlocked) {
      if (window.gtag._original) {
        window.gtag = window.gtag._original;
      } else {
        delete window.gtag;
      }
    }
    if (window.ga && window.ga._scbBlocked) {
      if (window.ga._original) {
        window.ga = window.ga._original;
      } else {
        delete window.ga;
      }
    }
    if (window.fbq && window.fbq._scbBlocked) {
      if (window.fbq._original) {
        window.fbq = window.fbq._original;
      } else {
        delete window.fbq;
      }
    }
    
  }

  function showBanner() {
    const banner = document.getElementById("scb-banner");
    const overlay = document.getElementById("scb-overlay");
    
    if (banner && overlay) {
      // Check if consent is already given - don't show banner if it is
      const currentConsent = getConsent();
      if (currentConsent) {
        return;
      }
      
      // On privacy policy page, don't block content completely
      if (isPrivacyPage) {
        overlay.classList.add('visible', 'privacy-page');
        banner.classList.add('visible');
      } else {
        // Add body class to prevent scroll on other pages
        document.body.classList.add('scb-open');
        overlay.classList.add('visible');
        banner.classList.add('visible');
      }
      
    } else {
      console.error('Banner or overlay elements not found!');
      console.error('Banner:', banner);
      console.error('Overlay:', overlay);
    }
  }

  function hideBanner() {
    const banner = document.getElementById("scb-banner");
    const overlay = document.getElementById("scb-overlay");
    const settings = document.getElementById("scb-settings");
    
    if (banner && overlay) {
      // Remove body class to restore scroll
      document.body.classList.remove('scb-open');
      
      // Hide overlay and banner with smooth transitions
      overlay.classList.remove('visible', 'privacy-page');
      banner.classList.remove('visible');
      
      // Hide settings form
      if (settings) settings.classList.remove('visible');
    }
  }

  // Global function for the close button onclick
  window.scbCloseModal = function() {
    hideBanner();
  };

  function setLoadingState(loading) {
    const banner = document.getElementById("scb-banner");
    if (banner) {
      if (loading) {
        banner.classList.add('loading');
      } else {
        banner.classList.remove('loading');
      }
    }
  }

  // Initialize the banner
  function initBanner() {
    
    const banner = document.getElementById("scb-banner");
    const overlay = document.getElementById("scb-overlay");
    const settings = document.getElementById("scb-settings");
    const form = document.getElementById("scb-form");

    // Check if banner elements exist
    if (!banner || !overlay) {
      console.error('Cookie consent banner elements not found!');
      return;
    }
    
    // Check consent immediately and return if already given
    const stored = getConsent();
    
    if (stored) {
      // User has already given consent - activate scripts and don't show banner
      
      // Ensure banner is hidden
      banner.classList.remove('visible');
      overlay.classList.remove('visible', 'privacy-page');
      document.body.classList.remove('scb-open');
      
      if (stored.analytics) activateDataScripts("analytics");
      if (stored.marketing) activateDataScripts("marketing");
      
      // Restore tracking functions if consent was already given
      if (stored.analytics || stored.marketing) {
        restoreTrackingFunctions();
      }
      return;
    }

    // On privacy policy page, show banner but don't block content
    if (isPrivacyPage) {
      // Add a note about temporary access
      const privacyNote = document.createElement('div');
      privacyNote.className = 'scb-privacy-note';
      privacyNote.innerHTML = '<small>' + (window.scbSettings.privacyNoteText || '💡 You can read this page while deciding about cookies') + '</small>';
      
      const content = banner.querySelector('.scb-content');
      const text = banner.querySelector('.scb-text');
      if (content && text) {
        content.insertBefore(privacyNote, text);
      }
    }

    // No consent yet - show banner immediately to prevent flash
    showBanner();

    // controls
    const btnAcceptAll = document.getElementById("scb-btn-accept-all");
    const btnReject = document.getElementById("scb-btn-reject");
    const btnCustom = document.getElementById("scb-btn-custom");
    const btnCancel = document.getElementById("scb-btn-cancel");

    btnAcceptAll &&
      btnAcceptAll.addEventListener("click", function () {
        setLoadingState(true);
        
        const c = {
          essential: true,
          analytics: true,
          marketing: true,
          ts: Date.now(),
        };
        
                 saveConsent(c);
         
         // Set a flag to indicate consent has been given - make it more reliable
         const consentFlag = 'scb_consent_given=1; path=/; max-age=' + (expiryDays * 24 * 60 * 60) + '; SameSite=Lax';
         document.cookie = consentFlag;
         
         // Also set a session cookie for immediate effect
         document.cookie = 'scb_consent_given=1; path=/; SameSite=Lax';
         
         // Activate scripts first
         activateDataScripts("analytics");
         activateDataScripts("marketing");
         
         // Restore tracking functions
         restoreTrackingFunctions();
         
         setTimeout(() => {
           hideBanner();
           setLoadingState(false);
           
           // Reload page after a short delay to ensure blocking is completely removed
           setTimeout(() => {
             window.location.reload();
           }, 1000);
         }, 300);
      });

    btnReject &&
      btnReject.addEventListener("click", function () {
        setLoadingState(true);
        
        const c = {
          essential: true,
          analytics: false,
          marketing: false,
          ts: Date.now(),
        };
        
        saveConsent(c);
        
        setTimeout(() => {
          hideBanner();
          setLoadingState(false);
        }, 300);
      });

    btnCustom &&
      btnCustom.addEventListener("click", function () {
        if (settings) {
          settings.classList.toggle('visible');
        }
      });

    btnCancel &&
      btnCancel.addEventListener("click", function () {
        if (settings) settings.classList.remove('visible');
      });

    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        setLoadingState(true);
        
        const formd = new FormData(form);
        const c = {
          essential: true,
          analytics:
            formd.get("analytics") === "on" ||
            formd.get("analytics") === "true",
          marketing:
            formd.get("marketing") === "on" ||
            formd.get("marketing") === "true",
          ts: Date.now(),
        };
        
        saveConsent(c);
        if (c.analytics) activateDataScripts("analytics");
        if (c.marketing) activateDataScripts("marketing");
        
        // Restore tracking functions if analytics or marketing consent given
        if (c.analytics || c.marketing) {
          restoreTrackingFunctions();
        }
        
        setTimeout(() => {
          hideBanner();
          setLoadingState(false);
        }, 300);
      });
    }

    // Close banner when clicking outside (mobile-friendly)
    overlay.addEventListener("click", function(e) {
      if (e.target === overlay) {
        hideBanner();
      }
    });

    // Close banner with Escape key
    document.addEventListener("keydown", function(e) {
      if (e.key === "Escape") {
        hideBanner();
      }
    });
  }

  // Wait for DOM to be ready, but also check if elements are already there
  function ready() {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", initBanner);
    } else {
      // DOM is already ready
      initBanner();
    }
  }

  // Start initialization with a small delay to ensure everything is loaded
  setTimeout(ready, 50);
  
  // Check if consent is already given before setting up blocking
  const initialConsent = getConsent();
  const shouldBlock = !initialConsent || (!initialConsent.analytics && !initialConsent.marketing);
  
  // Simple and effective tracking function blocking - wait for scripts to load first
  function setupTrackingBlocking() {
    // Check if consent has already been given - don't block if it has
    const currentConsent = getConsent();
    if (currentConsent && (currentConsent.analytics || currentConsent.marketing)) {
      console.log('SCB: Consent already given, skipping tracking function blocking');
      return;
    }
    
    console.log('SCB: Setting up tracking function blocking...');
    
    // Block gtag
    if (window.gtag) {
      if (window.gtag && !window.gtag._scbBlocked) {
        window.gtag._original = window.gtag;
        window.gtag._scbBlocked = true;
        window.gtag = function() {
          console.log('SCB: gtag() blocked - no consent given');
          return false;
        };
        console.log('SCB: gtag() function blocked');
      }
    } else {
      window.gtag = function() {
        console.log('SCB: gtag() blocked - no consent given');
        return false;
      };
      window.gtag._scbBlocked = true;
      console.log('SCB: gtag() function created and blocked');
    }
    
    // Block ga
    if (window.ga) {
      if (window.ga && !window.ga._scbBlocked) {
        window.ga._original = window.ga;
        window.ga._scbBlocked = true;
        window.ga = function() {
          console.log('SCB: ga() blocked - no consent given');
          return false;
        };
        console.log('SCB: ga() function blocked');
      }
    } else {
      window.ga = function() {
        console.log('SCB: ga() blocked - no consent given');
        return false;
      };
      window.ga._scbBlocked = true;
      console.log('SCB: ga() function created and blocked');
    }
    
    // Block fbq
    if (window.fbq) {
      if (window.fbq && !window.fbq._scbBlocked) {
        window.fbq._original = window.fbq;
        window.fbq._scbBlocked = true;
        window.fbq = function() {
          console.log('SCB: fbq() blocked - no consent given');
          return false;
        };
        console.log('SCB: fbq() function created and blocked');
      }
    } else {
      window.fbq = function() {
        console.log('SCB: fbq() blocked - no consent given');
        return false;
      };
      window.fbq._scbBlocked = true;
      console.log('SCB: fbq() function created and blocked');
    }
    
    // Block dataLayer.push
    if (window.dataLayer) {
      if (window.dataLayer && window.dataLayer.push && !window.dataLayer.push._scbBlocked) {
        window.dataLayer._originalPush = window.dataLayer.push;
        window.dataLayer.push._scbBlocked = true;
        window.dataLayer.push = function() {
          console.log('SCB: dataLayer.push() blocked - no consent given');
          return false;
        };
        console.log('SCB: dataLayer.push() function blocked');
      }
    }
    
    console.log('SCB: Tracking function blocking completed');
  }

  // Restore original tracking functions when consent is given
  function restoreTrackingFunctions() {
    console.log('SCB: Restoring tracking functions...');
    
    // Restore gtag
    if (window.gtag && window.gtag._original) {
      window.gtag = window.gtag._original;
      console.log('SCB: gtag() function restored');
    }
    
    // Restore ga
    if (window.ga && window.ga._original) {
      window.ga = window.ga._original;
      console.log('SCB: ga() function restored');
    }
    
    // Restore fbq
    if (window.fbq && window.fbq._original) {
      window.fbq = window.fbq._original;
      console.log('SCB: fbq() function restored');
    }
    
    // Restore dataLayer.push
    if (window.dataLayer && window.dataLayer._originalPush) {
      window.dataLayer.push = window.dataLayer._originalPush;
      console.log('SCB: dataLayer.push() function restored');
    }
    
    console.log('SCB: Tracking functions restoration completed');
  }
  
  // Block tracking functions immediately to prevent network requests
  // Then re-block after scripts load to ensure complete coverage
  if (shouldBlock) {
    console.log('SCB: Blocking tracking functions immediately...');
    setupTrackingBlocking();
    
    // Also block after a delay to catch any late-loading scripts
    setTimeout(() => {
      // Check if consent has already been given - don't re-block if it has
      const currentConsent = getConsent();
      if (currentConsent && (currentConsent.analytics || currentConsent.marketing)) {
        console.log('SCB: Consent already given, skipping delayed blocking');
        return;
      }
      
      console.log('SCB: Re-blocking to catch late-loading scripts...');
      setupTrackingBlocking();
      
      // Test blocking after setup
      setTimeout(() => {
        if (window.scb && window.scb.testTracking) {
          console.log('SCB: Testing tracking blocking after setup...');
          window.scb.testTracking();
        }
      }, 500);
    }, 2000); // Wait 2 seconds for any late scripts
  } else {
    console.log('SCB: Consent already given, skipping initial tracking function blocking');
  }

  // expose for debugging / admin
  window.scb = {
    getConsent,
    saveConsent,
    consentGivenFor,
    activateDataScripts,
    showBanner,
    hideBanner,
    restoreTrackingFunctions,
    clearConsent: function() {
      try {
        localStorage.removeItem(name);
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        location.reload(); // Reload to test banner again
      } catch (e) {
        console.error('Error clearing consent:', e);
      }
    },
         forceShow: function() {
       showBanner();
     },
           testTracking: function() {
        console.log('Testing tracking functionality...');
        console.log('gtag available:', typeof window.gtag === 'function');
        console.log('ga available:', typeof window.ga === 'function');
        console.log('dataLayer available:', window.dataLayer);
        console.log('fbq available:', typeof window.fbq === 'function');
        
        // Test if functions are actually blocked
        console.log('--- Testing if functions are blocked ---');
        if (typeof window.gtag === 'function') {
          const gtagResult = window.gtag('event', 'test', { event_category: 'debug', event_label: 'consent_test' });
          console.log('gtag test result:', gtagResult);
        }
        if (typeof window.ga === 'function') {
          const gaResult = window.ga('send', 'event', 'test', 'consent_test');
          console.log('ga test result:', gaResult);
        }
        if (typeof window.fbq === 'function') {
          const fbqResult = window.fbq('track', 'test', { event_category: 'debug', event_label: 'consent_test' });
          console.log('fbq test result:', fbqResult);
        }
        
        // Check if functions are our blocked versions
        console.log('--- Function blocking status ---');
        if (window.gtag) {
          console.log('gtag is blocked:', window.gtag.toString().includes('SCB: gtag() blocked'));
        }
        if (window.ga) {
          console.log('ga is blocked:', window.ga.toString().includes('SCB: ga() blocked'));
        }
        if (window.fbq) {
          console.log('fbq is blocked:', window.fbq.toString().includes('SCB: fbq() blocked'));
        }
      },
      
      // Check if tracking functions are currently blocked
      isTrackingBlocked: function() {
        const status = {
          gtag: window.gtag && window.gtag.toString().includes('SCB: gtag() blocked'),
          ga: window.ga && window.ga.toString().includes('SCB: ga() blocked'),
          fbq: window.fbq && window.fbq.toString().includes('SCB: fbq() blocked'),
          dataLayer: window.dataLayer && window.dataLayer.push && window.dataLayer.push.toString().includes('SCB: dataLayer.push() blocked')
        };
        
        console.log('--- Tracking Function Blocking Status ---');
        console.log('gtag blocked:', status.gtag);
        console.log('ga blocked:', status.ga);
        console.log('fbq blocked:', status.fbq);
        console.log('dataLayer.push blocked:', status.dataLayer);
        
        return status;
      },
      
      debugConsent: function() {
        console.log('Current consent status:');
        console.log('scb_consent cookie:', document.cookie.match(/scb_consent=([^;]+)/));
        console.log('scb_consent_given cookie:', document.cookie.match(/scb_consent_given=([^;]+)/));
        console.log('localStorage consent:', localStorage.getItem('scb_consent'));
        console.log('getConsent() result:', getConsent());
        console.log('Blocking flags:', {
          createElement: window._scbCreateElementOverridden,
          createElementNS: window._scbCreateElementNSOverridden,
          imgBlocking: window._scbImgBlockingOverridden,
          xhr: window._scbXHROverridden,
          fetch: window._scbFetchOverridden
        });
      },
      debugGTM: function() {
        console.log('GTM Detection:');
        console.log('GTM scripts found:', document.querySelectorAll('script[src*="googletagmanager"], script[src*="gtm.js"]'));
        console.log('Blocked GTM scripts:', document.querySelectorAll('script[src*="googletagmanager"][src*="data:text/javascript"], script[src*="gtm.js"][src*="data:text/javascript"]'));
        console.log('GTM scripts with original sources:', document.querySelectorAll('script[data-original-src*="googletagmanager"], script[data-original-src*="gtm.js"]'));
        console.log('dataLayer available:', window.dataLayer);
        console.log('gtag available:', typeof window.gtag === 'function');
      },
      testConsentCookies: function() {
        console.log('Testing consent cookies...');
        console.log('All cookies:', document.cookie);
        console.log('scb_consent_given:', document.cookie.match(/scb_consent_given=([^;]+)/));
        console.log('scb_consent:', document.cookie.match(/scb_consent=([^;]+)/));
        console.log('localStorage consent:', localStorage.getItem('scb_consent'));
        
        // Test if we can set cookies
        try {
          document.cookie = 'scb_test_cookie=test; path=/; SameSite=Lax';
          const testCookie = document.cookie.match(/scb_test_cookie=([^;]+)/);
          
          // Clean up test cookie
          document.cookie = 'scb_test_cookie=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        } catch (e) {
          console.error('Error setting test cookie:', e);
        }
      }
  };
})();

