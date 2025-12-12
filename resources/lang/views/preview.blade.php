@php
    function getColumnLabel($column) {
        return match($column) {
            // ObservationRecord fields
            'id' => 'ID Registro',
            'shift_id' => 'Shift ID',
            'area_id' => 'Area ID',
            'user_id' => 'User ID',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
            // Shift relation
            'shift.name' => 'Turno - Nombre',
            'shift.nameCsv' => 'Turno - CSV',
            'shift.nameInput' => 'Turno - Input',
            // Area relation
            'area.name' => 'Área - Nombre',
            // User relation
            'user.name' => 'Usuario - Nombre',
            'user.email' => 'Usuario - Email',
            // Observations relation
            'observations.id' => 'Observación - ID',
            'observations.profile_id' => 'Observación - Profile ID',
            'observations.instruction_id' => 'Observación - Instruction ID',
            'observations.action_id' => 'Observación - Action ID',
            'observations.gloves' => 'Observación - Guantes',
            'observations.profile.name' => 'Observación - Perfil',
            'observations.instruction.name' => 'Observación - Instrucción',
            'observations.action.name' => 'Observación - Acción',
            default => $column,
        };
    }

    function getColumnValue($column, $record, $observation = null) {
        return match($column) {
            // ObservationRecord fields
            'id' => $record->id,
            'shift_id' => $record->shift_id,
            'area_id' => $record->area_id,
            'user_id' => $record->user_id,
            'created_at' => $record->created_at?->format('Y-m-d H:i:s') ?? '',
            'updated_at' => $record->updated_at?->format('Y-m-d H:i:s') ?? '',
            // Shift relation
            'shift.name' => $record->shift?->name ?? '-',
            'shift.nameCsv' => $record->shift?->nameCsv ?? '-',
            'shift.nameInput' => $record->shift?->nameInput ?? '-',
            // Area relation
            'area.name' => $record->area?->name ?? '-',
            // User relation
            'user.name' => $record->user?->name ?? '-',
            'user.email' => $record->user?->email ?? '-',
            // Observations relation
            'observations.id' => $observation?->id ?? '-',
            'observations.profile_id' => $observation?->profile_id ?? '-',
            'observations.instruction_id' => $observation?->instruction_id ?? '-',
            'observations.action_id' => $observation?->action_id ?? '-',
            'observations.gloves' => $observation ? ($observation->gloves ? 'Sí' : 'No') : '-',
            'observations.profile.name' => $observation?->profile?->name ?? '-',
            'observations.instruction.name' => $observation?->instruction?->name ?? '-',
            'observations.action.name' => $observation?->action?->name ?? '-',
            default => '-',
        };
    }

    // Contar total de filas (considerando que cada observación genera una fila)
    $totalRows = 0;
    foreach($records as $record) {
        $totalRows += max(1, $record->observations->count());
    }
@endphp

<div class="space-y-3">
    @if($records->count() > 0)
        <div class="flex items-center justify-between text-sm">
            <span class="font-semibold text-gray-700 dark:text-gray-300">
                Total de filas: {{ number_format($totalRows) }}
                <span class="text-xs text-gray-500">({{ $records->count() }} registros)</span>
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ count($selectedColumns) }} columna(s) seleccionada(s)
            </span>
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm max-h-[500px] overflow-y-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
            <thead class="bg-gray-100 dark:bg-gray-800 sticky top-0 z-10">
                <tr>
                    @foreach($selectedColumns as $column)
                        <th scope="col" class="px-3 py-2.5 text-left font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                            {{ getColumnLabel($column) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($records as $record)
                    @if($record->observations->isNotEmpty())
                        @foreach($record->observations as $observation)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                @foreach($selectedColumns as $column)
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                        @if(str_contains($column, 'observations.gloves'))
                                            @php $value = getColumnValue($column, $record, $observation); @endphp
                                            @if($value !== '-')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $value === 'Sí' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                    {{ $value }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        @else
                                            <span class="text-xs">{{ getColumnValue($column, $record, $observation) }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @else
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            @foreach($selectedColumns as $column)
                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                    <span class="text-xs">{{ getColumnValue($column, $record, null) }}</span>
                                </td>
                            @endforeach
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="{{ count($selectedColumns) }}" class="text-center p-6 text-gray-500 dark:text-gray-400">
                            Sin datos para previsualizar con los filtros seleccionados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
