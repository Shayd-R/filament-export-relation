<?php

namespace ShaydR\FilamentSmartExport\Actions\Concerns;

trait CanDownloadDirect
{
    protected bool $shouldDownloadDirect = false;

    public function downloadDirect(bool $condition = true): static
    {
        $this->shouldDownloadDirect = $condition;

        return $this;
    }

    public function shouldDownloadDirect(): bool
    {
        return $this->shouldDownloadDirect;
    }
}
