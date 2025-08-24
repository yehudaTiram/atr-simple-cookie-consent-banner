# תוסף באנר הסכמה לעוגיות ומדיניות פרטיות ATR לוורדפרס

## כדי לקבל עדכונים על גרסאות חדשות של התוסף יש להקיש על Watch בחלק העליון של דף זה. לא יזיק גם לתת כוכב בשביל לקדם את התוסף.

## 📥 הורדה
**הגרסה האחרונה זמינה להורדה מ:**
[https://atarimtr.co.il/wp-content/uploads/2025/02/atr-simple-cookie-consent-banner.zip](https://atarimtr.co.il/wp-content/uploads/2025/02/atr-simple-cookie-consent-banner.zip)

---

## 🌍 שפות ותרגום (i18n)

- התוסף יעבוד גם באתרים באנגלית וגם בעברית
- אם תרצו להוסיף שפה נוספת, יש ליצור קובץ שפה מתאים
- התרגום לעברית מוכן לשימוש מיידי

---

## 🎯 מה התוסף עושה?

תוסף שמסייע לבעלי אתרים בישראל לעמוד בדרישות **תיקון 13 לחוק הגנת הפרטיות**.

### ✨ תכונות עיקריות:

1. **באנר עוגיות חכם** - מציג למבקרים אפשרות לבחור איזה עוגיות לאשר
2. **אישור מדיניות פרטיות** - מוסיף שדה חובה בעמוד התשלום של WooCommerce
3. **אינטגרציה גלובלית של טפסים** - מוסיף תיבת סימון מדיניות פרטיות לכל הטפסים באתר (כולל Elementor)
4. **התאמה אישית** - אפשרות לשנות טקסטים ועיצוב

---

## 🚀 איך מתקינים?

1. **הורידו** את הקובץ ZIP
2. **התקינו** דרך WordPress Admin → Plugins → Add New → Upload Plugin
3. **הפעילו** את התוסף
4. **הגדירו** את ההעדפות דרך Settings → Cookie Consent Banner

---

## ⚙️ הגדרות

### תפריט ההגדרות:
- **Settings** - הגדרות בסיסיות של התוסף
- **Documentation** - מדריך מפורט לשימוש
- **Testing Guide** - איך לבדוק שהתוסף עובד נכון

### מיקום התפריט:
- אם יש לך את התוסף "ATR Core" - התפריט יופיע תחתיו
- אם אין - התוסף ייצור תפריט נפרד עם אייקון מגן

---

## 🧪 איך בודקים שהכל עובד?

1. **פיתחו את האתר בחלון פרטי (Incognito)**
2. **בידקו שהבאנר מופיע**
3. **נסו את כפתור "העדפות"**
4. **בחרו אפשרויות שונות ובידקו שהן נשמרות**

**📖 מדריך בדיקה מפורט:** יש תפריט "Testing Guide" עם הוראות מפורטות

---

## 🔧 תמיכה טכנית

### דרישות מערכת:
- WordPress 5.0 ומעלה
- PHP 7.4 ומעלה
- WooCommerce (אופציונלי)

### בעיות נפוצות:
- **הבאנר לא מופיע?** לבדוק שהתוסף מופעל ונסה לנקות Cache
- **טקסטים באנגלית?** לבדוק שהשפה מוגדרת לעברית ב-WordPress
- **שגיאות?** לבדוק את Console בדפדפן (F12)

---

## 📞 תמיכה ועזרה

- **מדריך מפורט:** [https://atarimtr.co.il/איך-להתאים-אתר-ווקומרס-woocommerce-לתיקון-13-לחו/](https://atarimtr.co.il/איך-להתאים-אתר-ווקומרס-woocommerce-לתיקון-13-לחו/)
- **צור קשר:** [https://atarimtr.co.il/צרו-קשר/](https://atarimtr.co.il/צרו-קשר/)
- **אתר המפתח:** [https://atarimtr.co.il/](https://atarimtr.co.il/)

---

## ⚠️ חשוב לדעת

### אחריות:
התוסף מסופק "כפי שהוא" (As Is). המפתח אינו אחראי לנזקים שעלולים להיגרם מהשימוש. ראו גם היעדר מצגים והגבלת אחריות למטה

### ייעוץ משפטי:
התוסף אינו מהווה ייעוץ משפטי. מומלץ להתייעץ עם עורך דין לגבי התאמה לחוקים הספציפיים שלך.

### תאימות:
התוסף נבדק עם הגרסאות העדכניות של WordPress ו-WooCommerce, אך תאימות עם תוספים אחרים אינה מובטחת.

---

## היסטוריית גרסאות - Version History

### גרסה 2.0.1 - Version 2.0.1 (2025-01-23)

#### ✨ תכונות חדשות - New Features
- **זיהוי מתקדם של טפסי WooCommerce** - Enhanced WooCommerce checkout and payment gateway detection
- **לוגיקת זיהוי טפסים חכמה** - Comprehensive form detection logic for privacy checkboxes
- **החרגות JavaScript לטפסי תשלום** - JavaScript-side exclusions for WooCommerce and payment forms

#### שינויים - Changes
- **שיפור אינטגרציית טפסים** - Improved form integration to respect global form integration setting
- **זיהוי מתקדם של iframe תשלומים** - Enhanced payment gateway iframe detection and exclusion
- **תיבת סימון WooCommerce כעת מכבדת הגדרות אינטגרציה** - WooCommerce privacy checkbox now respects integration settings

#### 🐛 תיקונים - Fixes
- **תיבת סימון WooCommerce לא מופיעה יותר כשאינטגרציה גלובלית כבויה** - WooCommerce checkout forms no longer receive privacy checkboxes when global form integration is disabled
- **החרגת iframe שערי תשלום** - Payment gateway iframes are properly excluded from privacy checkbox injection
- **זיהוי טפסים משופר** - Enhanced form detection to prevent interference with checkout processes
- **תיבת סימון WooCommerce כעת נשלטת כראוי** - WooCommerce privacy checkbox now properly controlled by integration settings
- **תיבת סימון WooCommerce מכבדת כעת את שתי ההגדרות** - WooCommerce privacy checkbox now respects both "Global Form Integration" and "WooCommerce Integration" settings

#### 🔧 שיפורים טכניים - Technical Improvements
- **תיקון אי-התאמה בשמות אפשרויות** - Fixed option name mismatch in WooCommerce class
- **הוספת פרמטר constructor לעקביות** - Added constructor parameter for plugin_name consistency
- **שינוי hook אתחול ל-wp_loaded** - Changed initialization hook to wp_loaded for proper settings loading
- **עדכון גרסה ל-2.0.1 בכל הקבצים** - Updated version to 2.0.1 consistently across all files

#### 📚 תיעוד - Documentation
- **הוספת רשומות changelog מקיפות** - Added comprehensive changelog entries for version 2.0.1
- **עדכון מדריך בדיקות** - Updated testing guide with new features
- **שיפור הוראות שימוש** - Enhanced user instructions

---

### גרסה 2.0.0 - Version 2.0.0 (2025-01-23)

#### ✨ תכונות חדשות - New Features
- **המרה מלאה ל-WordPress Plugin Boilerplate (WPBP)** - Complete WordPress Plugin Boilerplate conversion
- **ארכיטקטורה מקצועית מבוססת מחלקות** - Professional class-based architecture
- **דף הגדרות מקיף עם מספר סעיפים** - Comprehensive settings page with multiple sections
- **ניהול הסכמה משופר** - Enhanced cookie consent management
- **חסימת סקריפטי מעקב משופרת** - Improved tracking script blocking
- **תמיכה בינלאומית משופרת** - Better internationalization support
- **אינטגרציה גלובלית של טפסים** - Global form integration for all forms
- **אינטגרציה עם WooCommerce** - WooCommerce integration improvements

#### שינויים - Changes
- **ארגון מחדש של ארכיטקטורת התוסף** - Restructured entire plugin architecture
- **לוגיקת הצגת באנר משופרת** - Improved banner display logic
- **מנגנון בדיקת הסכמה משופר** - Enhanced consent checking mechanism
- **טיפול בשגיאות ומעקב משופר** - Better error handling and debugging
- **עדכון כל מספרי הגרסאות ל-2.0.0** - Updated all version numbers to 2.0.0

#### 🐛 תיקונים - Fixes
- **בעיות הצגת באנר במצב אינקוגניטו** - Banner display issues in incognito mode
- **פונקציות JavaScript חסרות** - Missing JavaScript functions
- **אי-התאמות מבנה HTML** - HTML structure mismatches
- **בעיות עיצוב CSS** - CSS styling problems
- **לוגיקת אימות הסכמה** - Consent validation logic
- **תקינות קישורי פעולת התוסף** - Plugin action links functionality

#### 📚 תיעוד - Documentation
- **מדריך בדיקות מקיף** - Comprehensive testing guide with testing scenarios and debugging tools
- **מדריך משתמש בעברית** - Hebrew user guide for non-technical users
- **הוראות התקנה מפורטות** - Detailed step-by-step installation instructions
- **מדריך פתרון בעיות** - Comprehensive troubleshooting section for common issues

---

### גרסה 1.0.0 - Version 1.0.0 (Initial Release)
- **שחרור ראשוני** - Initial release
- **באנר הסכמה בסיסי** - Basic cookie consent banner
- **תמיכה בעברית** - Hebrew language support
- **עמידה בחוק הגנת הפרטיות הישראלי** - Compliance with Israeli Privacy Protection Law

---

*פותח על ידי יהודה תירם - AtarimTR*

---

## היעדר מצגים והגבלת אחריות

מדיניות הפרטיות, הסברים וההנחיות הכלולים בתוסף ניתנים למטרות מידע בלבד ואינם מהווים ייעוץ משפטי או התחייבות משפטית כלשהי. אין להסתמך עליהם כהנחיה מחייבת או כתשתית לפעולה משפטית. מומלץ לעיין בחוקים הרלוונטיים ולהתאים את השימוש באתר ובתוסף בהתאם לצרכיך הספציפיים.

אין אחריות לעמידה בחוק הגנת הפרטיות ותיקון 13 שלו. התוסף המוצע (להלן: "התוסף") הוא כלי המאפשר הצגה של באנר המודיע על מדיניות העוגיות באתר. התוסף אינו מבטיח כי השימוש בו לבדו יעניק לאתר עמידה מלאה בדרישות חוק הגנת הפרטיות, התשמ"א-1981, ותיקון 13 שלו, והוא אינו אחראי לתכנים שתוסיפו לבאנר או למדיניות הפרטיות שלכם. לכן, לא נהיה אחראים לכל טענה שכל אדם או גוף יעלו כנגדכם בנוגע לעמידה בדרישות החוקיות של עוגיות, מדיניות פרטיות או כל נושא אחר הקשור לכך.

האחריות מול משתמשים המבקרים באתר שלכם מוטלת עליכם.

למען הסר ספק, מובהר כי לא תהיה לנו כל אחריות כלפי המשתמשים באתר שלכם או כלפי כל יישות שהיא, בכל טענה הקשורה לניהול מדיניות הפרטיות והעוגיות באתר או בכל טענה אחרת כלשהי. אתם תישאו באחריות לכל טענה שתועלה ובטיפול בה כולל בעלויות הנובעות ממנה, גם אם היא נבעה מפעילות התוסף.

התוסף ניתן "כמות שהוא" וללא מצגים.

התוסף נמסר לשימושכם "כמות שהוא" (As Is), ואינו נושא באחריות כלשהי כלפיכם או כלפי כל אדם. למען הסר ספק, התוסף אינו מציג אחריות כאילו לאחר התקנתו יהיה האתר שלכם תואם באופן מלא לכל חקיקה רלוונטית בנושא עוגיות, לרבות חוק הגנת הפרטיות ותיקון 13 שלו.

התוסף אינו אחראי לכל תוצאה, נזק ו/או עלות מכל מין וסוג שהם (ישירים או עקיפים) שנגרמו כתוצאה משימוש בו, בתכניו או במידע המוצג באמצעותו או כתוצאה מאי-עמידה בתקן ו/או דין כזה או אחר כולל אם נגרמו תקלות באתר כתוצאה מהשימוש בו.

התוסף אמנם מאפשר הצגה של באנר עוגיות, אך הוא אינו ממצה את כל הדרישות החוקיות והוא אינו מחליף ייעוץ משפטי בנושא.
