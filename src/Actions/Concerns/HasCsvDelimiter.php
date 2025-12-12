<?php

namespace Shayd\FilamentSmartExport\Actions\Concerns;

trait HasCsvDelimiter
{
    protected string $csvDelimiter = ',';

    public function csvDelimiter(string $delimiter): static
    {
        $this->csvDelimiter = $delimiter;

        return $this;
    }

    public function getCsvDelimiter(): string
    {
        return $this->csvDelimiter;
    }
}
