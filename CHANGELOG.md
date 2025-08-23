# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2025-01-23

### Added
- Complete WordPress Plugin Boilerplate (WPBP) conversion
- Professional class-based architecture
- Comprehensive settings page with multiple sections
- Enhanced cookie consent management
- Improved tracking script blocking
- Better internationalization support
- WooCommerce integration improvements
- Debug and development tools
- Enhanced CSS styling and responsive design
- Settings preferences form with cookie category selection
- Comprehensive testing guide with testing scenarios and debugging tools
- Global form integration - automatically adds privacy policy checkboxes to all forms (comments, contact forms, Gravity Forms, Ninja Forms, Contact Form 7, Elementor Forms, and generic forms)

### Changed
- Restructured entire plugin architecture
- Improved banner display logic
- Enhanced consent checking mechanism
- Better error handling and debugging
- Updated all version numbers to 2.0.0
- Moved CSS and JS files to `public/` directory for WPBP compliance
- Improved banner header design (removed duplicate site name)
- Improved admin menu structure - creates standalone menu when ATR Core plugin is not active

### Fixed
- Banner display issues in incognito mode
- Missing JavaScript functions
- HTML structure mismatches
- CSS styling problems
- Consent validation logic
- Plugin action links functionality
- Preferences button functionality (settings form toggle)
- CSS class-based visibility system for settings form
- File path issues for CSS and JavaScript assets
- Debug code cleanup for production use
- Made privacy note text i18n compatible
- Updated POT file with new translatable strings
- Fixed POT and PO files to be compatible with Poedit
- Removed commented-out entries and duplicate strings from translation files
- Added missing "ATR Cookie Consent Banner" string to translation files

### 🔧 **Tracking Function Blocking & Restoration Issues Fixed**
- **Infinite Re-blocking Loop**: Fixed critical issue where tracking functions were being restored then immediately re-blocked, causing infinite loops
- **Consent-Based Blocking**: Added consent checks to prevent blocking when consent is already given
- **Delayed Blocking Prevention**: Fixed delayed blocking from running after consent is given
- **Function Restoration**: Implemented proper `restoreTrackingFunctions()` mechanism for all tracking services
- **GTM/GA Blocking**: Improved blocking logic for Google Tag Manager and Google Analytics
- **Facebook Pixel Blocking**: Enhanced blocking for Facebook Pixel tracking
- **dataLayer Blocking**: Fixed blocking for Google Tag Manager dataLayer.push function
- **Timing Issues**: Resolved complex timing issues with aggressive blocking vs. delayed blocking approaches
- **Blocking Status Monitoring**: Added `isTrackingBlocked()` function for debugging blocking status

### 🌐 **Form Integration & Privacy Checkbox Issues Fixed**
- **Search Box Privacy Checkbox**: Fixed issue where privacy checkbox was appearing on search forms (undesirable)
- **Form Detection Logic**: Implemented smart content-based detection to identify forms that should have privacy checkboxes
- **Form Whitelist System**: Added intelligent filtering to exclude search, login, and navigation forms
- **Elementor Form Integration**: Fixed JavaScript errors and improved Elementor form detection
- **Form Plugin Compatibility**: Enhanced support for Contact Form 7, Gravity Forms, Ninja Forms, and Elementor Forms
- **Banner Exclusion**: Fixed issue where privacy checkbox was appearing on the cookie banner itself
- **Smart Form Detection**: Implemented comprehensive logic to determine which forms collect personal data

### 🎨 **UI/UX & Display Issues Fixed**
- **Duplicate Header Elements**: Removed redundant `<h3>` element that was duplicating site name display
- **Settings Form Visibility**: Fixed CSS-based visibility system for preferences form
- **Button Functionality**: Resolved "Preferences" button not working issue
- **Form Toggle Logic**: Fixed settings form show/hide functionality
- **CSS Class Management**: Improved CSS class handling for form visibility states

### 📱 **Browser & Compatibility Issues Fixed**
- **Incognito Mode**: Fixed banner not displaying in incognito/private browsing mode
- **CSS Asset Loading**: Fixed 404 errors for CSS and JavaScript files
- **File Path Resolution**: Corrected asset paths for WPBP directory structure
- **Cross-Browser Support**: Improved compatibility across different browsers and devices

### 🔍 **Testing & Debugging Improvements**
- **Console Logging**: Enhanced debugging output for tracking function status
- **Function Testing**: Added comprehensive testing functions for tracking functionality
- **Status Monitoring**: Implemented real-time monitoring of blocking status
- **Error Reporting**: Improved error reporting and debugging information
- **Testing Guide**: Created comprehensive testing guide for users and developers
- **User Guide**: Created comprehensive Hebrew user guide for non-technical users

### 🌍 **Internationalization & Translation Issues Fixed**
- **Base Language Correction**: Fixed critical issue where Hebrew was used as base language instead of English
- **POT File Generation**: Corrected POT file to use English msgids
- **Translation File Naming**: Fixed PO/MO file naming convention (he_IL instead of he)
- **Poedit Compatibility**: Resolved issues preventing Poedit from opening translation files
- **Duplicate String Removal**: Cleaned up duplicate entries in translation files
- **Translation Validation**: Improved validation of translation file contents

### ⚙️ **Admin & Settings Issues Fixed**
- **Settings Page Access**: Fixed fatal errors preventing access to plugin settings
- **Menu Structure**: Improved admin menu organization and accessibility
- **Plugin Action Links**: Fixed plugin action links in WordPress admin
- **Settings Form**: Enhanced settings form with better validation and user experience

### 🚀 **Performance & Technical Improvements**
- **File Structure**: Optimized file organization for WPBP compliance
- **Asset Loading**: Improved CSS and JavaScript asset loading
- **Memory Management**: Better memory usage and cleanup
- **Error Handling**: Enhanced error handling and graceful degradation
- **Code Organization**: Improved code structure and maintainability

### 📚 **Documentation & User Experience Improvements**
- **Hebrew User Guide**: Created comprehensive HTML user guide in Hebrew for non-technical users
- **Testing Guide**: Enhanced testing guide with data collection testing procedures
- **Installation Instructions**: Added detailed step-by-step installation instructions
- **Troubleshooting Guide**: Comprehensive troubleshooting section for common issues
- **Legal Disclaimers**: Added clear legal disclaimers and user responsibility notices
- **Visual Instructions**: Added visual cues and step-by-step numbered instructions
- **Cross-Browser Instructions**: Detailed instructions for different browsers and devices
- **Form Integration Documentation**: Clear explanation of privacy checkbox functionality

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

---****

*For detailed installation instructions and usage examples, please refer to the plugin documentation.*
