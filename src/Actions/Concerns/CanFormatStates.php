<?php

namespace ShaydR\FilamentSmartExport\Actions\Concerns;

trait CanFormatStates
{
    protected bool $shouldFormatStates = true;

    public function formatStates(bool $condition = true): static
    {
        $this->shouldFormatStates = $condition;

        return $this;
    }

    public function shouldFormatStates(): bool
    {
        return $this->shouldFormatStates;
    }
}
