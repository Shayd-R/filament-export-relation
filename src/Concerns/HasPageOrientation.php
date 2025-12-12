<?php

namespace Shayd\FilamentSmartExport\Concerns;

trait HasPageOrientation
{
    protected string $pageOrientation = 'portrait';

    public function pageOrientation(string $orientation): static
    {
        $this->pageOrientation = $orientation;

        return $this;
    }

    public function getPageOrientation(): string
    {
        return $this->pageOrientation;
    }
}
