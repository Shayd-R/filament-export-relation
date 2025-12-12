<div class="filament-smart-export-table-view">
    @if($paginator && $paginator->count() > 0)
        <div class="overflow-x-auto">
            <table class="smart-export-preview-table">
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($paginator->items() as $record)
                        <tr>
                            @foreach($columns as $column)
                                <td>
                                    @if(isset($column['formatStateUsing']) && is_callable($column['formatStateUsing']))
                                        {{ $column['formatStateUsing']($record->{$column['name']}) }}
                                    @else
                                        {{ data_get($record, $column['name']) ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($paginator->hasPages())
            <div class="mt-4">
                {{ $paginator->links() }}
            </div>
        @endif

        {{-- Summary --}}
        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
            Mostrando {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} de {{ $paginator->total() }} registros
        </div>
    @else
        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
            <div class="text-6xl mb-4">📭</div>
            <p>No hay datos para mostrar</p>
        </div>
    @endif
</div>

<style>
    .filament-smart-export-table-view {
        padding: 1rem;
    }

    .smart-export-preview-table {
        width: 100%;
        border-collapse: collapse;
    }

    .smart-export-preview-table thead {
        background-color: #f9fafb;
    }

    .dark .smart-export-preview-table thead {
        background-color: #374151;
    }

    .smart-export-preview-table th {
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.875rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .dark .smart-export-preview-table th {
        border-bottom-color: #4b5563;
        color: #f9fafb;
    }

    .smart-export-preview-table td {
        padding: 0.75rem;
        font-size: 0.875rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .dark .smart-export-preview-table td {
        border-bottom-color: #374151;
        color: #d1d5db;
    }

    .smart-export-preview-table tbody tr:hover {
        background-color: #f3f4f6;
    }

    .dark .smart-export-preview-table tbody tr:hover {
        background-color: #374151;
    }
</style>
