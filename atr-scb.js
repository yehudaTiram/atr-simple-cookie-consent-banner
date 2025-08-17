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
  }

  function showBanner() {
    const banner = document.getElementById("scb-banner");
    const overlay = document.getElementById("scb-overlay");
    
    if (banner && overlay) {
      // Check if consent is already given - don't show banner if it is
      const currentConsent = getConsent();
      if (currentConsent) {
        console.log('SCB Debug - showBanner called but consent already given, not showing');
        return;
      }
      
      // Debug: Log what's happening
      console.log('SCB Debug - showBanner called, isPrivacyPage:', isPrivacyPage);
      console.log('SCB Debug - Current consent:', currentConsent);
      
      // On privacy policy page, don't block content completely
      if (isPrivacyPage) {
        console.log('SCB Debug - Adding privacy-page class');
        overlay.classList.add('visible', 'privacy-page');
        banner.classList.add('visible');
      } else {
        console.log('SCB Debug - Adding regular banner classes');
        // Add body class to prevent scroll on other pages
        document.body.classList.add('scb-open');
        overlay.classList.add('visible');
        banner.classList.add('visible');
      }
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
      
      if (settings) settings.hidden = true;
    }
  }

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

    // Debug: Log privacy page detection
    console.log('SCB Debug - isPrivacyPage:', isPrivacyPage);
    console.log('SCB Debug - scbSettings:', window.scbSettings);

    // Check if banner elements exist
    if (!banner || !overlay) {
      console.warn('Cookie consent banner elements not found');
      return;
    }

    // Check consent immediately and return if already given
    const stored = getConsent();
    console.log('SCB Debug - Stored consent:', stored);
    
    if (stored) {
      // User has already given consent - activate scripts and don't show banner
      console.log('SCB Debug - User already consented, not showing banner');
      
      // Ensure banner is hidden
      banner.classList.remove('visible');
      overlay.classList.remove('visible', 'privacy-page');
      document.body.classList.remove('scb-open');
      
      if (stored.analytics) activateDataScripts("analytics");
      if (stored.marketing) activateDataScripts("marketing");
      return;
    }

    // On privacy policy page, show banner but don't block content
    if (isPrivacyPage) {
      // Add a note about temporary access
      const privacyNote = document.createElement('div');
      privacyNote.className = 'scb-privacy-note';
      privacyNote.innerHTML = '<small>💡 You can read this page while deciding about cookies</small>';
      
      const content = banner.querySelector('.scb-content');
      const text = banner.querySelector('.scb-text');
      if (content && text) {
        content.insertBefore(privacyNote, text);
      }
    }

    // No consent yet - show banner immediately to prevent flash
    console.log('SCB Debug - No consent found, showing banner immediately');
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
        activateDataScripts("analytics");
        activateDataScripts("marketing");
        
        setTimeout(() => {
          hideBanner();
          setLoadingState(false);
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
          settings.hidden = !settings.hidden;
        }
      });

    btnCancel &&
      btnCancel.addEventListener("click", function () {
        if (settings) settings.hidden = true;
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
        
        setTimeout(() => {
          hideBanner();
          setLoadingState(false);
        }, 300);
      });
    }

    // Close banner when clicking outside (mobile-friendly)
    overlay.addEventListener("click", function(e) {
      console.log('SCB Debug - Overlay clicked, target:', e.target, 'overlay:', overlay);
      if (e.target === overlay) {
        console.log('SCB Debug - Closing banner via overlay click');
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

  // expose for debugging / admin
  window.scb = {
    getConsent,
    saveConsent,
    consentGivenFor,
    activateDataScripts,
    showBanner,
    hideBanner,
    clearConsent: function() {
      try {
        localStorage.removeItem(name);
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        console.log('SCB Debug - Consent cleared');
        location.reload(); // Reload to test banner again
      } catch (e) {
        console.error('SCB Debug - Error clearing consent:', e);
      }
    },
    forceShow: function() {
      console.log('SCB Debug - Force showing banner');
      showBanner();
    }
  };
})();
