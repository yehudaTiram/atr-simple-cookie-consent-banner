/** @format */

// scb-script.js - Vanilla JS cookie consent
(function () {
  const name = (window.scbSettings && scbSettings.cookieName) || "scb_consent";
  const expiryDays = (window.scbSettings && scbSettings.expiryDays) || 365;

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
      if (v) return JSON.parse(v);
    } catch (e) {}
    // fallback read cookie
    const match = document.cookie.match(
      new RegExp("(^| )" + name + "=([^;]+)")
    );
    if (match) {
      try {
        return JSON.parse(decodeURIComponent(match[2]));
      } catch (e) {}
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

  // On load: if consent exists, auto-activate
  document.addEventListener("DOMContentLoaded", function () {
    const banner = document.getElementById("scb-banner");
    const overlay = document.getElementById("scb-overlay");
    const settings = document.getElementById("scb-settings");
    const form = document.getElementById("scb-form");

    function hideBanner() {
      if (banner) banner.style.display = "none";
      if (overlay) overlay.style.display = "none";
      if (settings) settings.hidden = true;
    }

    const stored = getConsent();
    if (stored) {
      // activate consented categories
      if (stored.analytics) activateDataScripts("analytics");
      if (stored.marketing) activateDataScripts("marketing");
      hideBanner();
      return;
    }

    // no consent yet -> show banner
    if (banner) banner.style.display = "block";
    if (overlay) overlay.style.display = "block";

    // controls
    const btnAcceptAll = document.getElementById("scb-btn-accept-all");
    const btnReject = document.getElementById("scb-btn-reject");
    const btnCustom = document.getElementById("scb-btn-custom");
    const btnCancel = document.getElementById("scb-btn-cancel");

    btnAcceptAll &&
      btnAcceptAll.addEventListener("click", function () {
        const c = {
          essential: true,
          analytics: true,
          marketing: true,
          ts: Date.now(),
        };
        saveConsent(c);
        activateDataScripts("analytics");
        activateDataScripts("marketing");
        hideBanner();
      });

    btnReject &&
      btnReject.addEventListener("click", function () {
        const c = {
          essential: true,
          analytics: false,
          marketing: false,
          ts: Date.now(),
        };
        saveConsent(c);
        hideBanner();
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
        hideBanner();
      });
    }
  });

  // expose for debugging / admin
  window.scb = {
    getConsent,
    saveConsent,
    consentGivenFor,
    activateDataScripts,
  };
})();
