<?php

namespace ShaydR\FilamentSmartExport\Concerns;

trait HasFormat
{
    protected ?string $format = null;

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }
}
