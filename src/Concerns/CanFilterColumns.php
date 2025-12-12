<?php

namespace Shayd\FilamentSmartExport\Concerns;

use Closure;

trait CanFilterColumns
{
    protected array | Closure | null $filterColumns = null;

    public function filterColumns(array | Closure $columns): static
    {
        $this->filterColumns = $columns;

        return $this;
    }

    public function getFilterColumns(): ?array
    {
        return $this->evaluate($this->filterColumns);
    }

    public function hasFilterColumns(): bool
    {
        return $this->filterColumns !== null;
    }
}
