# Laravel Settings Service

A flexible and powerful Laravel package for managing application settings with multi-language support and various data structures.

## Features

- **Multi-language Support**: Built-in localization for settings
- **Multiple Data Types**: Support for strings, arrays, and nested structures
- **Flexible Storage**: Store single values or collections
- **ArrayAccess Implementation**: Access settings like arrays
- **Database-backed**: Persistent storage using Eloquent
- **Performance**: Efficient data retrieval and caching
- **Blade Directive**: Custom Blade directive for easy iteration

## Installation

1. Add the `SettingsServices` class to your project:
```bash
app/Services/Settings/SettingsServices.php
```

2. Add the helper function to your `app/helpers.php` or create it:
```php
<?php
use \App\Services\Settings\SettingsServices;

if (!function_exists('setting')) {
    function setting(?string $key, ?string $lang = null): SettingsServices
    {
        return new SettingsServices($key, $lang);
    }
}
```

3. Create the settings migration:
```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->timestamps();
});
```

4. Create the `Setting` model:
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];
}
```

## Usage

### 1. Logo/File Path

Store and retrieve file paths:

```php

setting('website')->save([
    'logo_path' => 'website/old_logo.png',
]);

// Get
$logoPath = setting('website')->logo_path; // "website/old_logo.png"
```

### 2. Single Item (Non-localized)

Store simple key-value pairs:

```php

$items = [
    'name'  => 'name value new',
    'email' => 'email value',
];
setting('item_single')->save($items);

// Get
$name = setting('item_single')->name;   // "name value new"
$email = setting('item_single')->email; // "email value"
```

### 3. Single Item (Multi-language)

Store localized key-value pairs:

```php

$items = [
    'name'  => ['ar' => 'name ar', 'en' => 'name en'],
    'email' => ['ar' => 'email ar', 'en' => 'email en'],
];
setting('single_language')->save($items);

// Get (uses current app locale)
$name = setting('single_language')->name;  // Returns based on app()->getLocale()
$email = setting('single_language')->email;

// Get specific language
$nameAr = setting('single_language', 'ar')->name; // "name ar"
$nameEn = setting('single_language', 'en')->name; // "name en"
```

### 4. Single Multiple Items (Non-localized)

Store collections of items:

```php

$items = [
    ['name' => 'name value 1', 'email' => 'email value 1'],
    ['name' => 'name value 2', 'email' => 'email value 2'],
];
setting('single_multiple_items')->save($items);

// Get all items
foreach (setting('single_multiple_items')->get() as $item) {
    echo $item->name;  // "name value 1", "name value 2"
    echo $item->email; // "email value 1", "email value 2"
}

// Or use each() method
setting('single_multiple_items')->each(function ($item) {
    echo "{$item->name} - {$item->email}";
});
```

**Blade Usage:**
```blade
@eachSetting('single_multiple_items')
    <div>{{ $item->name }} - {{ $item->email }}</div>
@endEachSetting
```

### 5. Single Multiple Items (Multi-language)

Store collections with localized content:

```php

$items = [
    [
        'name'  => ['ar' => 'name value 1 ar', 'en' => 'name value 1 en'],
        'email' => ['ar' => 'email value 1 ar', 'en' => 'email value 1 en'],
    ],
    [
        'name'  => ['ar' => 'name value 2 ar', 'en' => 'name value 2 en'],
        'email' => ['ar' => 'email value 2 ar', 'en' => 'email value 2 en'],
    ],
];
setting('single_multiple_language_items')->save($items);

// Get items (respects current locale)
foreach (setting('single_multiple_language_items')->get() as $item) {
    echo $item->name;  // Returns based on app()->getLocale()
    echo $item->email;
}

// Or use each() method
setting('single_multiple_language_items')->each(function ($item) {
    echo "{$item->name} - {$item->email}";
});
```

**Blade Usage:**
```blade
@eachSetting('single_multiple_language_items')
    <div>{{ $item->name }} - {{ $item->email }}</div>
@endEachSetting
```

### 6. Multiple Items (Non-localized)

Store multiple items:

```php

$items = [
    ['name' => 'name 1 multiples', 'email' => 'email 1 multiples'],
    ['name' => 'name 2 multiples', 'email' => 'email 2 multiples'],
];
setting('multiple_items')->save($items);

// Get all items
foreach (setting('multiple_items')->get() as $item) {
    echo $item->name;  // "name 1 multiples", "name 2 multiples"
    echo $item->email; // "email 1 multiples", "email 2 multiples"
}

// Or use each() method
setting('multiple_items')->each(function ($item) {
    echo "{$item->name} - {$item->email}";
});
```

**Blade Usage:**
```blade
@eachSetting('multiple_items')
    <div>{{ $item->name }} - {{ $item->email }}</div>
@endEachSetting
```

### 7. Multiple Items (Multi-language)

Store multiple items with localization:

```php

$items = [
    [
        'name'  => ['ar' => 'name value 1 ar', 'en' => 'name value 1 en'],
        'email' => ['ar' => 'email value 1 ar', 'en' => 'email value 1 en'],
    ],
    [
        'name'  => ['ar' => 'name value 2 ar', 'en' => 'name value 2 en'],
        'email' => ['ar' => 'email value 2 ar', 'en' => 'email value 2 en'],
    ],
];
setting('multiple_language_items')->save($items);

// Get items (respects current locale)
foreach (setting('multiple_language_items')->get() as $item) {
    echo $item->name;  // Returns based on app()->getLocale()
    echo $item->email;
}

// Or use each() method
setting('multiple_language_items')->each(function ($item) {
    echo "{$item->name} - {$item->email}";
});

// Get items in specific language
setting('multiple_language_items', 'ar')->each(function ($item) {
    echo "{$item->name} - {$item->email}"; // Arabic content
});
```

**Blade Usage:**
```blade
@eachSetting('multiple_language_items')
    <div>{{ $item->name }} - {{ $item->email }}</div>
@endEachSetting
```

## Blade Directive

Use the custom `@eachSetting` directive in your Blade templates for iterating over multiple items.

### Register the Directive

Add to your `AppServiceProvider`:

```php

use Illuminate\Support\Facades\Blade;

public function boot()
{
    Blade::directive('eachSetting', function ($expression) {
        return "<?php foreach(setting({$expression})->get() as \$item): ?>";
    });
    
    Blade::directive('endEachSetting', function () {
        return "<?php endforeach; ?>";
    });
}
```

### Blade Examples

All examples using `@eachSetting` are shown in the usage section above for items 4-7.

## Advanced Usage

### ArrayAccess

The service implements `ArrayAccess`, allowing array-like access:

```php

$items = ['name' => 'John', 'email' => 'john@example.com'];
setting('user')->save($items);

// Check if key exists
if (isset(setting('user')['name'])) {
    // Key exists
}

// Get value
$name = setting('user')['name'];
```

### Get Raw Array

Retrieve the raw stored value:

```php
$rawData = setting('users')->toArray();
```

### Method Chaining

```php
setting('users')
    ->each(function ($user, $index) {
        echo "User {$index}: {$user->name}";
    });
```

## API Reference

### Methods

| Method | Description |
|--------|-------------|
| `save(array\|string $data)` | Save data to database |
| `get()` | Get all items (for arrays) with locale processing |
| `each(callable $callback)` | Iterate over items with callback |
| `toArray()` | Get raw value as array |
| `__get($property)` | Magic getter for accessing properties |

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | string | Unique identifier for the setting |
| `$lang` | string\|null | Language code (defaults to `app()->getLocale()`) |

## How It Works

1. **Storage**: Data is stored as JSON in the database
2. **Retrieval**: JSON is decoded and processed based on structure
3. **Localization**: Multi-language values are automatically resolved based on current or specified locale
4. **Fallback**: If a locale key doesn't exist, falls back to the first available value

## Requirements

- Laravel 8.x or higher
- PHP 8.0 or higher

## License

This package is open-sourced software licensed under the MIT license.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

If you encounter any issues or have questions, please open an issue on the repository.

---

Made with ❤️ for the Laravel community