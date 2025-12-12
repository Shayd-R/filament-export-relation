<?php

namespace ShaydR\FilamentSmartExport\Concerns;

trait CanShowHiddenColumns
{
    protected bool $showHiddenColumns = false;

    public function showHiddenColumns(bool $condition = true): static
    {
        $this->showHiddenColumns = $condition;

        return $this;
    }

    public function shouldShowHiddenColumns(): bool
    {
        return $this->showHiddenColumns;
    }
}
