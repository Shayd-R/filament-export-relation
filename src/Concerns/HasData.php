<?php

namespace ShaydR\FilamentSmartExport\Concerns;

use Illuminate\Database\Eloquent\Collection;

trait HasData
{
    protected ?Collection $data = null;

    public function data(Collection $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): ?Collection
    {
        return $this->data;
    }
}
