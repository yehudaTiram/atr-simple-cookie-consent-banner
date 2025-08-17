<?php
/*
 * Plugin Name: ATR Simple Cookie Consent Banner for Israeli web sites
 * Description: Cookie consent banner specifically designed for Israeli websites to comply with the 13th amendment of the Privacy Protection Law (תיקון 13 לחוק הגנת הפרטיות). Handles Essential, Analytics, and Marketing cookies with proper consent management. Suitable for all Israeli businesses and websites. Use at your own risk - no warranty or liability for damages.
 * Plugin URI:        https://atarimtr.co.il
 * Version:           1.0.2
 * Author:            Yehuda Tiram
 * Author URI:        https://atarimtr.co.il/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       atr-simple-cookie-consent-banner
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) exit;

// Plugin version - update this when making changes
define('SCB_VERSION', '1.0.2');

/* --- enqueue assets --- */
add_action('wp_enqueue_scripts', function () {
  wp_register_style('scb-style', plugins_url('atr-scb.css', __FILE__), [], SCB_VERSION);
  wp_register_script('scb-script', plugins_url('atr-scb.js', __FILE__), [], SCB_VERSION, true);

  wp_enqueue_style('scb-style');
  wp_enqueue_script('scb-script');

  // Check if we're on the privacy policy page
  $is_privacy_page = false;
  $privacy_policy_url = get_privacy_policy_url();
  
  // More robust privacy page detection
  if ($privacy_policy_url) {
    $current_url = get_permalink();
    $privacy_url_parts = parse_url($privacy_policy_url);
    $current_url_parts = parse_url($current_url);
    
    // Check if current page matches privacy policy URL
    if ($current_url === $privacy_policy_url || 
        (isset($privacy_url_parts['path']) && isset($current_url_parts['path']) && 
         $privacy_url_parts['path'] === $current_url_parts['path'])) {
      $is_privacy_page = true;
    }
  }

  // pass some settings to JS if needed
  wp_localize_script('scb-script', 'scbSettings', [
    'cookieName' => 'scb_consent',
    'expiryDays' => 365,
    'siteName' => get_bloginfo('name'),
    'isPrivacyPage' => $is_privacy_page,
    'privacyPolicyUrl' => $privacy_policy_url,
  ]);
});

/* --- inject banner HTML in footer --- */
add_action('wp_footer', function () {
?>
  <!-- Cookie Consent Banner (Injected by plugin) -->
  <div id="scb-overlay" aria-hidden="true"></div>

  <div id="scb-banner" role="dialog" aria-live="polite" aria-label="Cookie consent" aria-hidden="true">
    <div class="scb-content">
      <div class="scb-text">
        <strong><?php echo esc_html(get_bloginfo('name')); ?></strong>
        משתמשים בעוגיות כדי להבטיח תפקוד האתר ולשפר את חוויית המשתמש. אפשר לבחור אילו סוגי עוגיות להפעיל.
      </div>

      <div class="scb-controls">
        <button id="scb-btn-accept-all" class="scb-btn scb-btn-primary" type="button">
          <span class="scb-btn-text">קבל הכל</span>
          <span class="scb-btn-loading" style="display: none;">טוען...</span>
        </button>
        <button id="scb-btn-reject" class="scb-btn" type="button">
          <span class="scb-btn-text">הסר לא הכרחיות</span>
          <span class="scb-btn-loading" style="display: none;">טוען...</span>
        </button>
        <button id="scb-btn-custom" class="scb-btn" type="button">העדפות</button>
      </div>

      <div id="scb-settings" class="scb-settings" hidden>
        <form id="scb-form">
          <fieldset>
            <legend>בחירת עוגיות</legend>
            <label><input type="checkbox" name="essential" checked disabled> הכרחיות (נדרשות)</label><br>
            <label><input type="checkbox" name="analytics"> אנליטיקה (Google Analytics)</label><br>
            <label><input type="checkbox" name="marketing"> שיווק/פרסום (Facebook/Ads)</label>
          </fieldset>

          <div class="scb-actions">
            <button type="submit" class="scb-btn scb-btn-primary">
              <span class="scb-btn-text">שמור בחירות</span>
              <span class="scb-btn-loading" style="display: none;">טוען...</span>
            </button>
            <button type="button" id="scb-btn-cancel" class="scb-btn">בטל</button>
          </div>
        </form>
      </div>
      <div class="scb-more" style="display: flex;justify-content: space-between;direction: ltr;"><a href="<?php echo esc_url(get_privacy_policy_url() ?: '#'); ?>" target="_blank" rel="noopener">מדיניות פרטיות</a>
        <a href="https://atarimtr.co.il/" target="_blank" rel="noopener" role="link">
          <svg inkscape:version="1.2.1 (9c6d41e410, 2022-07-14)" version="1.1" id="svg2" viewBox="0 0 24 24" height="16" width="16" sodipodi:docname="atr-guten-icon.svg" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/">
            <sodipodi:namedview inkscape:window-maximized="1" inkscape:window-y="-8" inkscape:window-x="-8" inkscape:window-height="1009" inkscape:window-width="1920" inkscape:guide-bbox="true" showguides="false" showgrid="false" inkscape:current-layer="layer3" inkscape:document-units="px" inkscape:cy="12" inkscape:cx="12" inkscape:zoom="33.583333" inkscape:pageshadow="2" inkscape:pageopacity="0.0" borderopacity="1.0" bordercolor="#666666" pagecolor="#ffffff" id="base" units="px" inkscape:showpageshadow="2" inkscape:pagecheckerboard="0" inkscape:deskcolor="#d1d1d1"></sodipodi:namedview>
            <defs id="defs4"></defs>
            <g style="display:inline" inkscape:label="Layer 3" id="layer3" inkscape:groupmode="layer" transform="translate(0,-1028.3622)">
              <path sodipodi:nodetypes="ccccscc" inkscape:connector-curvature="0" id="path4182" d="m 12.076801,1046.5293 -5.1846318,3.1169 3.6096058,1.9426 c 0,0 0.572927,0.233 1.489745,0.6308 1.041163,-0.5804 4.979356,-2.5632 4.979356,-2.5632 -1.889288,-1.1423 -4.894068,-3.1271 -4.894068,-3.1271 z" style="display:inline;fill:#26a2b4;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
              <path sodipodi:nodetypes="cccccc" inkscape:connector-curvature="0" id="path4186" d="m 1.7276013,1046.5237 5.150301,-2.8783 5.1643637,2.9306 -5.1370677,3.067 -4.3969393,-2.4535 c -0.5179755,-0.2845 -0.7939729,-0.4057 -0.7806577,-0.6658 z" style="display:inline;fill:#4fab2e;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
              <path sodipodi:nodetypes="ccccc" inkscape:connector-curvature="0" id="path4190" d="m 12.042266,1046.576 -5.1847193,-2.9627 -0.00947,-5.6896 5.1690883,2.8632 z" style="display:inline;fill:#d8d9de;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
              <path sodipodi:nodetypes="cccccc" inkscape:connector-curvature="0" id="path4192" d="m 17.056384,1037.8742 v 5.7497 l 5.063042,2.8378 c 0,0 0.09576,-0.3658 0.07506,-1.5277 v -4.1967 z" style="display:inline;fill:#d8d9de;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:0.998851px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
              <path sodipodi:nodetypes="ccccc" inkscape:connector-curvature="0" id="path4196" d="m 17.056384,1043.6651 -4.985509,2.9452 4.910058,3.0574 5.16782,-3.3012 z" style="display:inline;fill:#012788;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
              <path sodipodi:nodetypes="ccccc" inkscape:connector-curvature="0" id="path4198" d="m 22.224248,1040.7869 0.0027,-4.1764 c -0.0027,-1.2226 -0.15367,-1.3928 -0.15367,-1.3928 l -5.016854,2.7161 z" style="display:inline;fill:#e0247d;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:0"></path>
              <path sodipodi:nodetypes="ccccc" inkscape:connector-curvature="0" id="path4202" d="m 17.056384,1031.9634 -4.993594,3.225 5.016407,2.7568 4.994081,-2.7275 z" style="display:inline;fill:#bd0ad4;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
              <path sodipodi:nodetypes="cccccc" inkscape:connector-curvature="0" id="path4210" d="m 12.090525,1029.5845 c 0,0 -0.549471,0.038 -1.250067,0.4265 -1.505646,0.9077 -3.9714327,2.3243 -3.9714327,2.3243 l 5.2137227,2.8591 4.973636,-3.231 z" style="display:inline;fill:#e96715;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
              <path sodipodi:nodetypes="cccccc" inkscape:connector-curvature="0" id="path4214" d="m 6.8779023,1032.3353 c 0,0 -2.7172973,1.4804 -4.1721331,2.2468 -0.6391915,0.3653 -0.763411,0.7989 -0.763411,0.7989 l 4.9012607,2.5495 5.1952551,-2.7097 z" style="display:inline;fill:#f5b940;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
              <path sodipodi:nodetypes="ccccc" inkscape:connector-curvature="0" id="path4256" d="m 1.714286,1040.7869 5.1724933,-2.8531 -4.9621748,-2.5439 c 0,0 -0.2103185,0.3073 -0.2103185,1.5978 0,1.2815 0,3.7992 0,3.7992 z" style="display:inline;fill:#f8ee31;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
              <path sodipodi:nodetypes="ccccc" inkscape:connector-curvature="0" id="path4258" d="m 12.077531,1035.1884 -5.2102955,2.7454 5.1625545,2.8531 5.026594,-2.8531 z" style="display:inline;fill:#e4412e;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
              <path sodipodi:nodetypes="cccccc" inkscape:connector-curvature="0" id="path532" d="m 6.8727612,1037.904 v 5.7497 l -5.0833936,2.8378 c 0,0 -0.096146,-0.3658 -0.075362,-1.5277 v -4.1967 z" style="display:inline;fill:#ffffff;fill-opacity:1;fill-rule:evenodd;stroke:#a6a6a6;stroke-width:0.101;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;stroke-dasharray:none;stroke-dashoffset:0"></path>
            </g>
            <metadata id="metadata5298">
              <rdf:rdf>
                <cc:work rdf:about="AtarimTR" atarimtr="">
                  <dc:title atarimtr="">AtarimTR</dc:title>
                </cc:work>
              </rdf:rdf>
            </metadata>
          </svg>
        </a>


      </div>
    </div>
  </div>
  <!-- End Cookie Consent Banner -->
<?php
});

/* --- optional: helper to print data-consent attributes for inline script placeholders --- */
/* Usage example in theme or plugin: <script type="text/plain" data-consent="analytics" src="..."></script>
   The JS will replace it when consent for 'analytics' is given. */

// WooCommerce integration - only activate if WooCommerce is active
function scb_init_woocommerce_integration()
{
  // Check if WooCommerce is active
  if (!class_exists('WooCommerce')) {
    return;
  }

  // הוספת צ'קבוקס אישור מדיניות פרטיות בעמוד התשלום
  add_action('woocommerce_review_order_before_submit', function () {
    woocommerce_form_field('privacy_policy_accepted', [
      'type'        => 'checkbox',
      'class'       => ['form-row privacy'],
      'label_class' => ['woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'],
      'input_class' => ['woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'],
      'required'    => true,
      'label'       => 'קראתי ואני מאשר/ת את <a href="' . esc_url(get_privacy_policy_url()) . '" target="_blank">מדיניות הפרטיות</a>',
    ]);
  }, 20);

  // ולידציה – לוודא שסומן
  add_action('woocommerce_checkout_process', function () {
    if (empty($_POST['privacy_policy_accepted'])) {
      wc_add_notice('יש לאשר את מדיניות הפרטיות לפני ביצוע ההזמנה.', 'error');
    }
  });

  // שמירת ההסכמה בהזמנה
  add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
    if (!empty($_POST['privacy_policy_accepted'])) {
      update_post_meta($order_id, '_privacy_policy_accepted', 'yes');
    }
  });

  // הצגת ההסכמה בממשק הניהול
  add_action('woocommerce_admin_order_data_after_billing_address', function ($order) {
    $accepted = get_post_meta($order->get_id(), '_privacy_policy_accepted', true);
    if ($accepted === 'yes') {
      echo '<p><strong>אישור מדיניות פרטיות:</strong> כן</p>';
    }
  });
}

// Initialize WooCommerce integration
add_action('init', 'scb_init_woocommerce_integration');
