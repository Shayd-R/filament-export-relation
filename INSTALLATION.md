# Installation & Usage Guide

## 📦 Installation

### Step 1: Install via Composer

```bash
composer require shayd/filament-smart-export
```

## 🚀 Basic Usage

### Add to Any Filament Resource

```php
<?php

namespace App\Filament\Resources;

use Shayd\FilamentSmartExport\Actions\SmartExportBulkAction;
use Filament\Tables\Table;

class UserResource extends Resource
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                // ... more columns
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                SmartExportBulkAction::make(), // ✨ Add this line
            ]);
    }
}
```

## 🎯 Examples

### Example 1: Users with Roles

```php
// Model: User
// Relationships: roles, permissions, profile

use Shayd\FilamentSmartExport\Actions\SmartExportBulkAction;

public static function table(Table $table): Table
{
    return $table
        ->columns([...])
        ->bulkActions([
            SmartExportBulkAction::make(),
        ]);
}

// Automatically generates:
// 📦 User (Main Model)
//   - id, name, email, created_at, etc.
// 🔗 Roles (Multiple)
//   - id, name, guard_name
// 🔗 Permissions (Multiple)
//   - id, name, guard_name
// 🔍 Profile (Single)
//   - id, bio, avatar, etc.
```

### Example 2: Orders with Items

```php
// Model: Order
// Relationships: items, customer, shipping

SmartExportBulkAction::make()

// Automatically generates:
// 📦 Order (Main Model)
//   - id, order_number, total, status
// 🔗 Items (Multiple)  ← Creates multiple rows per order
//   - id, product_name, quantity, price
// 🔍 Customer (Single)
//   - id, name, email, phone
// 🔍 Shipping (Single)
//   - id, address, city, country
```

### Example 3: Customized Action

```php
SmartExportBulkAction::make()
    ->label('Export to Excel')
    ->icon('heroicon-o-document-download')
    ->color('primary')
    ->modalHeading('Export Orders')
    ->modalSubmitActionLabel('Download')
```

## 🎨 Customization

### Change Labels

```php
SmartExportBulkAction::make()
    ->label('Download Data')
    ->modalHeading('Export Configuration')
    ->modalSubmitActionLabel('Generate File')
```

### Change Icon and Color

```php
SmartExportBulkAction::make()
    ->icon('heroicon-o-arrow-down-tray')
    ->color('success')
```

### Change Modal Width

```php
SmartExportBulkAction::make()
    ->modalWidth('6xl') // or 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '7xl'
```

## 🌍 Translations

### Publishing Translations

```bash
php artisan vendor:publish --tag="filament-smart-export-translations"
```

### Adding New Language

Create a file in `resources/lang/vendor/filament-smart-export/{locale}/smart-export.php`:

```php
<?php

return [
    'label' => 'Exportación Inteligente',
    'modal_heading' => 'Configurar Exportación',
    // ... more translations
];
```

### Available Languages

- 🇬🇧 English (`en`)
- 🇪🇸 Spanish (`es`)

Want to contribute a translation? PR welcome!

## 📊 Export Behavior

### Single Relationships (BelongsTo, HasOne)

**1 Model Record = 1 Export Row**

```
Order #1 → Customer "John"
Export: 1 row
```

### Multiple Relationships (HasMany, BelongsToMany)

**1 Model Record = N Export Rows**

```
Order #1 → 3 Items
Export: 3 rows (one per item)

| Order ID | Customer | Item Name | Quantity |
|----------|----------|-----------|----------|
| 1        | John     | Product A | 2        |
| 1        | John     | Product B | 1        |
| 1        | John     | Product C | 3        |
```

## 🔧 Troubleshooting

### Modal doesn't open

```bash
# Clear Filament cache
php artisan filament:clear-cached-components

# Rebuild assets
npm run build
```

### Dropdowns are empty

**Check:**
1. Model has relationship methods defined
2. Related tables exist in database
3. Check logs: `storage/logs/laravel.log`

### File doesn't download

```bash
# Recreate storage link
php artisan storage:link

# Check permissions
chmod -R 775 storage/app/public/exports

# Check .htaccess or nginx config
```

### Preview doesn't update

1. Make sure columns have `->live()` (automatic)
2. Clear browser cache
3. Check JavaScript console (F12)

## 🎓 Advanced Usage

### Accessing Selected Records

The action works with Filament's bulk selection. Users select records in the table, then click the export action.

### Date Filters

The modal includes optional date filters:
- **Date From**: Export records created after this date
- **Date To**: Export records created before this date

### Search in Dropdowns

Each dropdown has a built-in search box to quickly find columns.

### Bulk Toggle

Click "Toggle All" in any dropdown to select/deselect all columns at once.

## 📝 Export Process

1. User selects records in table
2. Clicks "Smart Export" action
3. Modal opens with auto-generated dropdowns
4. User selects desired columns
5. Preview updates in real-time
6. User configures filename and format
7. Clicks "Generate Export"
8. File is created in `storage/app/public/exports/`
9. Database notification sent with download link
10. Toast notification confirms success

## 💾 File Storage

Exported files are stored in:
```
storage/app/public/exports/
```

File naming format:
```
{filename}-{date}-{time}.{format}
Example: export-2025-12-11-143025.xlsx
```

## 🔐 Permissions

Make sure your `storage` directory has proper permissions:

```bash
chmod -R 775 storage
chown -R www-data:www-data storage  # Linux
```

## ⚡ Performance

For large exports (1000+ records):
- Consider using queued jobs
- Implement pagination
- Add memory limit checks

The package is optimized for:
- Up to 1000 records: Instant
- 1000-5000 records: Fast (< 10 seconds)
- 5000+ records: May take longer

## 🚨 Common Pitfalls

### ❌ Don't do this:
```php
// Wrong namespace
use App\Filament\Actions\SmartExportBulkAction;
```

### ✅ Do this:
```php
// Correct namespace
use Shayd\FilamentSmartExport\Actions\SmartExportBulkAction;
```

### ❌ Don't do this:
```php
// Forgetting to select records
SmartExportBulkAction::make()->action(...)
```

### ✅ Do this:
```php
// Let users select records first
SmartExportBulkAction::make()
```

## 📞 Support

Need help?
- 📖 [Documentation](https://github.com/shayd/filament-smart-export)
- 🐛 [Issues](https://github.com/shayd/filament-smart-export/issues)
- 💬 [Discussions](https://github.com/shayd/filament-smart-export/discussions)

## 🎉 You're Ready!

Your Filament Smart Export is now configured and ready to use. Just add one line to any resource and enjoy automatic exports!

```php
SmartExportBulkAction::make()
```

Happy exporting! 🚀
