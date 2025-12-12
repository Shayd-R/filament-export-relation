<?php

namespace ShaydR\FilamentSmartExport\Actions;

use Filament\Actions\BulkAction;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;
use OpenSpout\Writer\CSV\Writer as CSVWriter;
use OpenSpout\Common\Entity\Row;
use ShaydR\FilamentSmartExport\Actions\Concerns\CanDownloadDirect;
use ShaydR\FilamentSmartExport\Actions\Concerns\CanFormatStates;
use ShaydR\FilamentSmartExport\Actions\Concerns\CanHaveExtraColumns;
use ShaydR\FilamentSmartExport\Actions\Concerns\HasCsvDelimiter;
use ShaydR\FilamentSmartExport\Actions\Concerns\HasDefaultFormat;
use ShaydR\FilamentSmartExport\Actions\Concerns\HasFileName;

/**
 * Smart Export Bulk Action - Completely automatic export action for Filament
 * 
 * This action automatically discovers:
 * - The model from where it's invoked
 * - All model columns
 * - All relationships (BelongsTo, HasMany, etc.)
 * - Related model columns
 * 
 * Usage:
 * SmartExportBulkAction::make()
 * 
 * The action will automatically generate checkable dropdowns for each model:
 * - Main model with its columns
 * - Each related model with its columns
 * 
 * Supports multiple relationships (HasMany) by creating multiple rows in the export.
 * 
 * @package Shayd\FilamentSmartExport
 * @author Shayd
 * @license MIT
 */
class SmartExportBulkAction extends BulkAction
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
        return 'smart-export';
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
             ->modalWidth('7xl')
             ->form(function () {
                 $this->discoverModelFromContext();
                 return $this->getFormSchema();
             })
             ->action($this->getAction());
    }
    
    /**
     * Attempts to discover the model from Filament context
     */
    protected function discoverModelFromContext(): void
    {
        if ($this->modelClass) {
            return;
        }

        try {
            $livewire = $this->getLivewire();
            
            if ($livewire && property_exists($livewire, 'model')) {
                $this->modelClass = $livewire->model;
                $this->discoverModelStructure();
                return;
            }
            
            if ($livewire && method_exists($livewire, 'getModel')) {
                $this->modelClass = $livewire->getModel();
                $this->discoverModelStructure();
                return;
            }
            
            if ($livewire && method_exists($livewire, 'getTable')) {
                $table = $livewire->getTable();
                if ($table && method_exists($table, 'getModel')) {
                    $this->modelClass = $table->getModel();
                    $this->discoverModelStructure();
                    return;
                }
            }
        } catch (\Throwable $e) {
            // Fail silently
        }
    }

    /**
     * Discovers model structure: columns and relationships
     */
    protected function discoverModelStructure(): void
    {
        if (!$this->modelClass) {
            return;
        }

        $model = new $this->modelClass;
        $tableName = $model->getTable();

        $this->modelColumns[$this->modelClass] = Schema::getColumnListing($tableName);
        $this->discoverRelations($model);
    }

    /**
     * Discovers all relationships of the model (only direct relationships)
     */
    protected function discoverRelations(Model $model): void
    {
        $reflectionClass = new \ReflectionClass($model);
        
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class !== get_class($model) || 
                $method->getNumberOfParameters() > 0 ||
                Str::startsWith($method->name, '__')) {
                continue;
            }

            try {
                $return = $method->invoke($model);
                
                if ($return instanceof Relation) {
                    $relatedModel = get_class($return->getRelated());
                    $relationType = class_basename(get_class($return));
                    
                    $this->discoveredRelations[$method->name] = [
                        'name' => $method->name,
                        'type' => $relationType,
                        'model' => $relatedModel,
                        'isMultiple' => in_array($relationType, ['HasMany', 'BelongsToMany', 'MorphMany', 'MorphToMany']),
                    ];

                    $relatedInstance = new $relatedModel;
                    $relatedTable = $relatedInstance->getTable();
                    $this->modelColumns[$relatedModel] = Schema::getColumnListing($relatedTable);
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
    }

    /**
     * Generates an emoji based on field type
     */
    protected function getFieldEmoji(string $columnName): string
    {
        return match(true) {
            Str::contains($columnName, ['id', 'ID']) => '🔑',
            Str::contains($columnName, ['name', 'nombre', 'title', 'titulo']) => '📝',
            Str::contains($columnName, ['email', 'correo']) => '📧',
            Str::contains($columnName, ['phone', 'telefono', 'tel']) => '📞',
            Str::contains($columnName, ['date', 'fecha']) => '📅',
            Str::contains($columnName, ['created', 'updated', 'deleted']) => '🕐',
            Str::contains($columnName, ['status', 'estado']) => '🔄',
            Str::contains($columnName, ['user', 'usuario']) => '👤',
            Str::contains($columnName, ['price', 'precio', 'cost', 'costo']) => '💰',
            default => '📋',
        };
    }

    /**
     * Gets a readable name for a column
     */
    protected function getReadableColumnName(string $columnName): string
    {
        return Str::title(str_replace('_', ' ', $columnName));
    }

    public function getFormSchema(): array
    {
        if (!$this->modelClass) {
            return [
                Section::make(__('filament-smart-export::smart-export.error'))
                    ->schema([
                        Placeholder::make('error')
                            ->label(false)
                            ->content(new HtmlString('<div class="text-center p-4 text-red-500">' . __('filament-smart-export::smart-export.error_message') . '</div>'))
                    ])
            ];
        }

        $columnSchemas = $this->generateColumnDropdowns();

        return [
            Section::make(__('filament-smart-export::smart-export.configuration'))
                ->schema([
                    TextInput::make('filename')
                        ->label(__('filament-smart-export::smart-export.filename'))
                        ->required()
                        ->default('export'),
                    Select::make('format')
                        ->label(__('filament-smart-export::smart-export.format'))
                        ->options([
                            'xlsx' => 'Excel (XLSX)',
                            'csv' => 'CSV',
                        ])
                        ->default('xlsx')
                        ->required(),
                    DatePicker::make('date_from')
                        ->label(__('filament-smart-export::smart-export.date_from'))
                        ->live(),
                    DatePicker::make('date_to')
                        ->label(__('filament-smart-export::smart-export.date_to'))
                        ->live(),
                ])
                ->columns(2),
            
            Section::make(__('filament-smart-export::smart-export.columns_section'))
                ->description(__('filament-smart-export::smart-export.columns_description'))
                ->schema($columnSchemas)
                ->columns(2)
                ->collapsible(),
            
            Section::make(__('filament-smart-export::smart-export.preview'))
                ->description(__('filament-smart-export::smart-export.preview_description'))
                ->schema([
                    Placeholder::make('preview')
                        ->label(false)
                        ->content(function (Get $get) {
                            $selectedColumns = $this->collectSelectedColumns($get);
                            $dateFrom = $get('date_from');
                            $dateTo = $get('date_to');
                            
                            if (empty($selectedColumns)) {
                                return new HtmlString('<div class="text-center p-4 text-gray-500">' . __('filament-smart-export::smart-export.select_columns') . '</div>');
                            }
                            
                            $query = $this->modelClass::query();
                            
                            $relations = $this->extractRelationsToLoad($selectedColumns);
                            if (!empty($relations)) {
                                $query->with($relations);
                            }
                            
                            if ($dateFrom) {
                                $query->whereDate('created_at', '>=', $dateFrom);
                            }
                            if ($dateTo) {
                                $query->whereDate('created_at', '<=', $dateTo);
                            }
                            
                            $previewRecords = $query->limit(20)->get();
                            
                            if ($previewRecords->isEmpty()) {
                                return new HtmlString('<div class="text-center p-4 text-gray-500">' . __('filament-smart-export::smart-export.no_records') . '</div>');
                            }
                            
                            return new HtmlString($this->renderPreviewTable($previewRecords, $selectedColumns));
                        })
                        ->live(),
                ])
                ->columnSpanFull()
                ->collapsible(),
        ];
    }

    /**
     * Generates column dropdowns grouped by model
     */
    protected function generateColumnDropdowns(): array
    {
        $schemas = [];

        $mainModelName = class_basename($this->modelClass);
        $mainColumns = [];
        
        foreach ($this->modelColumns[$this->modelClass] as $column) {
            $emoji = $this->getFieldEmoji($column);
            $label = $this->getReadableColumnName($column);
            $mainColumns[$column] = "{$emoji} {$label}";
        }

        $schemas[] = CheckboxList::make('columns_main')
            ->label("📦 {$mainModelName} (" . __('filament-smart-export::smart-export.main_model') . ")")
            ->options($mainColumns)
            ->default(array_slice(array_keys($mainColumns), 0, 5))
            ->columns(2)
            ->bulkToggleable()
            ->searchable()
            ->live();

        foreach ($this->discoveredRelations as $relationName => $relationData) {
            $relatedModelName = class_basename($relationData['model']);
            $relatedColumns = [];
            
            if (isset($this->modelColumns[$relationData['model']])) {
                foreach ($this->modelColumns[$relationData['model']] as $column) {
                    $emoji = $this->getFieldEmoji($column);
                    $label = $this->getReadableColumnName($column);
                    $columnKey = "{$relationName}.{$column}";
                    $relatedColumns[$columnKey] = "{$emoji} {$label}";
                }
            }

            $relationTypeEmoji = $relationData['isMultiple'] ? '🔗' : '🔍';
            $relationTypeLabel = $relationData['isMultiple'] 
                ? __('filament-smart-export::smart-export.multiple') 
                : __('filament-smart-export::smart-export.single');

            $schemas[] = CheckboxList::make("columns_{$relationName}")
                ->label("{$relationTypeEmoji} {$relatedModelName} ({$relationTypeLabel})")
                ->options($relatedColumns)
                ->default([])
                ->columns(2)
                ->bulkToggleable()
                ->searchable()
                ->live()
                ->hint(__('filament-smart-export::smart-export.relationship') . ": {$relationData['type']}");
        }

        return $schemas;
    }

    /**
     * Collects all selected columns from all dropdowns
     */
    protected function collectSelectedColumns(Get $get): array
    {
        $allColumns = [];

        $mainColumns = $get('columns_main') ?? [];
        $allColumns = array_merge($allColumns, $mainColumns);

        foreach ($this->discoveredRelations as $relationName => $relationData) {
            $relationColumns = $get("columns_{$relationName}") ?? [];
            $allColumns = array_merge($allColumns, $relationColumns);
        }

        return $allColumns;
    }

    /**
     * Extracts relationships that need to be loaded
     */
    protected function extractRelationsToLoad(array $columns): array
    {
        $relations = [];
        
        foreach ($columns as $column) {
            if (Str::contains($column, '.')) {
                $parts = explode('.', $column);
                array_pop($parts);
                $relationPath = implode('.', $parts);
                
                if (!in_array($relationPath, $relations)) {
                    $relations[] = $relationPath;
                }
            }
        }

        return $relations;
    }

    /**
     * Renders the preview table
     */
    protected function renderPreviewTable($records, array $columns): string
    {
        $html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">';
        
        $html .= '<thead class="bg-gray-50 dark:bg-gray-800"><tr>';
        foreach ($columns as $column) {
            $label = $this->getColumnLabel($column);
            $html .= '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">' . htmlspecialchars($label) . '</th>';
        }
        $html .= '</tr></thead>';
        
        $html .= '<tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">';
        
        foreach ($records as $record) {
            $hasMultipleRelations = false;
            $multipleRelationData = [];
            
            foreach ($columns as $column) {
                if (Str::contains($column, '.')) {
                    $parts = explode('.', $column);
                    $relationName = $parts[0];
                    
                    if (isset($this->discoveredRelations[$relationName]) && 
                        $this->discoveredRelations[$relationName]['isMultiple']) {
                        $hasMultipleRelations = true;
                        
                        if (!isset($multipleRelationData[$relationName])) {
                            $multipleRelationData[$relationName] = $record->{$relationName} ?? collect();
                        }
                    }
                }
            }
            
            if ($hasMultipleRelations && !empty($multipleRelationData)) {
                $maxItems = max(array_map(fn($rel) => $rel->count(), $multipleRelationData));
                
                for ($i = 0; $i < $maxItems; $i++) {
                    $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-800">';
                    foreach ($columns as $column) {
                        $value = $this->getColumnValueDynamic($column, $record, $multipleRelationData, $i);
                        $html .= '<td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">' . htmlspecialchars($value) . '</td>';
                    }
                    $html .= '</tr>';
                }
            } else {
                $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-800">';
                foreach ($columns as $column) {
                    $value = $this->getColumnValueDynamic($column, $record, [], 0);
                    $html .= '<td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">' . htmlspecialchars($value) . '</td>';
                }
                $html .= '</tr>';
            }
        }
        
        $html .= '</tbody></table></div>';
        
        return $html;
    }

    public function getAction(): \Closure
    {
        return function (Collection $records, array $data) {
            if (!$this->modelClass && $records->isNotEmpty()) {
                $this->modelClass = get_class($records->first());
                $this->discoverModelStructure();
            }
            
            $selectedColumns = [];
            
            $mainColumns = $data['columns_main'] ?? [];
            $selectedColumns = array_merge($selectedColumns, $mainColumns);
            
            foreach ($this->discoveredRelations as $relationName => $relationData) {
                $relationColumns = $data["columns_{$relationName}"] ?? [];
                $selectedColumns = array_merge($selectedColumns, $relationColumns);
            }
            
            if (empty($selectedColumns)) {
                Notification::make()
                    ->title(__('filament-smart-export::smart-export.no_columns_title'))
                    ->body(__('filament-smart-export::smart-export.no_columns_body'))
                    ->warning()
                    ->send();
                return;
            }

            $recordIds = $records->pluck('id')->unique();
            
            $relations = $this->extractRelationsToLoad($selectedColumns);
            $query = $this->modelClass::query()->whereIn('id', $recordIds);
            
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            if (!empty($data['date_from'])) {
                $query->whereDate('created_at', '>=', $data['date_from']);
            }
            if (!empty($data['date_to'])) {
                $query->whereDate('created_at', '<=', $data['date_to']);
            }
            
            $exportRecords = $query->get();
            
            if ($exportRecords->isEmpty()) {
                Notification::make()
                    ->title(__('filament-smart-export::smart-export.no_records_title'))
                    ->body(__('filament-smart-export::smart-export.no_records_body'))
                    ->warning()
                    ->send();
                return;
            }

            $format = $data['format'];
            $filename = $data['filename'] . '-' . now()->format('Y-m-d-His') . '.' . $format;
            
            return response()->streamDownload(function () use ($exportRecords, $selectedColumns, $format) {
                $writer = $format === 'csv' ? new CSVWriter() : new XLSXWriter();
                
                if ($format === 'csv' && method_exists($writer, 'setFieldDelimiter')) {
                    $writer->setFieldDelimiter($this->getCsvDelimiter());
                }
                
                $writer->openToFile('php://output');
                
                $headers = [];
                foreach ($selectedColumns as $column) {
                    $headers[] = $this->getColumnLabel($column);
                }
                $writer->addRow(Row::fromValues($headers));
                
                $totalRows = 0;
                foreach ($exportRecords as $record) {
                    $hasMultipleRelations = false;
                    $multipleRelationData = [];
                    
                    foreach ($selectedColumns as $column) {
                        if (Str::contains($column, '.')) {
                            $parts = explode('.', $column);
                            $relationName = $parts[0];
                            
                            if (isset($this->discoveredRelations[$relationName]) && 
                                $this->discoveredRelations[$relationName]['isMultiple']) {
                                $hasMultipleRelations = true;
                                
                                if (!isset($multipleRelationData[$relationName])) {
                                    $multipleRelationData[$relationName] = $record->{$relationName} ?? collect();
                                }
                            }
                        }
                    }
                    
                    if ($hasMultipleRelations && !empty($multipleRelationData)) {
                        $maxItems = max(array_map(fn($rel) => $rel->count(), $multipleRelationData));
                        
                        for ($i = 0; $i < max($maxItems, 1); $i++) {
                            $rowData = [];
                            foreach ($selectedColumns as $column) {
                                $rowData[] = $this->getColumnValueDynamic($column, $record, $multipleRelationData, $i);
                            }
                            $writer->addRow(Row::fromValues($rowData));
                            $totalRows++;
                        }
                    } else {
                        $rowData = [];
                        foreach ($selectedColumns as $column) {
                            $rowData[] = $this->getColumnValueDynamic($column, $record, [], 0);
                        }
                        $writer->addRow(Row::fromValues($rowData));
                        $totalRows++;
                    }
                }
                
                $writer->close();
            }, $filename, [
                'Content-Type' => $format === 'csv' 
                    ? 'text/csv' 
                    : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        };
    }

    /**
     * Gets the label dynamically for a column
     */
    protected function getColumnLabel(string $column): string
    {
        if (!Str::contains($column, '.')) {
            return $this->getReadableColumnName($column);
        }
        
        $parts = explode('.', $column);
        $columnName = array_pop($parts);
        $relationPath = implode('.', $parts);
        
        $relationData = $this->discoveredRelations[$relationPath] ?? null;
        if ($relationData) {
            $modelName = class_basename($relationData['model']);
            $columnLabel = $this->getReadableColumnName($columnName);
            return "{$modelName} - {$columnLabel}";
        }
        
        return $this->getReadableColumnName($columnName);
    }

    /**
     * Gets the value dynamically for a column
     */
    protected function getColumnValueDynamic(string $column, $record, array $multipleRelationData = [], int $index = 0)
    {
        if (!Str::contains($column, '.')) {
            $value = $record->{$column} ?? '';
            
            if ($value instanceof \Carbon\Carbon) {
                return $value->format('Y-m-d H:i:s');
            }
            
            if (is_bool($value)) {
                return $value ? __('filament-smart-export::smart-export.yes') : __('filament-smart-export::smart-export.no');
            }
            
            return $value;
        }
        
        $parts = explode('.', $column);
        $columnName = array_pop($parts);
        
        $current = $record;
        foreach ($parts as $relationName) {
            if (!$current) {
                return '';
            }
            
            if (isset($this->discoveredRelations[$relationName]) && 
                $this->discoveredRelations[$relationName]['isMultiple']) {
                
                if (isset($multipleRelationData[$relationName])) {
                    $collection = $multipleRelationData[$relationName];
                    $current = $collection->get($index);
                } else {
                    $relation = $current->{$relationName} ?? collect();
                    $current = $relation->get($index);
                }
            } else {
                $current = $current->{$relationName} ?? null;
            }
        }
        
        if (!$current) {
            return '';
        }
        
        $value = $current->{$columnName} ?? '';
        
        if ($value instanceof \Carbon\Carbon) {
            return $value->format('Y-m-d H:i:s');
        }
        
        if (is_bool($value)) {
            return $value ? __('filament-smart-export::smart-export.yes') : __('filament-smart-export::smart-export.no');
        }
        
        return $value;
    }
}
