<?php

namespace ShaydR\FilamentSmartExport;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use OpenSpout\Writer\CSV\Writer as CSVWriter;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;
use OpenSpout\Common\Entity\Row;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Central class for Smart Export functionality
 */
class FilamentSmartExport
{
    /**
     * Generate export file based on format
     */
    public static function export(
        Collection $records,
        array $selectedColumns,
        string $format = 'xlsx',
        string $fileName = 'export',
        string $csvDelimiter = ','
    ): StreamedResponse {
        $fileName = self::sanitizeFileName($fileName, $format);

        return response()->streamDownload(function () use ($records, $selectedColumns, $format, $csvDelimiter) {
            $writer = $format === 'csv' 
                ? new CSVWriter()
                : new XLSXWriter();

            if ($format === 'csv' && method_exists($writer, 'setFieldDelimiter')) {
                $writer->setFieldDelimiter($csvDelimiter);
            }

            $writer->openToFile('php://output');

            // Write headers
            $headers = array_values($selectedColumns);
            $writer->addRow(Row::fromValues($headers));

            // Write data rows
            foreach ($records as $record) {
                $rowData = [];
                foreach (array_keys($selectedColumns) as $column) {
                    $rowData[] = self::getColumnValue($record, $column);
                }
                $writer->addRow(Row::fromValues($rowData));
            }

            $writer->close();
        }, $fileName, [
            'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Get column value from record
     */
    protected static function getColumnValue($record, string $column): string
    {
        // Handle nested relations (e.g., 'user.name')
        if (str_contains($column, '.')) {
            $parts = explode('.', $column);
            $relation = $parts[0];
            $attribute = $parts[1];

            if ($record->$relation) {
                return $record->$relation->$attribute ?? '';
            }

            return '';
        }

        return $record->$column ?? '';
    }

    /**
     * Sanitize file name
     */
    protected static function sanitizeFileName(string $fileName, string $format): string
    {
        $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileName);
        $fileName = trim($fileName, '_');
        
        return $fileName . '.' . $format;
    }

    /**
     * Discover model columns
     */
    public static function getModelColumns(string $modelClass): array
    {
        $model = new $modelClass;
        $tableName = $model->getTable();

        return Schema::getColumnListing($tableName);
    }

    /**
     * Generate friendly column name
     */
    public static function formatColumnName(string $column): string
    {
        return ucwords(str_replace('_', ' ', $column));
    }
}
