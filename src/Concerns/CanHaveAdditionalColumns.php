<?php

namespace ShaydR\FilamentSmartExport\Concerns;

use Closure;

trait CanHaveAdditionalColumns
{
    protected array | Closure $additionalColumns = [];

    public function additionalColumns(array | Closure $columns): static
    {
        $this->additionalColumns = $columns;

        return $this;
    }

    public function getAdditionalColumns(): array
    {
        return $this->evaluate($this->additionalColumns);
    }
}
