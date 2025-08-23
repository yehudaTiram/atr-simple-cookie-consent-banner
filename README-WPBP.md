# ATR Simple Cookie Consent Banner - WordPress Plugin Boilerplate Version

This is the WordPress Plugin Boilerplate (WPBP) version of the ATR Simple Cookie Consent Banner plugin, designed for Israeli websites to comply with the 13th amendment of the Privacy Protection Law.

## 🏗️ New Structure

The plugin has been restructured following WordPress Plugin Boilerplate standards for better maintainability, extensibility, and WordPress coding standards compliance.

### Directory Structure

```
atr-simple-cookie-consent-banner/
├── admin/                          # Admin-specific functionality
│   ├── css/                        # Admin stylesheets
│   │   └── atr-simple-cookie-consent-banner-admin.css
│   └── js/                         # Admin JavaScript
│       └── atr-simple-cookie-consent-banner-admin.js
├── includes/                       # Core plugin classes
│   ├── class-atr-simple-cookie-consent-banner-loader.php
│   ├── class-atr-simple-cookie-consent-banner-i18n.php
│   ├── class-atr-simple-cookie-consent-banner-consent.php
│   └── class-atr-simple-cookie-consent-banner-woocommerce.php
├── public/                         # Public-facing functionality
│   └── class-atr-simple-cookie-consent-banner-public.php
├── languages/                      # Translation files
├── atr-scb.css                    # Frontend styles (unchanged)
├── atr-scb.js                     # Frontend JavaScript (unchanged)
├── atr-simple-cookie-consent-banner.php  # Main plugin file
├── README.md                       # Original README
└── README-WPBP.md                 # This file
```

## 🔧 Key Changes

### 1. **Class-Based Architecture**
- **Main Plugin Class**: `ATR_Simple_Cookie_Consent_Banner` orchestrates all functionality
- **Loader Class**: Manages WordPress hooks and filters systematically
- **Public Class**: Handles frontend functionality (banner, script blocking)
- **Admin Class**: Manages admin-side functionality
- **WooCommerce Class**: Handles e-commerce integration
- **Consent Class**: Manages cookie consent logic

### 2. **Improved Hook Management**
- All WordPress hooks are now managed through the loader class
- Better separation of concerns
- Easier to maintain and extend

### 3. **Enhanced Security**
- Proper WordPress coding standards
- Better input validation and sanitization
- Improved nonce handling (when admin features are added)

### 4. **Internationalization Ready**
- Text domain properly configured
- Translation-ready strings
- Language file support

## 🚀 Benefits of WPBP Structure

### **Maintainability**
- Clear separation of concerns
- Easy to locate specific functionality
- Consistent coding patterns

### **Extensibility**
- Simple to add new features
- Easy to override specific functionality
- Clean inheritance structure

### **WordPress Standards**
- Follows WordPress coding standards
- Proper hook usage
- Better integration with WordPress ecosystem

### **Professional Development**
- Industry-standard structure
- Easier for team development
- Better documentation support

## 📋 Usage

### **Basic Implementation**
The plugin works exactly the same as before - no changes needed for existing implementations.

### **Adding Custom Functionality**
```php
// Example: Add custom consent type
add_filter('atr_scb_consent_types', function($types) {
    $types['custom'] = 'Custom Cookies';
    return $types;
});
```

### **Customizing Banner Text**
```php
// Example: Override banner text
add_filter('atr_scb_banner_text', function($text) {
    return 'Your custom cookie consent message';
});
```

## 🔌 Integration Examples

### **Google Analytics (with consent)**
```html
<script type="text/plain" data-consent="analytics" src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script type="text/plain" data-consent="analytics">
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

### **Facebook Pixel (with consent)**
```html
<script type="text/plain" data-consent="marketing" src="https://connect.facebook.net/en_US/fbevents.js"></script>
<script type="text/plain" data-consent="marketing">
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', 'PIXEL_ID');
    fbq('track', 'PageView');
</script>
```

## 🛠️ Development

### **Adding New Features**
1. Create new class in appropriate directory
2. Register with the main plugin class
3. Add hooks through the loader

### **Modifying Existing Features**
1. Extend the appropriate class
2. Use WordPress filters and actions
3. Maintain backward compatibility

### **Testing**
- Test on WordPress 5.0+
- Verify WooCommerce integration
- Check consent functionality
- Validate script blocking

## 📚 Documentation

- **Original README**: Contains detailed usage instructions
- **Code Comments**: Inline documentation for all classes
- **WordPress Standards**: Follows WordPress coding guidelines

## 🔄 Migration from Old Version

1. **Backup** your current plugin
2. **Replace** the main plugin file
3. **Upload** the new directory structure
4. **Test** functionality on a staging site
5. **Deploy** to production

## 📞 Support

For support and questions:
- **Website**: https://atarimtr.co.il/
- **Author**: Yehuda Tiram
- **License**: GPL-2.0+

## 📄 License

This plugin is licensed under the GPL v2 or later.

---

**Note**: This WPBP version maintains 100% backward compatibility while providing a more professional, maintainable codebase for future development.
