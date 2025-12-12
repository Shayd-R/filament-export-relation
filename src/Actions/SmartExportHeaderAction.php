<?php

namespace Shayd\FilamentSmartExport\Actions;

use Filament\Tables\Actions\Action;
use Shayd\FilamentSmartExport\Actions\Concerns\CanDownloadDirect;
use Shayd\FilamentSmartExport\Actions\Concerns\CanFormatStates;
use Shayd\FilamentSmartExport\Actions\Concerns\CanHaveExtraColumns;
use Shayd\FilamentSmartExport\Actions\Concerns\HasCsvDelimiter;
use Shayd\FilamentSmartExport\Actions\Concerns\HasDefaultFormat;
use Shayd\FilamentSmartExport\Actions\Concerns\HasFileName;

/**
 * Smart Export Header Action - Export entire table without selection
 * 
 * This action exports all records from the table, optionally filtered by table filters.
 * 
 * Usage:
 * SmartExportHeaderAction::make()
 * 
 * @package Shayd\FilamentSmartExport
 */
class SmartExportHeaderAction extends Action
{
    use CanDownloadDirect;
    use CanFormatStates;
    use CanHaveExtraColumns;
    use HasCsvDelimiter;
    use HasDefaultFormat;
    use HasFileName;

    protected ?string $modelClass = null;
    protected array $discoveredRelations = [];
    protected array $modelColumns = [];

    public static function getDefaultName(): ?string
    {
        return 'smart-export-header';
    }

    /**
     * Create a new instance of the action
     */
    public static function make(?string $name = null): static
    {
        $action = parent::make($name ?? static::getDefaultName());
        return $action;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-smart-export::smart-export.label'))
             ->icon('heroicon-o-arrow-down-tray')
             ->color('success')
             ->modalHeading(__('filament-smart-export::smart-export.modal_heading'))
             ->modalSubmitActionLabel(__('filament-smart-export::smart-export.modal_submit'))
             ->modalWidth('7xl');
             
        // Aquí puedes agregar la lógica del form y action similar a SmartExportBulkAction
        // pero trabajando con todos los registros de la tabla
    }
}
