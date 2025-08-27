=== ATR Simple Cookie Consent Banner ===
Contributors: yehudaT
Donate link: https://www.paypal.com/donate/?cmd=_s-xclick&hosted_button_id=T6VTA75GTS3YA&ssrt=1736764301031
Tags: cookies, cookie consent, privacy, woocommerce, israel, gdpr
Requires at least: 5.0
Tested up to: 6.8.2
Requires PHP: 7.4
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple, flexible cookie consent banner tailored for Israeli websites (Privacy Protection Law Amendment 13). Includes consent management, WooCommerce and forms integration.

== Description ==

ATR Simple Cookie Consent Banner is built specifically for Israeli websites to help site owners address the Privacy Protection Law (Amendment 13). It provides a simple consent banner with categories (Essential, Analytics, Marketing), integrates with WooCommerce checkout privacy acceptance, and can inject a privacy policy checkbox into site‑wide forms (including common page builders).

Key features:

- Smart cookie consent banner with categories
- Privacy policy acceptance on WooCommerce checkout
- Global form integration: adds a privacy policy checkbox to most forms site‑wide
- Preferences dialog and granular consent
- Optional tracking script blocking hooks
- Customizable texts and styles
- Hebrew and English out of the box (i18n ready)

Important notes and disclaimer:

- This plugin is a technical aid only and does not constitute legal advice. Using this plugin does not by itself guarantee compliance with any law (including Israel’s Privacy Protection Law or GDPR). Consult your legal advisor for your specific needs.
- Use at your own risk. No warranties; no liability for damages.

Resources:

- Documentation (Hebrew): https://atarimtr.co.il/איך-להתאים-אתר-ווקומרס-woocommerce-לתיקון-13-לחו/
- Support/Contact: https://atarimtr.co.il/צרו-קשר/
- Developer site: https://atarimtr.co.il/

= Internationalization (i18n) =

- Text domain: `atr-simple-cookie-consent-banner`
- Domain path: `/languages`
- Hebrew translation is included. You can add more languages by creating PO/MO files in `languages/`.

== Installation ==

1. Upload the plugin ZIP via WordPress → Plugins → Add New → Upload Plugin, or extract to `wp-content/plugins/`.
2. Activate the plugin.
3. Go to Settings → Cookie Consent Banner to configure texts, behavior, and styling.
4. If using WooCommerce, verify the privacy acceptance appears and functions at checkout.

== Frequently Asked Questions ==

= The banner does not appear =
- Confirm the plugin is active.
- Open your site in a private/incognito window.
- Clear any page or server cache.
- Ensure your theme calls `wp_head()` and `wp_footer()`.

= How do I customize the banner texts and styles? =
- Go to Settings → Cookie Consent Banner. You can adjust texts and, in most themes, apply CSS to `.atr-scb-*` classes. Enqueued assets are `public/atr-scb.css` and `public/atr-scb.js`.

= Does it integrate with WooCommerce? =
- Yes. A privacy policy acceptance checkbox can be added to checkout and respects the plugin’s integration settings.

= Will this plugin make my site fully compliant with the law? =
- No. It’s a technical tool only. Legal compliance depends on your specific site, data practices, and legal advice.

= Is Hebrew supported? =
- Yes. Hebrew and English are included. Additional languages can be added via PO/MO files in `languages/`.

== Screenshots ==

1. Cookie consent banner with action buttons
2. Preferences dialog for granular consent
3. WooCommerce checkout privacy acceptance checkbox
4. Global form privacy policy checkbox example

== Changelog ==

= 2.0.1 - 2025-01-23 =
- Enhanced WooCommerce checkout and payment gateway detection
- Smarter global form detection and privacy checkbox injection
- JavaScript-side exclusions for checkout/payment forms and iframes
- WooCommerce checkbox now respects both Global Form Integration and WooCommerce Integration settings
- Fixed option name mismatch in WooCommerce class
- Added constructor parameter for plugin name consistency
- Initialization hook changed to `wp_loaded` for proper settings loading
- Updated documentation and testing guide

= 2.0.0 - 2025-01-23 =
- Migrated to a class-based architecture (WordPress Plugin Boilerplate)
- Comprehensive settings pages and improved consent management
- Improved banner display logic and tracking script blocking hooks
- Better internationalization support
- Global forms and WooCommerce integration improvements
- Numerous fixes for banner display, JS functions, HTML structure, and CSS

= 1.0.0 =
- Initial release with basic cookie consent banner
- Hebrew language support
- Initial compliance helpers for Israeli Privacy Protection Law

== Upgrade Notice ==

= 2.0.1 =
Recommended update. Improves WooCommerce and form detection logic, adds exclusions for payment flows, and fixes several issues for more reliable behavior.
