# Filament Smart Export

Automatic export action for Filament that discovers your model structure and relationships.

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

## Requirements

- PHP 8.1+
- Laravel 10+ / 11+
- Filament 3.0+

## License

MIT
