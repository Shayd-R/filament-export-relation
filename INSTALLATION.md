# Installation

## Install

```bash
composer require shayd-r/filament-smart-export
```

## Usage

Add to any Filament resource:

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

## Customization

```php
SmartExportBulkAction::make()
    ->label('Export to Excel')
    ->icon('heroicon-o-document-download')
    ->color('primary')
```

## Translations

Publish translations:

```bash
php artisan vendor:publish --tag="filament-smart-export-translations"
```

Available: English, Spanish
