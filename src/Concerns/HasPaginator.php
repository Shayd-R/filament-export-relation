<?php

namespace Shayd\FilamentSmartExport\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;

trait HasPaginator
{
    protected ?LengthAwarePaginator $paginator = null;

    public function paginator(LengthAwarePaginator $paginator): static
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function getPaginator(): ?LengthAwarePaginator
    {
        return $this->paginator;
    }
}
