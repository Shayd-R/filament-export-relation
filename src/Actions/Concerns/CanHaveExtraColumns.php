<?php

namespace Shayd\FilamentSmartExport\Actions\Concerns;

use Closure;

trait CanHaveExtraColumns
{
    protected array | Closure $extraColumns = [];

    public function extraColumns(array | Closure $columns): static
    {
        $this->extraColumns = $columns;

        return $this;
    }

    public function getExtraColumns(): array
    {
        return $this->evaluate($this->extraColumns);
    }
}
