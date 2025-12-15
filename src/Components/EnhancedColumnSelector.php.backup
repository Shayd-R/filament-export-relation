<?php

namespace ShaydR\FilamentSmartExport\Components;

use Livewire\Component;
use Illuminate\Support\Collection;

/**
 * Enhanced Column Selector Livewire Component
 * 
 * Maneja la lógica de selección de columnas con soporte para:
 * - Búsqueda en tiempo real
 * - Select all por sección
 * - Campos BelongsTo con selects
 * - Relaciones HasMany colapsables
 */
class EnhancedColumnSelector extends Component
{
    // Datos del modelo
    public string $modelName = '';
    public array $columns = [];
    public array $relations = [];
    
    // Estado de selección
    public array $selectedColumns = [
        'main' => [],
        'relations' => [],
    ];
    
    // Estado de búsqueda
    public array $searchQueries = [];
    
    // Estado de expansión de relaciones
    public bool $hasManyExpanded = false;

    /**
     * Mount component
     */
    public function mount(
        string $modelName,
        array $columns,
        array $relations,
        array $selectedColumns = []
    ): void {
        $this->modelName = $modelName;
        $this->columns = $columns;
        $this->relations = $relations;
        
        if (!empty($selectedColumns)) {
            $this->selectedColumns = $selectedColumns;
        } else {
            $this->initializeDefaultSelections();
        }
    }

    /**
     * Initialize default selections (first 5 columns of main model)
     */
    protected function initializeDefaultSelections(): void
    {
        $count = 0;
        foreach ($this->columns as $key => $column) {
            if ($count >= 5) {
                break;
            }
            
            if ($column['type'] === 'simple') {
                $this->selectedColumns['main'][$key] = true;
            } else {
                // For BelongsTo, select first option by default
                $this->selectedColumns['main'][$key] = [
                    'enabled' => true,
                    'field' => array_key_first($column['options'] ?? []),
                ];
            }
            
            $count++;
        }
    }

    /**
     * Select all columns in main model
     */
    public function selectAllMain(): void
    {
        foreach ($this->columns as $key => $column) {
            if ($column['type'] === 'simple') {
                $this->selectedColumns['main'][$key] = true;
            } else {
                $this->selectedColumns['main'][$key] = [
                    'enabled' => true,
                    'field' => $this->selectedColumns['main'][$key]['field'] ?? array_key_first($column['options'] ?? []),
                ];
            }
        }
    }

    /**
     * Deselect all columns in main model
     */
    public function deselectAllMain(): void
    {
        foreach ($this->columns as $key => $column) {
            if ($column['type'] === 'simple') {
                $this->selectedColumns['main'][$key] = false;
            } else {
                $this->selectedColumns['main'][$key]['enabled'] = false;
            }
        }
    }

    /**
     * Select all columns in a specific relation
     */
    public function selectAllRelation(string $relationKey): void
    {
        if (!isset($this->relations[$relationKey])) {
            return;
        }

        foreach ($this->relations[$relationKey]['columns'] as $key => $column) {
            if ($column['type'] === 'simple') {
                $this->selectedColumns['relations'][$relationKey][$key] = true;
            } else {
                $this->selectedColumns['relations'][$relationKey][$key] = [
                    'enabled' => true,
                    'field' => $this->selectedColumns['relations'][$relationKey][$key]['field'] ?? array_key_first($column['options'] ?? []),
                ];
            }
        }
    }

    /**
     * Deselect all columns in a specific relation
     */
    public function deselectAllRelation(string $relationKey): void
    {
        if (!isset($this->relations[$relationKey])) {
            return;
        }

        foreach ($this->relations[$relationKey]['columns'] as $key => $column) {
            if ($column['type'] === 'simple') {
                $this->selectedColumns['relations'][$relationKey][$key] = false;
            } else {
                $this->selectedColumns['relations'][$relationKey][$key]['enabled'] = false;
            }
        }
    }

    /**
     * Get all selected columns for export
     * Returns array in format: ['column_name' => 'Label', 'relation.column' => 'Label']
     */
    public function getSelectedColumnsForExport(): array
    {
        $result = [];

        // Process main model columns
        foreach ($this->selectedColumns['main'] as $key => $value) {
            if (is_bool($value) && $value === true) {
                // Simple field
                $result[$key] = $this->columns[$key]['label'] ?? $key;
            } elseif (is_array($value) && ($value['enabled'] ?? false)) {
                // BelongsTo field
                $field = $value['field'] ?? 'id';
                $relation = $this->columns[$key]['relation'] ?? '';
                $columnKey = $relation ? "{$relation}.{$field}" : $key;
                $result[$columnKey] = $this->columns[$key]['label'] ?? $key;
            }
        }

        // Process relations
        foreach ($this->selectedColumns['relations'] as $relationKey => $columns) {
            foreach ($columns as $columnKey => $value) {
                if (is_bool($value) && $value === true) {
                    // Simple field in relation
                    $fullKey = "{$relationKey}.{$columnKey}";
                    $label = $this->relations[$relationKey]['columns'][$columnKey]['label'] ?? $columnKey;
                    $result[$fullKey] = "{$this->relations[$relationKey]['name']} - {$label}";
                } elseif (is_array($value) && ($value['enabled'] ?? false)) {
                    // BelongsTo field in relation
                    $field = $value['field'] ?? 'id';
                    $nestedRelation = $this->relations[$relationKey]['columns'][$columnKey]['relation'] ?? '';
                    $fullKey = $nestedRelation 
                        ? "{$relationKey}.{$nestedRelation}.{$field}"
                        : "{$relationKey}.{$columnKey}";
                    $label = $this->relations[$relationKey]['columns'][$columnKey]['label'] ?? $columnKey;
                    $result[$fullKey] = "{$this->relations[$relationKey]['name']} - {$label}";
                }
            }
        }

        return $result;
    }

    /**
     * Get filtered columns based on search query
     */
    public function getFilteredColumns(string $section = 'main', ?string $relationKey = null): array
    {
        $searchQuery = strtolower($this->searchQueries[$section] ?? '');
        
        if (empty($searchQuery)) {
            return $section === 'main' 
                ? $this->columns 
                : ($this->relations[$relationKey]['columns'] ?? []);
        }

        $columns = $section === 'main' 
            ? $this->columns 
            : ($this->relations[$relationKey]['columns'] ?? []);

        return array_filter($columns, function ($column) use ($searchQuery) {
            $label = strtolower($column['label'] ?? '');
            return str_contains($label, $searchQuery);
        });
    }

    /**
     * Toggle HasMany section expansion
     */
    public function toggleHasManySection(): void
    {
        $this->hasManyExpanded = !$this->hasManyExpanded;
    }

    /**
     * Get count of selected columns
     */
    public function getSelectedCount(): int
    {
        $count = 0;

        // Count main columns
        foreach ($this->selectedColumns['main'] as $value) {
            if (is_bool($value) && $value === true) {
                $count++;
            } elseif (is_array($value) && ($value['enabled'] ?? false)) {
                $count++;
            }
        }

        // Count relation columns
        foreach ($this->selectedColumns['relations'] as $columns) {
            foreach ($columns as $value) {
                if (is_bool($value) && $value === true) {
                    $count++;
                } elseif (is_array($value) && ($value['enabled'] ?? false)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('filament-smart-export::components.enhanced-column-selector');
    }
}
