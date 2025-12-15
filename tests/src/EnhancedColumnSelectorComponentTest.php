<?php

namespace ShaydR\FilamentSmartExport\Tests;

use Livewire\Livewire;
use ShaydR\FilamentSmartExport\Components\EnhancedColumnSelector;

/**
 * Tests para el componente Livewire EnhancedColumnSelector
 */
class EnhancedColumnSelectorComponentTest extends TestCase
{
    /** @test */
    public function component_can_be_mounted()
    {
        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'TestModel',
            'columns' => [],
            'relations' => [],
        ]);

        $component->assertStatus(200);
    }

    /** @test */
    public function it_initializes_with_model_name()
    {
        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => [],
            'relations' => [],
        ]);

        $component->assertSet('modelName', 'Post');
    }

    /** @test */
    public function it_initializes_default_selections_for_simple_columns()
    {
        $columns = [
            'id' => ['type' => 'simple', 'label' => 'Id', 'emoji' => '🔑'],
            'title' => ['type' => 'simple', 'label' => 'Title', 'emoji' => '📝'],
            'content' => ['type' => 'simple', 'label' => 'Content', 'emoji' => '📝'],
        ];

        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => $columns,
            'relations' => [],
        ]);

        // Debe seleccionar automáticamente los primeros 5 campos
        $component->assertSet('selectedColumns.main.id', true);
        $component->assertSet('selectedColumns.main.title', true);
        $component->assertSet('selectedColumns.main.content', true);
    }

    /** @test */
    public function it_initializes_default_selections_for_belongs_to_columns()
    {
        $columns = [
            'id' => ['type' => 'simple', 'label' => 'Id', 'emoji' => '🔑'],
            'user_id' => [
                'type' => 'belongs_to',
                'label' => 'User',
                'emoji' => '👤',
                'relation' => 'user',
                'options' => ['id' => 'Id', 'name' => 'Name', 'email' => 'Email'],
            ],
        ];

        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => $columns,
            'relations' => [],
        ]);

        // BelongsTo debe tener estructura con enabled y field
        $this->assertTrue($component->get('selectedColumns.main.user_id')['enabled']);
        $this->assertEquals('id', $component->get('selectedColumns.main.user_id')['field']);
    }

    /** @test */
    public function select_all_main_selects_all_simple_columns()
    {
        $columns = [
            'id' => ['type' => 'simple', 'label' => 'Id', 'emoji' => '🔑'],
            'title' => ['type' => 'simple', 'label' => 'Title', 'emoji' => '📝'],
            'content' => ['type' => 'simple', 'label' => 'Content', 'emoji' => '📝'],
            'published' => ['type' => 'simple', 'label' => 'Published', 'emoji' => '🔄'],
        ];

        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => $columns,
            'relations' => [],
            'selectedColumns' => [], // Iniciar sin selección
        ]);

        // Ejecutar select all
        $component->call('selectAllMain');

        // Verificar que todos estén seleccionados
        $component->assertSet('selectedColumns.main.id', true);
        $component->assertSet('selectedColumns.main.title', true);
        $component->assertSet('selectedColumns.main.content', true);
        $component->assertSet('selectedColumns.main.published', true);
    }

    /** @test */
    public function select_all_main_enables_all_belongs_to_columns()
    {
        $columns = [
            'id' => ['type' => 'simple', 'label' => 'Id', 'emoji' => '🔑'],
            'user_id' => [
                'type' => 'belongs_to',
                'label' => 'User',
                'emoji' => '👤',
                'relation' => 'user',
                'options' => ['id' => 'Id', 'name' => 'Name'],
            ],
        ];

        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => $columns,
            'relations' => [],
            'selectedColumns' => [
                'main' => [
                    'user_id' => ['enabled' => false, 'field' => 'name']
                ]
            ],
        ]);

        $component->call('selectAllMain');

        // Verificar que el BelongsTo esté habilitado y mantenga el field seleccionado
        $this->assertTrue($component->get('selectedColumns.main.user_id')['enabled']);
        $this->assertEquals('name', $component->get('selectedColumns.main.user_id')['field']);
    }

    /** @test */
    public function deselect_all_main_deselects_all_columns()
    {
        $columns = [
            'id' => ['type' => 'simple', 'label' => 'Id', 'emoji' => '🔑'],
            'title' => ['type' => 'simple', 'label' => 'Title', 'emoji' => '📝'],
        ];

        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => $columns,
            'relations' => [],
        ]);

        // Primero seleccionar todos
        $component->call('selectAllMain');
        
        // Luego deseleccionar
        $component->call('deselectAllMain');

        $component->assertSet('selectedColumns.main.id', false);
        $component->assertSet('selectedColumns.main.title', false);
    }

    /** @test */
    public function select_all_relation_works_for_hasmany()
    {
        $relations = [
            'comments' => [
                'name' => 'Comment',
                'key' => 'comments',
                'columns' => [
                    'id' => ['type' => 'simple', 'label' => 'Id', 'emoji' => '🔑'],
                    'content' => ['type' => 'simple', 'label' => 'Content', 'emoji' => '📝'],
                ],
            ],
        ];

        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => [],
            'relations' => $relations,
            'selectedColumns' => ['main' => [], 'relations' => []],
        ]);

        $component->call('selectAllRelation', 'comments');

        $component->assertSet('selectedColumns.relations.comments.id', true);
        $component->assertSet('selectedColumns.relations.comments.content', true);
    }

    /** @test */
    public function select_all_relation_handles_belongs_to_in_hasmany()
    {
        $relations = [
            'observations' => [
                'name' => 'Observation',
                'key' => 'observations',
                'columns' => [
                    'id' => ['type' => 'simple', 'label' => 'Id', 'emoji' => '🔑'],
                    'profile_id' => [
                        'type' => 'belongs_to',
                        'label' => 'Profile',
                        'emoji' => '👤',
                        'relation' => 'profile',
                        'options' => ['id' => 'Id', 'name' => 'Name'],
                    ],
                ],
            ],
        ];

        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'ObservationRecord',
            'columns' => [],
            'relations' => $relations,
            'selectedColumns' => ['main' => [], 'relations' => []],
        ]);

        $component->call('selectAllRelation', 'observations');

        $this->assertTrue($component->get('selectedColumns.relations.observations.profile_id')['enabled']);
        $this->assertEquals('id', $component->get('selectedColumns.relations.observations.profile_id')['field']);
    }

    /** @test */
    public function get_selected_columns_for_export_returns_correct_format()
    {
        $columns = [
            'id' => ['type' => 'simple', 'label' => 'Id', 'emoji' => '🔑'],
            'title' => ['type' => 'simple', 'label' => 'Title', 'emoji' => '📝'],
        ];

        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => $columns,
            'relations' => [],
        ]);

        $exported = $component->call('getSelectedColumnsForExport')->getData();

        // Debe retornar array con formato key => label
        $this->assertIsArray($exported);
    }

    /** @test */
    public function get_selected_columns_includes_belongs_to_with_selected_field()
    {
        $columns = [
            'user_id' => [
                'type' => 'belongs_to',
                'label' => 'User',
                'emoji' => '👤',
                'relation' => 'user',
                'options' => ['id' => 'Id', 'name' => 'Name', 'email' => 'Email'],
            ],
        ];

        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => $columns,
            'relations' => [],
            'selectedColumns' => [
                'main' => [
                    'user_id' => ['enabled' => true, 'field' => 'name']
                ],
                'relations' => [],
            ],
        ]);

        $exported = $component->call('getSelectedColumnsForExport');

        // Debe incluir 'user.name' en vez de 'user_id'
        $this->assertArrayHasKey('user.name', $exported);
    }

    /** @test */
    public function toggle_hasmany_section_changes_state()
    {
        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => [],
            'relations' => [],
        ]);

        // Estado inicial
        $component->assertSet('hasManyExpanded', false);

        // Alternar
        $component->call('toggleHasManySection');
        $component->assertSet('hasManyExpanded', true);

        // Alternar de nuevo
        $component->call('toggleHasManySection');
        $component->assertSet('hasManyExpanded', false);
    }

    /** @test */
    public function get_selected_count_returns_correct_number()
    {
        $columns = [
            'id' => ['type' => 'simple', 'label' => 'Id', 'emoji' => '🔑'],
            'title' => ['type' => 'simple', 'label' => 'Title', 'emoji' => '📝'],
            'content' => ['type' => 'simple', 'label' => 'Content', 'emoji' => '📝'],
        ];

        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => $columns,
            'relations' => [],
        ]);

        // Por defecto selecciona los primeros 3
        $count = $component->call('getSelectedCount');
        $this->assertEquals(3, $count);
    }

    /** @test */
    public function it_can_change_belongs_to_field_selection()
    {
        $columns = [
            'user_id' => [
                'type' => 'belongs_to',
                'label' => 'User',
                'emoji' => '👤',
                'relation' => 'user',
                'options' => ['id' => 'Id', 'name' => 'Name', 'email' => 'Email'],
            ],
        ];

        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => $columns,
            'relations' => [],
        ]);

        // Cambiar el campo seleccionado
        $component->set('selectedColumns.main.user_id.field', 'email');

        // Verificar el cambio
        $this->assertEquals('email', $component->get('selectedColumns.main.user_id')['field']);
    }

    /** @test */
    public function component_renders_successfully()
    {
        $component = Livewire::test(EnhancedColumnSelector::class, [
            'modelName' => 'Post',
            'columns' => [
                'id' => ['type' => 'simple', 'label' => 'Id', 'emoji' => '🔑'],
            ],
            'relations' => [],
        ]);

        $component->assertViewIs('filament-smart-export::components.enhanced-column-selector');
    }
}
