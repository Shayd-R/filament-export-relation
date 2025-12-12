<?php

namespace Shayd\FilamentSmartExport\Tests;

use Shayd\FilamentSmartExport\Actions\SmartExportBulkAction;
use Shayd\FilamentSmartExport\Actions\SmartExportHeaderAction;
use Shayd\FilamentSmartExport\Tests\TestCase;

class ExportTest extends TestCase
{
    /** @test */
    public function it_can_create_bulk_action()
    {
        $action = SmartExportBulkAction::make();

        $this->assertInstanceOf(SmartExportBulkAction::class, $action);
    }

    /** @test */
    public function it_can_create_header_action()
    {
        $action = SmartExportHeaderAction::make();

        $this->assertInstanceOf(SmartExportHeaderAction::class, $action);
    }

    /** @test */
    public function it_has_default_name()
    {
        $action = SmartExportBulkAction::make();

        $this->assertEquals('smart-export', $action::getDefaultName());
    }

    /** @test */
    public function it_can_set_default_format()
    {
        $action = SmartExportBulkAction::make()
            ->defaultFormat('csv');

        $this->assertEquals('csv', $action->getDefaultFormat());
    }

    /** @test */
    public function it_can_set_csv_delimiter()
    {
        $action = SmartExportBulkAction::make()
            ->csvDelimiter(';');

        $this->assertEquals(';', $action->getCsvDelimiter());
    }

    /** @test */
    public function it_can_enable_download_direct()
    {
        $action = SmartExportBulkAction::make()
            ->downloadDirect();

        $this->assertTrue($action->shouldDownloadDirect());
    }

    /** @test */
    public function it_can_set_file_name()
    {
        $action = SmartExportBulkAction::make()
            ->fileName('my-export');

        $this->assertEquals('my-export', $action->getFileName());
    }

    /** @test */
    public function it_can_disable_format_states()
    {
        $action = SmartExportBulkAction::make()
            ->formatStates(false);

        $this->assertFalse($action->shouldFormatStates());
    }
}
