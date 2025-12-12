# Filament Smart Export

Automatic export action for Filament v4 that discovers your model structure and relationships.

## Features

- Auto-discovers models, columns, and relationships
- Export to XLSX or CSV
- Live preview of data
- Multiple relationship support (HasMany, BelongsToMany)
- Direct download (no storage needed)

## Installation

```bash
composer require shayd-r/filament-smart-export
```

## Usage

### Bulk Action (for selected records)

```php
use ShaydR\FilamentSmartExport\Actions\SmartExportBulkAction;

public static function table(Table $table): Table
{
    return $table
        ->bulkActions([
            SmartExportBulkAction::make(),
        ]);
}
```

### Header Action (for all records)

```php
use ShaydR\FilamentSmartExport\Actions\SmartExportHeaderAction;

public static function table(Table $table): Table
{
    return $table
        ->headerActions([
            SmartExportHeaderAction::make(),
        ]);
}
```

## Version Compatibility

- **Filament v4**: Use version `^2.0` (main branch)
- **Filament v3**: Use version `^1.0` (filament-v3 branch)

## Requirements

- PHP 8.1+
- Laravel 11+ / 12+
- Filament 4.0+

## License

MIT
