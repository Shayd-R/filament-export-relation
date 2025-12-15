{{-- Enhanced Column Selector Component --}}
@props([
    'modelName' => 'Model',
    'columns' => [],
    'relations' => [],
    'selectedColumns' => [],
    'wire' => null,
])

<div class="space-y-6">
    {{-- Two Column Layout for Desktop --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Left Column: Main Model --}}
        <div class="space-y-4">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                {{-- Card Header --}}
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $modelName }}
                            </h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                {{ __('filament-smart-export::smart-export.main_model') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="px-4 py-4 space-y-3">
                    {{-- Search Field --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                            placeholder="{{ __('filament-smart-export::smart-export.search_fields') }}"
                            x-data
                            @input="$dispatch('search-main-columns', { query: $el.value })"
                        >
                    </div>

                    {{-- Select All Link --}}
                    <div class="flex justify-end">
                        <button 
                            type="button"
                            class="text-xs font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
                            @click="$dispatch('select-all-main')"
                        >
                            {{ __('filament-smart-export::smart-export.select_all') }}
                        </button>
                    </div>

                    {{-- Columns Grid --}}
                    <div class="grid grid-cols-2 gap-3" x-data="{ searchQuery: '' }" @search-main-columns.window="searchQuery = $event.detail.query">
                        @foreach($columns as $columnKey => $columnData)
                            <div 
                                class="space-y-2"
                                x-show="searchQuery === '' || '{{ strtolower($columnData['label']) }}'.includes(searchQuery.toLowerCase())"
                                x-transition
                            >
                                @if($columnData['type'] === 'simple')
                                    {{-- Simple Field --}}
                                    <label class="flex items-center space-x-2 cursor-pointer group">
                                        <input 
                                            type="checkbox" 
                                            wire:model.live="selectedColumns.main.{{ $columnKey }}"
                                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900"
                                        >
                                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                            <span class="mr-1">{{ $columnData['emoji'] }}</span>
                                            {{ $columnData['label'] }}
                                        </span>
                                    </label>
                                @else
                                    {{-- BelongsTo Field with Select --}}
                                    <div class="space-y-1">
                                        <label class="flex items-center space-x-2 cursor-pointer group">
                                            <input 
                                                type="checkbox" 
                                                wire:model.live="selectedColumns.main.{{ $columnKey }}.enabled"
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900"
                                            >
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                                <span class="mr-1">{{ $columnData['emoji'] }}</span>
                                                {{ $columnData['label'] }}:
                                            </span>
                                        </label>
                                        <select 
                                            wire:model.live="selectedColumns.main.{{ $columnKey }}.field"
                                            class="block w-full mt-1 rounded-md border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-primary-500 focus:border-primary-500"
                                        >
                                            @foreach($columnData['options'] as $optionKey => $optionLabel)
                                                <option value="{{ $optionKey }}">{{ $optionLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: HasMany Relations --}}
        <div class="space-y-4">
            <div x-data="{ expanded: false }">
                {{-- Collapsible Header --}}
                <button 
                    type="button"
                    @click="expanded = !expanded"
                    class="w-full flex items-center justify-between px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors"
                >
                    <div class="flex items-center space-x-2">
                        <svg 
                            class="h-5 w-5 text-amber-600 dark:text-amber-400 transition-transform"
                            :class="{ 'rotate-90': expanded }"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('filament-smart-export::smart-export.show_multiple_relationships') }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                            HasMany
                        </span>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        ({{ count($relations) }} {{ __('filament-smart-export::smart-export.relations') }})
                    </span>
                </button>

                {{-- Collapsible Content --}}
                <div 
                    x-show="expanded"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="mt-4 space-y-4"
                >
                    @foreach($relations as $relationKey => $relationData)
                        <div class="rounded-lg border border-amber-200 dark:border-amber-800 bg-white dark:bg-gray-800 shadow-sm">
                            {{-- Relation Card Header --}}
                            <div class="px-4 py-3 border-b border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 rounded-t-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $relationData['name'] }}
                                        </h4>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                            {{ __('filament-smart-export::smart-export.multiple_relation') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Relation Card Body --}}
                            <div class="px-4 py-4 space-y-3">
                                {{-- Search Field --}}
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input 
                                        type="text" 
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm"
                                        placeholder="{{ __('filament-smart-export::smart-export.search_fields') }}"
                                        x-data
                                        @input="$dispatch('search-relation-{{ $relationKey }}', { query: $el.value })"
                                    >
                                </div>

                                {{-- Select All Link --}}
                                <div class="flex justify-end">
                                    <button 
                                        type="button"
                                        class="text-xs font-medium text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300"
                                        @click="$dispatch('select-all-{{ $relationKey }}')"
                                    >
                                        {{ __('filament-smart-export::smart-export.select_all') }}
                                    </button>
                                </div>

                                {{-- Relation Columns Grid --}}
                                <div class="grid grid-cols-2 gap-3" x-data="{ searchQuery: '' }" @search-relation-{{ $relationKey }}.window="searchQuery = $event.detail.query">
                                    @foreach($relationData['columns'] as $colKey => $colData)
                                        <div 
                                            class="space-y-2"
                                            x-show="searchQuery === '' || '{{ strtolower($colData['label']) }}'.includes(searchQuery.toLowerCase())"
                                            x-transition
                                        >
                                            @if($colData['type'] === 'simple')
                                                {{-- Simple Field --}}
                                                <label class="flex items-center space-x-2 cursor-pointer group">
                                                    <input 
                                                        type="checkbox" 
                                                        wire:model.live="selectedColumns.relations.{{ $relationKey }}.{{ $colKey }}"
                                                        class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-900"
                                                    >
                                                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                                        <span class="mr-1">{{ $colData['emoji'] }}</span>
                                                        {{ $colData['label'] }}
                                                    </span>
                                                </label>
                                            @else
                                                {{-- BelongsTo Field within HasMany with Purple Background --}}
                                                <div class="space-y-1 p-2 rounded-md bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800">
                                                    <label class="flex items-center space-x-2 cursor-pointer group">
                                                        <input 
                                                            type="checkbox" 
                                                            wire:model.live="selectedColumns.relations.{{ $relationKey }}.{{ $colKey }}.enabled"
                                                            class="rounded border-purple-300 text-purple-600 focus:ring-purple-500 dark:border-purple-600 dark:bg-gray-900"
                                                        >
                                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                                            <span class="mr-1">{{ $colData['emoji'] }}</span>
                                                            {{ $colData['label'] }}:
                                                        </span>
                                                    </label>
                                                    <select 
                                                        wire:model.live="selectedColumns.relations.{{ $relationKey }}.{{ $colKey }}.field"
                                                        class="block w-full mt-1 rounded-md border-purple-300 dark:border-purple-600 bg-purple-100 dark:bg-purple-900/30 text-gray-900 dark:text-gray-100 text-sm focus:ring-purple-500 focus:border-purple-500"
                                                    >
                                                        @foreach($colData['options'] as $optKey => $optLabel)
                                                            <option value="{{ $optKey }}">{{ $optLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Select All functionality
    document.addEventListener('alpine:init', () => {
        // Main model select all
        window.addEventListener('select-all-main', () => {
            @this.selectAllMain();
        });

        // Relations select all
        @foreach($relations as $relationKey => $relationData)
            window.addEventListener('select-all-{{ $relationKey }}', () => {
                @this.selectAllRelation('{{ $relationKey }}');
            });
        @endforeach
    });
</script>
@endpush
