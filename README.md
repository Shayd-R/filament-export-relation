# Filament Smart Export

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shayd/filament-smart-export.svg?style=flat-square)](https://packagist.org/packages/shayd/filament-smart-export)
[![Total Downloads](https://img.shields.io/packagist/dt/shayd/filament-smart-export.svg?style=flat-square)](https://packagist.org/packages/shayd/filament-smart-export)

**Filament Smart Export** is a completely automatic export action for Filament that intelligently discovers your model structure and relationships, generating dynamic export forms with zero configuration.

## ✨ Features

- 🔍 **Auto-Discovery**: Automatically detects models, columns, and relationships
- 📦 **Grouped Dropdowns**: One dropdown per model (main + related)
- 🔗 **Relationship Support**: Handles BelongsTo, HasMany, BelongsToMany, etc.
- 😊 **Smart Emojis**: Auto-assigns appropriate emojis based on field type
- 👁️ **Live Preview**: Real-time preview of export data
- 📊 **Multiple Formats**: Export to XLSX or CSV
- 🌐 **Multilingual**: Supports English and Spanish (extendable)
- ⚡ **Zero Configuration**: Just add one line of code
- ⚡ **Direct Download**: Files stream directly to browser without disk storage

## 📦 Installation

You can install the package via composer:

```bash
composer require shayd/filament-smart-export
```

## 🚀 Usage

### Basic Usage

Add the action to any Filament table:

```php
use Shayd\FilamentSmartExport\Actions\SmartExportBulkAction;

public static function table(Table $table): Table
{
    return $table
        ->columns([...])
        ->bulkActions([
            SmartExportBulkAction::make(),
        ]);
}
```

That's it! The action will automatically:
- Detect your model
- Find all columns
- Discover all relationships
- Generate grouped dropdowns
- Handle export logic

### What You Get Automatically

For a model like `ObservationRecord` with relationships `shift`, `area`, `user`, and `observations`, you'll see:

```
📦 ObservationRecord (Main Model)
  ☑ 🔑 Id
  ☑ 🕐 Created At
  ...

🔍 Shift (Single)
  ☐ 📝 Name
  ☐ 🔑 Id
  ...

🔍 Area (Single)
  ☐ 📝 Name
  ...

🔍 User (Single)
  ☐ 📝 Name
  ☐ 📧 Email
  ...

🔗 Observations (Multiple)
  ☐ 🔑 Id
  ☐ 📋 Gloves
  ...
```

### Multiple Relationships

The package intelligently handles HasMany relationships by creating multiple rows in the export:

**Example:**
If `ObservationRecord #1` has 3 observations:

| ID | Area | Observation ID | Profile | Action |
|----|------|----------------|---------|--------|
| 1  | ICU  | 10             | Doctor  | Before |
| 1  | ICU  | 11             | Doctor  | After  |
| 1  | ICU  | 12             | Doctor  | During |

## 🎨 Customization

### Custom Label

```php
SmartExportBulkAction::make()
    ->label('Export Data')
```

### Custom Icon

```php
SmartExportBulkAction::make()
    ->icon('heroicon-o-document-download')
```

### Custom Color

```php
SmartExportBulkAction::make()
    ->color('primary')
```

## 🌍 Translations

The package includes English and Spanish translations. To publish the translations:

```bash
php artisan vendor:publish --tag="filament-smart-export-translations"
```

You can add more languages by creating files in:
```
resources/lang/vendor/filament-smart-export/{locale}/smart-export.php
```

## 📋 Requirements

- PHP 8.1+
- Laravel 10.0+
- Filament 3.0+

## 🔧 How It Works

### 1. Model Discovery
Uses Filament's context to automatically detect the current model

### 2. Column Detection
Leverages Laravel's Schema Builder to get all table columns

### 3. Relationship Discovery
Uses PHP Reflection to discover all relationship methods

### 4. Dynamic UI Generation
Creates checkable dropdowns grouped by model

### 5. Smart Export
Handles simple and multiple relationships intelligently

## 💡 Use Cases

### Any Model Works!

```php
// Users with roles
SmartExportBulkAction::make() 
// → Detects: User, roles, permissions, profile

// Orders with items
SmartExportBulkAction::make()
// → Detects: Order, items, customer, shipping

// Posts with comments
SmartExportBulkAction::make()
// → Detects: Post, comments, author, tags

// YOUR MODEL HERE
SmartExportBulkAction::make()
// → Detects: Everything automatically! ✨
```

## 📊 Export Features

- **Date Filters**: Filter by date range before exporting
- **Live Preview**: See first 20 rows before exporting
- **Searchable Dropdowns**: Find columns quickly
- **Bulk Toggle**: Select/deselect all columns at once
- **Database Notifications**: Get download link in notifications
- **File Size Info**: Shows file size in notification

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 🙏 Credits

- **Shayd** - Creator and Maintainer
- Built for [Filament](https://filamentphp.com)
- Uses [OpenSpout](https://github.com/openspout/openspout) for file generation

## 🐛 Issues & Support

If you encounter any issues or have questions:
- Open an issue on [GitHub](https://github.com/shayd/filament-smart-export/issues)

## ⭐ Show Your Support

If you find this package helpful, please consider giving it a ⭐ on [GitHub](https://github.com/shayd/filament-smart-export)!

---

Made with ❤️ for the Filament community
