<?php

namespace ShaydR\FilamentSmartExport\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ShaydR\FilamentSmartExport\Actions\SmartExportBulkAction;
use ShaydR\FilamentSmartExport\Tests\Models\Post;
use ShaydR\FilamentSmartExport\Tests\Models\User;
use ShaydR\FilamentSmartExport\Tests\Models\Category;

/**
 * Tests para el nuevo diseño mejorado del selector de columnas
 */
class EnhancedColumnSelectorTest extends TestCase
{
    protected SmartExportBulkAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        
        $this->action = SmartExportBulkAction::make();
    }

    /** @test */
    public function it_can_detect_belongs_to_relations_in_main_model()
    {
        // Arrange: Crear una instancia del modelo Post que tiene BelongsTo (user_id)
        $post = new Post();
        
        // Usar reflection para acceder al método protegido
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getBelongsToRelations');
        $method->setAccessible(true);
        
        // Act: Obtener las relaciones BelongsTo
        $belongsToRelations = $method->invoke($this->action, $post);
        
        // Assert: Verificar que se detectó la relación 'user'
        $this->assertArrayHasKey('user', $belongsToRelations);
        $this->assertEquals('BelongsTo', $belongsToRelations['user']['type']);
        $this->assertEquals(User::class, $belongsToRelations['user']['model']);
    }

    /** @test */
    public function it_can_generate_enhanced_column_structure()
    {
        // Arrange: Configurar el modelo en la acción
        $reflection = new \ReflectionClass($this->action);
        
        $modelClassProperty = $reflection->getProperty('modelClass');
        $modelClassProperty->setAccessible(true);
        $modelClassProperty->setValue($this->action, Post::class);
        
        // Descubrir estructura
        $discoverMethod = $reflection->getMethod('discoverModelStructure');
        $discoverMethod->setAccessible(true);
        $discoverMethod->invoke($this->action);
        
        // Act: Generar estructura mejorada
        $method = $reflection->getMethod('generateEnhancedColumnStructure');
        $method->setAccessible(true);
        $structure = $method->invoke($this->action);
        
        // Assert: Verificar estructura básica
        $this->assertArrayHasKey('model_name', $structure);
        $this->assertArrayHasKey('columns', $structure);
        $this->assertArrayHasKey('relations', $structure);
        $this->assertEquals('Post', $structure['model_name']);
    }

    /** @test */
    public function it_identifies_belongs_to_fields_correctly()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->action);
        
        $modelClassProperty = $reflection->getProperty('modelClass');
        $modelClassProperty->setAccessible(true);
        $modelClassProperty->setValue($this->action, Post::class);
        
        $discoverMethod = $reflection->getMethod('discoverModelStructure');
        $discoverMethod->setAccessible(true);
        $discoverMethod->invoke($this->action);
        
        // Act
        $method = $reflection->getMethod('generateEnhancedColumnStructure');
        $method->setAccessible(true);
        $structure = $method->invoke($this->action);
        
        // Assert: El campo 'user_id' debe ser de tipo 'belongs_to'
        $this->assertArrayHasKey('user_id', $structure['columns']);
        $this->assertEquals('belongs_to', $structure['columns']['user_id']['type']);
        $this->assertArrayHasKey('options', $structure['columns']['user_id']);
        $this->assertNotEmpty($structure['columns']['user_id']['options']);
    }

    /** @test */
    public function it_identifies_simple_fields_correctly()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->action);
        
        $modelClassProperty = $reflection->getProperty('modelClass');
        $modelClassProperty->setAccessible(true);
        $modelClassProperty->setValue($this->action, Post::class);
        
        $discoverMethod = $reflection->getMethod('discoverModelStructure');
        $discoverMethod->setAccessible(true);
        $discoverMethod->invoke($this->action);
        
        // Act
        $method = $reflection->getMethod('generateEnhancedColumnStructure');
        $method->setAccessible(true);
        $structure = $method->invoke($this->action);
        
        // Assert: Los campos 'id', 'title', 'content' deben ser simples
        $this->assertEquals('simple', $structure['columns']['id']['type']);
        $this->assertEquals('simple', $structure['columns']['title']['type']);
        $this->assertEquals('simple', $structure['columns']['content']['type']);
    }

    /** @test */
    public function it_can_discover_nested_relations_in_hasmany()
    {
        // Arrange: User tiene HasMany posts, y Post tiene BelongsTo user
        $reflection = new \ReflectionClass($this->action);
        
        $modelClassProperty = $reflection->getProperty('modelClass');
        $modelClassProperty->setAccessible(true);
        $modelClassProperty->setValue($this->action, User::class);
        
        $discoverMethod = $reflection->getMethod('discoverModelStructure');
        $discoverMethod->setAccessible(true);
        $discoverMethod->invoke($this->action);
        
        // Act
        $method = $reflection->getMethod('generateEnhancedColumnStructure');
        $method->setAccessible(true);
        $structure = $method->invoke($this->action);
        
        // Assert: Verificar que tiene relaciones HasMany
        $this->assertNotEmpty($structure['relations']);
        
        // Buscar la relación 'posts' si existe
        if (isset($structure['relations']['posts'])) {
            $this->assertArrayHasKey('columns', $structure['relations']['posts']);
            
            // Verificar que los BelongsTo dentro del HasMany están marcados correctamente
            if (isset($structure['relations']['posts']['columns']['user_id'])) {
                $this->assertEquals('belongs_to', $structure['relations']['posts']['columns']['user_id']['type']);
            }
        }
    }

    /** @test */
    public function it_includes_emoji_in_column_structure()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->action);
        
        $modelClassProperty = $reflection->getProperty('modelClass');
        $modelClassProperty->setAccessible(true);
        $modelClassProperty->setValue($this->action, Post::class);
        
        $discoverMethod = $reflection->getMethod('discoverModelStructure');
        $discoverMethod->setAccessible(true);
        $discoverMethod->invoke($this->action);
        
        // Act
        $method = $reflection->getMethod('generateEnhancedColumnStructure');
        $method->setAccessible(true);
        $structure = $method->invoke($this->action);
        
        // Assert: Todos los campos deben tener emoji
        foreach ($structure['columns'] as $column) {
            $this->assertArrayHasKey('emoji', $column);
            $this->assertNotEmpty($column['emoji']);
        }
    }

    /** @test */
    public function it_includes_label_in_column_structure()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->action);
        
        $modelClassProperty = $reflection->getProperty('modelClass');
        $modelClassProperty->setAccessible(true);
        $modelClassProperty->setValue($this->action, Post::class);
        
        $discoverMethod = $reflection->getMethod('discoverModelStructure');
        $discoverMethod->setAccessible(true);
        $discoverMethod->invoke($this->action);
        
        // Act
        $method = $reflection->getMethod('generateEnhancedColumnStructure');
        $method->setAccessible(true);
        $structure = $method->invoke($this->action);
        
        // Assert: Todos los campos deben tener label legible
        foreach ($structure['columns'] as $columnKey => $column) {
            $this->assertArrayHasKey('label', $column);
            $this->assertNotEmpty($column['label']);
            
            // El label debe ser más legible que la key
            $this->assertNotEquals($columnKey, $column['label']);
        }
    }

    /** @test */
    public function belongs_to_fields_have_options_array()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->action);
        
        $modelClassProperty = $reflection->getProperty('modelClass');
        $modelClassProperty->setAccessible(true);
        $modelClassProperty->setValue($this->action, Post::class);
        
        $discoverMethod = $reflection->getMethod('discoverModelStructure');
        $discoverMethod->setAccessible(true);
        $discoverMethod->invoke($this->action);
        
        // Act
        $method = $reflection->getMethod('generateEnhancedColumnStructure');
        $method->setAccessible(true);
        $structure = $method->invoke($this->action);
        
        // Assert: Los campos BelongsTo deben tener opciones
        foreach ($structure['columns'] as $column) {
            if ($column['type'] === 'belongs_to') {
                $this->assertArrayHasKey('options', $column);
                $this->assertIsArray($column['options']);
                $this->assertNotEmpty($column['options']);
                
                // Las opciones deben tener formato key => label
                foreach ($column['options'] as $key => $label) {
                    $this->assertIsString($key);
                    $this->assertIsString($label);
                }
            }
        }
    }

    /** @test */
    public function it_includes_relation_name_in_belongs_to_fields()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->action);
        
        $modelClassProperty = $reflection->getProperty('modelClass');
        $modelClassProperty->setAccessible(true);
        $modelClassProperty->setValue($this->action, Post::class);
        
        $discoverMethod = $reflection->getMethod('discoverModelStructure');
        $discoverMethod->setAccessible(true);
        $discoverMethod->invoke($this->action);
        
        // Act
        $method = $reflection->getMethod('generateEnhancedColumnStructure');
        $method->setAccessible(true);
        $structure = $method->invoke($this->action);
        
        // Assert: Los campos BelongsTo deben incluir el nombre de la relación
        foreach ($structure['columns'] as $column) {
            if ($column['type'] === 'belongs_to') {
                $this->assertArrayHasKey('relation', $column);
                $this->assertNotEmpty($column['relation']);
            }
        }
    }

    /** @test */
    public function hasmany_relations_have_correct_structure()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->action);
        
        $modelClassProperty = $reflection->getProperty('modelClass');
        $modelClassProperty->setAccessible(true);
        $modelClassProperty->setValue($this->action, User::class);
        
        $discoverMethod = $reflection->getMethod('discoverModelStructure');
        $discoverMethod->setAccessible(true);
        $discoverMethod->invoke($this->action);
        
        // Act
        $method = $reflection->getMethod('generateEnhancedColumnStructure');
        $method->setAccessible(true);
        $structure = $method->invoke($this->action);
        
        // Assert: Cada relación debe tener estructura completa
        foreach ($structure['relations'] as $relationKey => $relation) {
            $this->assertArrayHasKey('name', $relation);
            $this->assertArrayHasKey('key', $relation);
            $this->assertArrayHasKey('columns', $relation);
            
            $this->assertEquals($relationKey, $relation['key']);
            $this->assertIsArray($relation['columns']);
        }
    }

    /** @test */
    public function it_does_not_include_parent_relations_in_main_relations_list()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->action);
        
        $modelClassProperty = $reflection->getProperty('modelClass');
        $modelClassProperty->setAccessible(true);
        $modelClassProperty->setValue($this->action, User::class);
        
        $discoverMethod = $reflection->getMethod('discoverModelStructure');
        $discoverMethod->setAccessible(true);
        $discoverMethod->invoke($this->action);
        
        // Act
        $method = $reflection->getMethod('generateEnhancedColumnStructure');
        $method->setAccessible(true);
        $structure = $method->invoke($this->action);
        
        // Assert: Las relaciones con 'parent' no deben estar en la lista principal
        foreach ($structure['relations'] as $relation) {
            $this->assertArrayNotHasKey('parent', $relation);
        }
    }

    /** @test */
    public function field_emoji_helper_returns_correct_icons()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getFieldEmoji');
        $method->setAccessible(true);
        
        // Act & Assert: Verificar emojis para diferentes tipos de campos
        $this->assertEquals('🔑', $method->invoke($this->action, 'id'));
        $this->assertEquals('🔑', $method->invoke($this->action, 'user_id'));
        $this->assertEquals('📝', $method->invoke($this->action, 'name'));
        $this->assertEquals('📝', $method->invoke($this->action, 'title'));
        $this->assertEquals('📧', $method->invoke($this->action, 'email'));
        $this->assertEquals('📞', $method->invoke($this->action, 'phone'));
        $this->assertContains($method->invoke($this->action, 'created_at'), ['🕐', '📅']); // Puede ser fecha o tiempo
        $this->assertContains($method->invoke($this->action, 'updated_at'), ['🕐', '📅']); // Puede ser fecha o tiempo
        $this->assertEquals('👤', $method->invoke($this->action, 'user'));
    }

    /** @test */
    public function readable_column_name_converts_snake_case_to_title()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getReadableColumnName');
        $method->setAccessible(true);
        
        // Act & Assert
        $this->assertEquals('User Id', $method->invoke($this->action, 'user_id'));
        $this->assertEquals('Created At', $method->invoke($this->action, 'created_at'));
        $this->assertEquals('Full Name', $method->invoke($this->action, 'full_name'));
        $this->assertEquals('Id', $method->invoke($this->action, 'id'));
    }
}
