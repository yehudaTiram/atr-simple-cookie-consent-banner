# Changelog

All notable changes to the ATR Simple Cookie Consent Banner plugin will be documented in this file.

## [1.0.2] - 2025-08-17

### 🐛 **Bug Fixes**
- **Fixed Click-Outside-to-Close on Privacy Pages** - Users can now click outside the banner to close it on privacy policy pages
- **Eliminated Banner Flash After Consent** - Banner no longer briefly appears on pages where consent has already been given
- **Improved Consent Validation** - Better timing and validation to prevent unnecessary banner display

### 🔧 **Improvements**
- **Enhanced Privacy Page Detection** - More robust detection logic for privacy policy pages
- **Better CSS Management** - Fixed pointer-events issues that were preventing interaction
- **Improved Asset Versioning** - Added SCB_VERSION constant for consistent version management

### 🛠️ **Developer Experience**
- **Added Debug Capabilities** - Console logging for development and troubleshooting
- **Enhanced Error Handling** - Better error prevention and debugging information
- **Code Organization** - Cleaner initialization and consent checking logic

---

## [1.0.1] - 2025-08-15

### 🆕 **New Features**
- **Privacy Policy Page Access** - Users can now read privacy policy pages without being blocked by the consent banner
- **Smart Page Detection** - Automatically detects when users are on privacy policy pages
- **Enhanced WooCommerce Integration** - Improved structure and performance for WooCommerce compatibility

### 🔧 **Improvements**
- **No More Flash on Page Reload** - Banner now starts hidden with smooth transitions
- **Better Mobile Experience** - Responsive design with touch-friendly buttons and mobile-optimized layout
- **Smooth Animations** - Added CSS transitions and transforms for professional appearance
- **Loading States** - Visual feedback during consent operations
- **Accessibility Enhancements** - Better ARIA labels and keyboard navigation support

### 🐛 **Bug Fixes**
- **Fixed Banner Flash** - Banner no longer briefly appears on page reload
- **Fixed Mobile Blocking** - Banner no longer blocks content on mobile devices
- **Fixed Privacy Page Access** - Users can now access privacy policy content while deciding on cookies
- **Fixed Race Conditions** - Improved initialization timing to prevent display issues

### 🎨 **UI/UX Improvements**
- **Less Intrusive Overlay** - Privacy policy pages now have a lighter overlay (15% opacity vs 35%)
- **Click-Outside to Close** - Users can close banner by clicking outside (mobile-friendly)
- **Escape Key Support** - Added keyboard shortcut to close banner
- **Professional Styling** - Enhanced button hover effects and visual feedback
- **Hebrew Text Support** - Proper RTL support for Israeli websites

### 🏗️ **Technical Improvements**
- **Better State Management** - Improved consent state handling and persistence
- **Performance Optimization** - Reduced unnecessary DOM operations
- **Code Organization** - Cleaner, more maintainable code structure
- **WooCommerce Integration** - Single function wrapper for better performance
- **Error Prevention** - Added checks to prevent multiple initializations

### 📱 **Mobile Optimizations**
- **Responsive Design** - Banner adapts to different screen sizes
- **Touch-Friendly Buttons** - Larger buttons with proper spacing for mobile
- **Mobile-First Layout** - Optimized for mobile devices while maintaining desktop experience
- **Scroll Prevention** - Smart body scroll management (disabled on privacy pages)

### 🔒 **Privacy & Compliance**
- **Israeli Law Compliance** - Specifically designed for תיקון 13 לחוק הגנת הפרטיות
- **Cookie Categories** - Essential, Analytics, and Marketing cookie management
- **Consent Persistence** - Proper storage and retrieval of user consent
- **Script Blocking** - Non-essential scripts blocked until consent is given

### 📦 **Installation & Updates**
- **Version Control** - Assets properly versioned for cache busting
- **Clean Package** - Optimized file structure for WordPress installation
- **Backward Compatibility** - Maintains compatibility with existing implementations

---

## [1.0.0] - Initial Release

### 🎯 **Initial Features**
- Basic cookie consent banner functionality
- Essential, Analytics, and Marketing cookie categories
- WooCommerce integration for checkout privacy policy acceptance
- Local storage and cookie fallback for consent persistence
- Basic responsive design

---

## 📝 **Notes**

- **Compatibility**: WordPress 5.0+, PHP 7.4+
- **Browser Support**: Modern browsers with ES6 support
- **Mobile**: Responsive design for all device sizes
- **Languages**: Hebrew (RTL) and English support
- **License**: GPL-2.0+

---

*For detailed installation instructions and usage examples, please refer to the plugin documentation.*
