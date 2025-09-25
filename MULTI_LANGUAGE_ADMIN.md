# Multi-Language Admin Panel System

## 🌍 Overview

The SiOmar admin panel now supports **English** and **Arabic** languages with full RTL (Right-to-Left) support for Arabic. Users can seamlessly switch between languages using the language switcher in the top navigation bar.

## ✨ Features

### 🔄 Language Switching
- **Real-time switching** between English and Arabic
- **Session-based** language persistence
- **RTL support** for Arabic interface
- **Flag indicators** for easy identification

### 🎨 RTL Support
- **Complete RTL layout** for Arabic
- **Proper text direction** and alignment
- **Mirrored navigation** and UI elements
- **Arabic font support**

### 📝 Translation Coverage
- **Navigation menus** and headers
- **Form labels** and placeholders
- **Success/error messages**
- **Status indicators**
- **Action buttons**
- **Table headers** and content

## 🛠️ Technical Implementation

### 1. Language Files Structure
```
lang/
├── en/
│   └── admin.php          # English translations
└── ar/
    └── admin.php          # Arabic translations
```

### 2. Language Controller
**File:** `app/Http/Controllers/LanguageController.php`

**Key Methods:**
- `switchLanguage()` - Handles language switching
- `getCurrentLanguage()` - Returns current language info

### 3. Middleware
**File:** `app/Http/Middleware/SetLocale.php`

**Function:**
- Automatically sets application locale based on session
- Validates allowed locales (en, ar)
- Applied globally to all requests

### 4. Language Switcher Component
**File:** `resources/views/components/language-switcher.blade.php`

**Features:**
- Flag icons for visual identification
- Active state highlighting
- Responsive design (text hidden on small screens)

## 🚀 Usage

### For Users

#### Switching Languages
1. **Click the language switcher** in the top navigation bar
2. **Select your preferred language**:
   - 🇺🇸 **English** - Left-to-Right layout
   - 🇸🇦 **Arabic** - Right-to-Left layout
3. **Language changes immediately** and persists across sessions

#### RTL Experience (Arabic)
- **Navigation flows** from right to left
- **Text alignment** is properly adjusted
- **UI elements** are mirrored appropriately
- **Form inputs** maintain proper direction

### For Developers

#### Adding New Translations

1. **Add English translation** in `lang/en/admin.php`:
```php
'new_feature' => 'New Feature',
```

2. **Add Arabic translation** in `lang/ar/admin.php`:
```php
'new_feature' => 'ميزة جديدة',
```

3. **Use in Blade templates**:
```php
{{ __('admin.new_feature') }}
```

#### Adding New Languages

1. **Create language directory**:
```
lang/
└── fr/
    └── admin.php
```

2. **Update LanguageController**:
```php
$allowedLocales = ['en', 'ar', 'fr'];
```

3. **Update SetLocale middleware**:
```php
$allowedLocales = ['en', 'ar', 'fr'];
```

4. **Add language option** to language switcher component

## 📋 Translation Keys

### Navigation
- `dashboard` - Dashboard
- `units` - Units
- `sliders` - Sliders
- `users` - Users
- `reservations` - Reservations
- `logout` - Logout

### Common Actions
- `add_new` - Add New
- `edit` - Edit
- `delete` - Delete
- `save` - Save
- `cancel` - Cancel
- `update` - Update
- `create` - Create
- `view` - View
- `back` - Back

### Units Management
- `units_management` - Units Management
- `unit_name` - Unit Name
- `unit_number` - Unit Number
- `unit_type` - Unit Type
- `bedrooms` - Bedrooms
- `bathrooms` - Bathrooms
- `max_guests` - Max Guests
- `size_sqm` - Size (sqm)
- `address` - Address
- `description` - Description

### Pricing
- `pricing_information` - Pricing Information
- `base_price` - Base Price
- `weekend_price` - Weekend Price
- `holiday_price` - Holiday Price
- `cleaning_fee` - Cleaning Fee
- `per_night` - per night
- `pricing_summary` - Pricing Summary

### Status
- `available` - Available
- `occupied` - Occupied
- `maintenance` - Maintenance
- `reserved` - Reserved
- `active` - Active
- `inactive` - Inactive

### Messages
- `unit_created` - Unit created successfully
- `unit_updated` - Unit updated successfully
- `unit_deleted` - Unit deleted successfully
- `operation_successful` - Operation completed successfully
- `operation_failed` - Operation failed. Please try again.

## 🎯 Best Practices

### 1. Translation Keys
- **Use descriptive names** for translation keys
- **Group related keys** with prefixes (e.g., `unit_`, `slider_`)
- **Keep keys consistent** across languages

### 2. RTL Considerations
- **Test Arabic layout** thoroughly
- **Ensure proper text flow** in RTL mode
- **Check form inputs** and navigation alignment

### 3. Performance
- **Cache translations** in production
- **Use translation groups** for better organization
- **Minimize dynamic translations**

### 4. User Experience
- **Provide clear language indicators**
- **Maintain consistent terminology**
- **Test with native speakers**

## 🔧 Configuration

### Environment Variables
```env
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

### Bootstrap Configuration
**File:** `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
    
    // Add global middleware for locale setting
    $middleware->append(\App\Http\Middleware\SetLocale::class);
})
```

## 🧪 Testing

### Manual Testing
1. **Switch to Arabic** and verify RTL layout
2. **Test all admin pages** in both languages
3. **Verify form submissions** work correctly
4. **Check navigation** and breadcrumbs
5. **Test responsive design** in both languages

### Automated Testing
```php
// Test language switching
public function test_language_switching()
{
    $response = $this->get('/language/ar');
    $response->assertRedirect();
    $this->assertEquals('ar', session('locale'));
}
```

## 🚀 Future Enhancements

### Planned Features
- **User preference storage** in database
- **Auto-detection** of browser language
- **More languages** (French, Spanish, etc.)
- **Translation management** interface
- **Dynamic content translation**

### API Localization
- **API response localization**
- **Error message translations**
- **Date/time formatting** by locale
- **Number formatting** by locale

## 📞 Support

For questions or issues with the multi-language system:

1. **Check translation files** for missing keys
2. **Verify middleware** is properly registered
3. **Clear application cache** after changes
4. **Test in both languages** thoroughly

---

**🎉 The multi-language admin panel is now ready for use!**
