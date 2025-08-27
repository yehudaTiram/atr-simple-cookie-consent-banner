ATR Simple Cookie Consent Banner — Development Checkpoint

Current status (Aug 2025)
- Version: 2.0.1
- Admin notice shows Current Version with <br> formatting (EN + he_IL updated).
- WooCommerce checkbox: now controlled by either Global Form Integration or WooCommerce Integration (works as intended).
- Plugin action Settings link fixed to custom menu (admin.php?page=atr-simple-cookie-consent-banner).
- Tracking logic refactored previously; console.log dependency removed; window.scb.testTracking issue resolved.
- Packaging script (PowerShell) prepared previously; confirm before WP.org submission.

Open issue — CF7 checkbox missing when WooCommerce is active
- Symptom: On sites with Woo active, Contact Form 7 privacy checkbox does not appear. On sites without Woo, it appears.
- Finding: The wpcf7_form_elements filter sometimes receives a rendered fragment (no <form> wrapper), or content excludes the [submit …] token; in such cases, the simple str_replace('[submit', …) path fails.
- Current CF7 method: `add_privacy_checkbox_to_cf7` implements dual-path logic (string replace when [submit exists; fallback to DOM insertion before submit when only HTML is present). On some Woo sites, filter path still not affecting output (likely different render path/timing or block-based CF7 rendering).
- No consistent console errors when Woo is active; issue appears to be hook timing/content rather than runtime JS error.

What to try next (when resuming)
1) Ensure registration uses the filter with late priority
   - In `init_hooks()`:
     - add_filter('wpcf7_form_elements', array($this, 'add_privacy_checkbox_to_cf7'), 99);
   - If needed, temporarily also test:
     - add_filter('wpcf7_form', array($this, 'add_privacy_checkbox_to_cf7'), 99);
     - add_filter('wpcf7_contact_form', array($this, 'add_privacy_checkbox_to_cf7'), 99);

2) Verify the filter actually fires on the problematic site
   - Temporarily prepend marker inside CF7 method:
     - $content = "<!-- SCB CF7 MARKER -->\n" . $content;
   - Check page source for the marker.
   - Log diagnostics:
     - error_log('SCB CF7: len=' . strlen($content) . ' hasSubmitToken=' . (strpos($content,'[submit')!==false?'yes':'no') . ' hasSubmitInput=' . (stripos($content,'type="submit"')!==false?'yes':'no'));

3) If filter fires but no checkbox, confirm the fragment shape
   - When Woo is active, CF7 block may output only inner HTML (no <form>) and may include inline <style>. Avoid DOMDocument truncation by preferring regex/string injection before submit controls.
   - If both token and submit are missing, the page possibly loads submit later via JS → consider a JS-level fallback that runs on wpcf7 init events.

4) Optional JS fallback (only if needed)
   - On DOMContentLoaded and on CF7 init events, locate `.wpcf7-form` and inject checkbox before `input[type=submit], button[type=submit]` if not already present.
   - Guard against Woo checkout pages and payment iframes (reuse existing exclusion checks).

Files of interest
- atr-simple-cookie-consent-banner.php (defines hooks; forms initialized via class)
- includes/class-atr-simple-cookie-consent-banner-forms.php (CF7/Gravity/Ninja/Elementor + generic injection)
- admin/class-atr-simple-cookie-consent-banner-settings.php (admin notice + menu)
- languages/atr-simple-cookie-consent-banner-he_IL.po (notice string updated with <br> and version placeholder)

Environment notes (provide when resuming)
- WordPress version, CF7 version, WooCommerce version.
- Theme name/version; performance/caching plugins active.
- Problem page URL (CF7 page) and whether it’s block-based or shortcode CF7.

Submission prep for WordPress.org (pending)
- Confirm plugin headers and text-domain across all files.
- Ensure no vendor-only or development files in the release zip (README.txt for WP.org, stable tag, assets banner/icon if any).
- Confirm POT regeneration and PO/MO compile; verify Poedit compatibility.
- Re-run basic testing guide (consent flows; form checkbox on CF7/Gravity/Ninja/Elementor; Woo checkout unaffected; payment iframes excluded).

How to resume this chat/context later
- Keep this DEV-NOTES.md up to date and committed.
- When you return, open this file and share the latest notes here, or tell the assistant: “Resume from DEV-NOTES.md; last commit <hash>”.
- Optionally tag the repo now: `prep-wp-repo-checkpoint` so we can reference the exact state.

Immediate TODO checklist (next working session)
- [ ] Ensure CF7 filter uses add_filter with priority 99 and is active.
- [ ] Insert temporary SCB CF7 marker and confirm it appears on the problematic site.
- [ ] Capture log of hasSubmitToken/hasSubmitInput on the problematic site with Woo active.
- [ ] If filter runs and hasSubmitInput is true, confirm the regex injection path; otherwise plan JS fallback.
- [ ] Remove temporary diagnostics once resolved.


