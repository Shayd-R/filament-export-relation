<?php

namespace ShaydR\FilamentSmartExport\Tests;

use ShaydR\FilamentSmartExport\Actions\SmartExportBulkAction;

/**
 * Tests simplificados para las nuevas funcionalidades
 * Estos tests NO requieren base de datos ni migraciones
 */
class EnhancedFunctionalitySimpleTest extends TestCase
{
    protected SmartExportBulkAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = SmartExportBulkAction::make();
    }

    /** @test */
    public function it_can_get_field_emoji_for_id_fields()
    {
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getFieldEmoji');
        $method->setAccessible(true);
        
        $this->assertEquals('🔑', $method->invoke($this->action, 'id'));
        $this->assertEquals('🔑', $method->invoke($this->action, 'user_id'));
        $this->assertEquals('🔑', $method->invoke($this->action, 'category_id'));
    }

    /** @test */
    public function it_can_get_field_emoji_for_name_fields()
    {
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getFieldEmoji');
        $method->setAccessible(true);
        
        $this->assertEquals('📝', $method->invoke($this->action, 'name'));
        $this->assertEquals('📝', $method->invoke($this->action, 'title'));
        $this->assertEquals('📝', $method->invoke($this->action, 'nombre'));
    }

    /** @test */
    public function it_can_get_field_emoji_for_email_fields()
    {
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getFieldEmoji');
        $method->setAccessible(true);
        
        $this->assertEquals('📧', $method->invoke($this->action, 'email'));
        $this->assertEquals('📧', $method->invoke($this->action, 'correo'));
    }

    /** @test */
    public function it_can_get_field_emoji_for_phone_fields()
    {
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getFieldEmoji');
        $method->setAccessible(true);
        
        $this->assertEquals('📞', $method->invoke($this->action, 'phone'));
        $this->assertEquals('📞', $method->invoke($this->action, 'telefono'));
    }

    /** @test */
    public function it_can_get_field_emoji_for_timestamp_fields()
    {
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getFieldEmoji');
        $method->setAccessible(true);
        
        // Estos campos pueden retornar 🕐 o 📅 dependiendo de la lógica
        $createdEmoji = $method->invoke($this->action, 'created_at');
        $this->assertContains($createdEmoji, ['🕐', '📅']);
        
        $updatedEmoji = $method->invoke($this->action, 'updated_at');
        $this->assertContains($updatedEmoji, ['🕐', '📅']);
    }

    /** @test */
    public function it_can_get_field_emoji_for_user_fields()
    {
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getFieldEmoji');
        $method->setAccessible(true);
        
        $this->assertEquals('👤', $method->invoke($this->action, 'user'));
        $this->assertEquals('👤', $method->invoke($this->action, 'usuario'));
    }

    /** @test */
    public function it_can_get_field_emoji_for_money_fields()
    {
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getFieldEmoji');
        $method->setAccessible(true);
        
        $this->assertEquals('💰', $method->invoke($this->action, 'price'));
        $this->assertEquals('💰', $method->invoke($this->action, 'precio'));
        $this->assertEquals('💰', $method->invoke($this->action, 'cost'));
        $this->assertEquals('💰', $method->invoke($this->action, 'costo'));
    }

    /** @test */
    public function it_can_get_field_emoji_for_status_fields()
    {
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getFieldEmoji');
        $method->setAccessible(true);
        
        $this->assertEquals('🔄', $method->invoke($this->action, 'status'));
        $this->assertEquals('🔄', $method->invoke($this->action, 'estado'));
    }

    /** @test */
    public function it_returns_default_emoji_for_unknown_fields()
    {
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getFieldEmoji');
        $method->setAccessible(true);
        
        $this->assertEquals('📋', $method->invoke($this->action, 'random_field'));
        $this->assertEquals('📋', $method->invoke($this->action, 'unknown'));
    }

    /** @test */
    public function it_converts_snake_case_to_title_case()
    {
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getReadableColumnName');
        $method->setAccessible(true);
        
        $this->assertEquals('User Id', $method->invoke($this->action, 'user_id'));
        $this->assertEquals('Created At', $method->invoke($this->action, 'created_at'));
        $this->assertEquals('Full Name', $method->invoke($this->action, 'full_name'));
    }

    /** @test */
    public function it_capitalizes_single_word_columns()
    {
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getReadableColumnName');
        $method->setAccessible(true);
        
        $this->assertEquals('Name', $method->invoke($this->action, 'name'));
        $this->assertEquals('Email', $method->invoke($this->action, 'email'));
        $this->assertEquals('Id', $method->invoke($this->action, 'id'));
    }

    /** @test */
    public function it_handles_multiple_underscores()
    {
        $reflection = new \ReflectionClass($this->action);
        $method = $reflection->getMethod('getReadableColumnName');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->action, 'user_profile_image_url');
        $this->assertEquals('User Profile Image Url', $result);
    }

    /** @test */
    public function action_has_default_name()
    {
        $this->assertEquals('smart-export', SmartExportBulkAction::getDefaultName());
    }

    /** @test */
    public function action_can_be_instantiated()
    {
        $action = SmartExportBulkAction::make();
        $this->assertInstanceOf(SmartExportBulkAction::class, $action);
    }

    /** @test */
    public function action_can_set_csv_delimiter()
    {
        $action = SmartExportBulkAction::make()->csvDelimiter(';');
        $this->assertEquals(';', $action->getCsvDelimiter());
    }

    /** @test */
    public function action_can_set_default_format()
    {
        $action = SmartExportBulkAction::make()->defaultFormat('csv');
        $this->assertEquals('csv', $action->getDefaultFormat());
    }

    /** @test */
    public function action_can_enable_download_direct()
    {
        $action = SmartExportBulkAction::make()->downloadDirect();
        $this->assertTrue($action->shouldDownloadDirect());
    }

    /** @test */
    public function action_can_set_file_name()
    {
        $action = SmartExportBulkAction::make()->fileName('my-custom-export');
        $this->assertEquals('my-custom-export', $action->getFileName());
    }

    /** @test */
    public function action_can_disable_format_states()
    {
        $action = SmartExportBulkAction::make()->formatStates(false);
        $this->assertFalse($action->shouldFormatStates());
    }

    /** @test */
    public function action_has_format_states_enabled_by_default()
    {
        $action = SmartExportBulkAction::make();
        $this->assertTrue($action->shouldFormatStates());
    }
}
