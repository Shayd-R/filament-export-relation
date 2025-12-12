<?php

namespace ShaydR\FilamentSmartExport\Concerns;

use Closure;

trait CanHaveExtraViewData
{
    protected array | Closure $extraViewData = [];

    public function extraViewData(array | Closure $data): static
    {
        $this->extraViewData = $data;

        return $this;
    }

    public function getExtraViewData(): array
    {
        return $this->evaluate($this->extraViewData);
    }
}
