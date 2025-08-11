<?php
/*
 * Plugin Name: ATR Simple Cookie Consent Banner (Vanilla JS)
 * Description: Consent banner for Essential / Analytics / Marketing cookies. Blocks non-essential scripts until consent. Vanilla JS. Suitable for WooCommerce stores.
 * Plugin URI:        https://atarimtr.co.il
 * Version:           1.0.0
 * Author:            Yehuda Tiram
 * Author URI:        https://atarimtr.co.il/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       atr-simple-cookie-consent-banner
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) exit;

/* --- enqueue assets --- */
add_action('wp_enqueue_scripts', function() {
    wp_register_style('scb-style', plugins_url('atr-scb.css', __FILE__));
    wp_register_script('scb-script', plugins_url('atr-scb.js', __FILE__), [], false, true);

    wp_enqueue_style('scb-style');
    wp_enqueue_script('scb-script');

    // pass some settings to JS if needed
    wp_localize_script('scb-script', 'scbSettings', [
        'cookieName' => 'scb_consent',
        'expiryDays' => 365,
        'siteName' => get_bloginfo('name'),
    ]);
});

/* --- inject banner HTML in footer --- */
add_action('wp_footer', function() {
    ?>
    <!-- Cookie Consent Banner (Injected by plugin) -->
    <div id="scb-overlay" aria-hidden="true"></div>

    <div id="scb-banner" role="dialog" aria-live="polite" aria-label="Cookie consent" aria-hidden="false">
      <div class="scb-content">
        <div class="scb-text">
          <strong><?php echo esc_html(get_bloginfo('name')); ?></strong>
          משתמשים בעוגיות כדי להבטיח תפקוד האתר ולשפר את חוויית המשתמש. אפשר לבחור אילו סוגי עוגיות להפעיל.
        </div>

        <div class="scb-controls">
          <button id="scb-btn-accept-all" class="scb-btn scb-btn-primary">קבל הכל</button>
          <button id="scb-btn-reject" class="scb-btn">הסר לא הכרחיות</button>
          <button id="scb-btn-custom" class="scb-btn">העדפות</button>
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
              <button type="submit" class="scb-btn scb-btn-primary">שמור בחירות</button>
              <button type="button" id="scb-btn-cancel" class="scb-btn">בטל</button>
            </div>
          </form>
        </div>

        <div class="scb-more">
          <a href="<?php echo esc_url( get_privacy_policy_url() ?: '#' ); ?>">מדיניות פרטיות</a>
        </div>
      </div>
    </div>
    <!-- End Cookie Consent Banner -->
    <?php
});

/* --- optional: helper to print data-consent attributes for inline script placeholders --- */
/* Usage example in theme or plugin: <script type="text/plain" data-consent="analytics" src="..."></script>
   The JS will replace it when consent for 'analytics' is given. */

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
