# Cookie Consent Plugin Testing Guide

This guide provides comprehensive testing methods to verify that the ATR Simple Cookie Consent Banner plugin properly complies with user consent selections.

## 🧪 Testing Cookie Consent Compliance

### **1. Browser Developer Tools Testing**

#### **Console Testing (Easiest Method):**
Open your browser's Developer Tools (F12) and test these commands:

```javascript
// Check current consent status
window.scb.getConsent()

// Check specific consent types
window.scb.consentGivenFor('analytics')
window.scb.consentGivenFor('marketing')

// Test tracking functionality
window.scb.testTracking()

// Debug consent cookies
window.scb.debugConsent()

// Test GTM detection
window.scb.debugGTM()
```

#### **Network Tab Testing:**
1. Open Network tab in DevTools
2. Clear browser data (cookies, localStorage)
3. Reload page → Should see banner
4. Accept different consent options
5. Watch for tracking requests:
   - **No consent**: Should see blocked/404 requests
   - **Analytics consent**: Should see Google Analytics requests
   - **Marketing consent**: Should see Facebook Pixel requests

### **2. Cookie Consent Scenarios Testing**

#### **Test Case 1: Accept All**
```javascript
// In console, simulate accepting all
window.scb.saveConsent({
  essential: true,
  analytics: true,
  marketing: true,
  ts: Date.now()
})
// Reload page - should see no banner, all tracking active
```

#### **Test Case 2: Reject Non-Essential**
```javascript
// In console, simulate rejecting non-essential
window.scb.saveConsent({
  essential: true,
  analytics: false,
  marketing: false,
  ts: Date.now()
})
// Reload page - should see no banner, no tracking
```

#### **Test Case 3: Custom Selection**
```javascript
// In console, simulate custom selection
window.scb.saveConsent({
  essential: true,
  analytics: true,
  marketing: false,
  ts: Date.now()
})
// Reload page - should see no banner, analytics only
```

### **3. Visual Testing Methods**

#### **Banner Behavior:**
1. **Clear all data** → Banner should appear
2. **Click "Accept All"** → Banner should disappear, page should reload
3. **Click "Reject"** → Banner should disappear, no reload
4. **Click "Preferences"** → Settings form should toggle
5. **Custom selection** → Form should work, banner should close

#### **Tracking Script Behavior:**
1. **Before consent**: Check if tracking scripts are blocked
2. **After consent**: Check if appropriate scripts are activated

### **4. Real Tracking Script Testing**

#### **Google Analytics Test:**
```javascript
// Before consent (should be undefined/blocked)
console.log('gtag available:', typeof window.gtag === 'function')
console.log('dataLayer:', window.dataLayer)

// After analytics consent (should work)
// Check if gtag() function is available and working
```

#### **Facebook Pixel Test:**
```javascript
// Before consent (should be undefined/blocked)
console.log('fbq available:', typeof window.fbq === 'function')

// After marketing consent (should work)
// Check if fbq() function is available
```

### **4.1. Comprehensive Data Collection Testing**

After verifying that tracking functions are working, here's how to test that **actual data collection** is functioning:

#### **🧪 Built-in Testing Functions**

The plugin includes comprehensive testing tools. In your browser console, run:

```javascript
// Test if tracking functions are working (not blocked)
window.scb.testTracking();

// Check current blocking status
window.scb.isTrackingBlocked();
```

#### **📊 Google Analytics Data Collection Testing**

##### **Real-time Reports Verification:**
1. Go to **Google Analytics** → **Reports** → **Real-time** → **Events**
2. Perform actions on your site (click buttons, scroll, navigate)
3. You should see real-time data appearing in the reports

##### **Console Testing for GA:**
```javascript
// Test GA event tracking
gtag('event', 'test_event', {
  event_category: 'cookie_consent_test',
  event_label: 'user_clicked_accept_all'
});

// Test GA pageview
gtag('config', 'GA_MEASUREMENT_ID', {
  page_title: 'Test Page',
  page_location: window.location.href
});

// Check if gtag is working
console.log('gtag function type:', typeof gtag);
console.log('gtag function source:', gtag.toString().substring(0, 100));
```

#### **🏷️ Google Tag Manager Data Collection Testing**

##### **Preview Mode Testing:**
1. Go to **GTM** → **Preview** button
2. Enter your website URL
3. Perform actions and see real-time tag firing
4. Verify that tags are firing according to consent level

##### **Console Testing for GTM:**
```javascript
// Test dataLayer pushes
dataLayer.push({
  event: 'test_event',
  eventCategory: 'cookie_consent',
  eventAction: 'accept_all',
  eventLabel: 'test_label'
});

// Check dataLayer contents
console.log('dataLayer length:', dataLayer.length);
console.log('dataLayer contents:', dataLayer);

// Test if dataLayer.push is working
console.log('dataLayer.push function:', typeof dataLayer.push);
```

#### **📱 Facebook Pixel Data Collection Testing**

##### **Facebook Pixel Helper Extension:**
1. Install "Facebook Pixel Helper" Chrome extension
2. Browse your site and see pixel fires in real-time
3. Verify pixel events are firing after marketing consent

##### **Console Testing for Facebook Pixel:**
```javascript
// Test Facebook Pixel
fbq('track', 'PageView');
fbq('track', 'Lead', {
  content_name: 'Cookie Consent Test',
  content_category: 'Test Event'
});

// Check if fbq is working
console.log('fbq function type:', typeof fbq);
console.log('fbq function source:', fbq.toString().substring(0, 100));
```

#### **🌐 Network Tab Verification**

1. **Open Developer Tools** → **Network tab**
2. **Filter by domain:**
   - `google-analytics.com` (GA requests)
   - `googletagmanager.com` (GTM requests)
   - `facebook.com` (Facebook Pixel requests)
3. **Perform actions** on your site
4. **Look for successful requests** (200 status codes)

**Expected Results:**
- **Before consent**: Blocked/404 requests or no requests
- **After consent**: Successful 200 status requests to tracking services

#### **🎯 Real-world Testing Scenarios**

##### **Complete User Journey Test:**
```javascript
// 1. Clear consent and test from scratch
window.scb.clearConsent();
location.reload();

// 2. Accept all cookies in the banner
// 3. Navigate to different pages
// 4. Click buttons/links
// 5. Fill out forms
// 6. Check if data appears in GA/GTM real-time reports
```

##### **Specific Event Testing:**
```javascript
// Test various user interactions
document.querySelector('a').click(); // Test link clicks
window.scrollTo(0, 100); // Test scroll tracking
document.querySelector('form').submit(); // Test form submissions

// Test custom events
gtag('event', 'custom_event', {
  event_category: 'user_interaction',
  event_action: 'button_click',
  event_label: 'test_button'
});
```

#### **🔧 Advanced Debug Mode**

Enable more verbose logging for troubleshooting:

```javascript
// Enable debug mode for tracking
window.gtag_debug = true;
window.fbq_debug = true;

// Check function availability and status
console.log('=== Tracking Function Status ===');
console.log('gtag function:', typeof gtag);
console.log('ga function:', typeof ga);
console.log('fbq function:', typeof fbq);
console.log('dataLayer:', dataLayer);

// Check if functions are our restored versions
console.log('gtag is blocked:', gtag.toString().includes('SCB: gtag() blocked'));
console.log('ga is blocked:', ga.toString().includes('SCB: ga() blocked'));
console.log('fbq is blocked:', fbq.toString().includes('SCB: fbq() blocked'));
```

#### **📱 Cross-Platform Testing**

- **Test on mobile devices** (different user agents)
- **Test in incognito/private mode**
- **Test with different browsers** (Chrome, Firefox, Safari, Edge)
- **Test with different consent levels** on each platform

#### **✅ Expected Results After Accepting Cookies**

You should see these **console logs:**
```
SCB: Restoring tracking functions...
SCB: gtag() function restored
SCB: ga() function restored
SCB: fbq() function restored
SCB: dataLayer.push() function restored
SCB: Tracking functions restoration completed
```

**Network requests:**
- ✅ Successful calls to Google Analytics
- ✅ Successful calls to Facebook Pixel
- ✅ Successful calls to GTM

**Real-time data:**
- ✅ Pageviews appearing in GA real-time
- ✅ Events firing in GTM preview
- ✅ Pixel fires in Facebook Pixel Helper

#### **🚨 Troubleshooting Data Collection Issues**

If data collection isn't working after consent:

```javascript
// 1. Check consent status
window.scb.debugConsent();

// 2. Check if functions are still blocked
window.scb.isTrackingBlocked();

// 3. Test individual functions
gtag('event', 'test');
ga('send', 'event', 'test');
fbq('track', 'PageView');

// 4. Check for JavaScript errors in console
// 5. Verify network requests are going through
```

**Common Issues:**
- Functions restored but still not working → Check for JavaScript errors
- Network requests failing → Check for CORS or blocking issues
- Real-time data not appearing → Check GA/GTM configuration
- Pixel fires not showing → Check Facebook Pixel Helper extension

### **5. Automated Testing Script**

Create a test file to run all scenarios:

```javascript
// test-consent.js - Run this in console
function testConsentCompliance() {
  console.log('🧪 Testing Cookie Consent Compliance...')
  
  // Test 1: No consent
  console.log('\n📋 Test 1: No consent')
  window.scb.clearConsent()
  console.log('Consent cleared, banner should show on reload')
  
  // Test 2: Essential only
  console.log('\n📋 Test 2: Essential only')
  window.scb.saveConsent({
    essential: true,
    analytics: false,
    marketing: false,
    ts: Date.now()
  })
  console.log('Essential consent saved, no tracking should work')
  
  // Test 3: Analytics consent
  console.log('\n📋 Test 3: Analytics consent')
  window.scb.saveConsent({
    essential: true,
    analytics: true,
    marketing: false,
    ts: Date.now()
  })
  console.log('Analytics consent saved, GA should work, FB should not')
  
  // Test 4: Full consent
  console.log('\n📋 Test 4: Full consent')
  window.scb.saveConsent({
    essential: true,
    analytics: true,
    marketing: true,
    ts: Date.now()
  })
  console.log('Full consent saved, all tracking should work')
}

// Run the test
testConsentCompliance()
```

### **6. What to Look For**

#### **✅ Success Indicators:**
- Banner appears for new users
- Banner disappears after consent
- No banner on return visits (with valid consent)
- Tracking scripts work according to consent level
- Settings form toggles properly
- Consent persists across page reloads

#### **❌ Failure Indicators:**
- Banner doesn't appear for new users
- Banner keeps showing after consent
- Tracking works without consent
- Settings form doesn't toggle
- Consent doesn't persist

### **7. Quick Test Checklist**

```markdown
□ Clear browser data
□ Reload page → Banner appears
□ Click "Accept All" → Banner disappears, page reloads
□ Reload page → No banner appears
□ Check console: window.scb.getConsent() shows full consent
□ Check Network tab: Tracking requests should work
□ Clear consent: window.scb.clearConsent()
□ Reload page → Banner appears again
□ Click "Preferences" → Settings form toggles
□ Custom selection → Form works, consent saved
```

### **8. Testing Different Consent Levels**

#### **Essential Only (Reject Non-Essential):**
- ✅ Website functions normally
- ❌ No Google Analytics tracking
- ❌ No Facebook Pixel tracking
- ❌ No marketing cookies

#### **Analytics Consent:**
- ✅ Website functions normally
- ✅ Google Analytics tracking works
- ❌ No Facebook Pixel tracking
- ❌ No marketing cookies

#### **Full Consent (Accept All):**
- ✅ Website functions normally
- ✅ Google Analytics tracking works
- ✅ Facebook Pixel tracking works
- ✅ All marketing cookies enabled

### **9. Troubleshooting Common Issues**

#### **Banner Not Appearing:**
1. Check if consent already exists: `window.scb.getConsent()`
2. Clear consent: `window.scb.clearConsent()`
3. Check browser console for errors
4. Verify plugin is active in WordPress

#### **Settings Form Not Toggling:**
1. Check if `scb-settings` element exists
2. Verify CSS classes are working
3. Check JavaScript console for errors

#### **Tracking Not Working After Consent:**
1. Verify consent was saved correctly
2. Check if tracking scripts are properly unblocked
3. Reload page to ensure all changes take effect
4. Check Network tab for tracking requests

### **10. Performance Testing**

#### **Load Time Impact:**
- Measure page load time with banner
- Measure page load time without banner
- Banner should add minimal overhead (<100ms)

#### **Memory Usage:**
- Check memory usage in DevTools
- Plugin should not cause memory leaks
- Consent data should be lightweight

---

## 📋 **Quick Reference Commands**

```javascript
// Essential testing commands
window.scb.getConsent()                    // Check current consent
window.scb.clearConsent()                  // Clear all consent
window.scb.forceShow()                     // Force show banner
window.scb.testTracking()                  // Test tracking functions
window.scb.debugConsent()                  // Debug consent status
```

---

*For additional support or to report issues, please refer to the plugin documentation or contact support.*
