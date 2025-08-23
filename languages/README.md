# Internationalization (i18n) Guide for ATR Simple Cookie Consent Banner

This directory contains all the translation files for the ATR Simple Cookie Consent Banner plugin.

## 📁 File Structure

- `atr-simple-cookie-consent-banner.pot` - Template file with all translatable strings
- `atr-simple-cookie-consent-banner-he.po` - Hebrew translations (sample)
- `atr-simple-cookie-consent-banner-en.po` - English translations (sample)

## 🌍 Supported Languages

The plugin currently supports:
- **English (en)** - Base language (default)
- **Hebrew (he)** - Hebrew translation
- **Other languages** - Can be added by creating new PO files

## 🔧 How to Add a New Language

### 1. Create a PO File
Copy the POT file and rename it to match your language code:
```bash
cp atr-simple-cookie-consent-banner.pot atr-simple-cookie-consent-banner-[LANG_CODE].po
```

### 2. Edit the PO File Header
Update the language information in the PO file:
```po
"Language-Team: [Your Language]\n"
"Language: [LANG_CODE]\n"
"Last-Translator: [Your Name] <[your@email.com]>\n"
```

### 3. Translate the Strings
Translate each `msgstr` value to your language:
```po
msgid "Accept All"
msgstr "קבל הכל"  # Translate this to your language
```

### 4. Compile the MO File
Use a tool like Poedit or command line to compile the PO file to MO:
```bash
msgfmt atr-simple-cookie-consent-banner-[LANG_CODE].po -o atr-simple-cookie-consent-banner-[LANG_CODE].mo
```

## 📝 Translatable Strings

### Main Banner Text
- Cookie consent message
- Button labels (Accept All, Reject, Preferences, etc.)
- Form labels and descriptions
- Privacy policy link text

### WooCommerce Integration
- Privacy policy acceptance checkbox
- Validation messages
- Admin order display text

### Consent Management
- Banner text (Hebrew and English versions)
- Consent type descriptions

## 🛠️ Translation Tools

### Recommended Tools
- **Poedit** - User-friendly GUI editor
- **Lokalise** - Online translation platform
- **GNU gettext** - Command line tools

### Command Line Usage
```bash
# Extract translatable strings
xgettext -o atr-simple-cookie-consent-banner.pot *.php

# Update existing PO files
msgmerge -U atr-simple-cookie-consent-banner-[LANG].po atr-simple-cookie-consent-banner.pot

# Compile PO to MO
msgfmt atr-simple-cookie-consent-banner-[LANG].po -o atr-simple-cookie-consent-banner-[LANG].mo
```

## 🔍 Finding Translatable Strings

All translatable strings in the plugin use WordPress internationalization functions:

- `__( 'text', 'atr-simple-cookie-consent-banner' )` - For simple strings
- `_e( 'text', 'atr-simple-cookie-consent-banner' )` - For echoed strings
- `esc_html( __( 'text', 'atr-simple-cookie-consent-banner' ) )` - For escaped HTML output

## 📚 Translation Guidelines

### 1. Maintain Context
Keep the original meaning and context of each string.

### 2. Preserve HTML Tags
Don't remove or modify HTML tags in strings like:
```po
msgid "קראתי ואני מאשר/ת את <a href=\"%s\" target=\"_blank\">מדיניות הפרטיות</a>"
```

### 3. Use Proper Plural Forms
Some languages have different plural forms. Update the header accordingly:
```po
"Plural-Forms: nplurals=2; plural=(n != 1);\n"
```

### 4. Test Your Translations
After creating a translation:
1. Upload the MO file to the `languages/` directory
2. Set the WordPress language to your language
3. Test the plugin to ensure all text appears correctly

## 🚀 Contributing Translations

To contribute translations:

1. Fork the plugin repository
2. Create your language PO file
3. Translate all strings
4. Compile to MO format
5. Submit a pull request

## 📞 Support

For translation support or questions:
- **Website**: https://atarimtr.co.il/
- **Author**: Yehuda Tiram
- **License**: GPL-2.0+

## 📄 License

Translations are licensed under the same GPL-2.0+ license as the plugin.

---

**Note**: Always test your translations thoroughly before deploying to production sites.
