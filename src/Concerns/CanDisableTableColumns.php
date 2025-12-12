<?php

namespace ShaydR\FilamentSmartExport\Concerns;

trait CanDisableTableColumns
{
    protected bool $disableTableColumns = false;

    public function disableTableColumns(bool $condition = true): static
    {
        $this->disableTableColumns = $condition;

        return $this;
    }

    public function shouldDisableTableColumns(): bool
    {
        return $this->disableTableColumns;
    }
}
