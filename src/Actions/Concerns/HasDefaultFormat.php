<?php

namespace Shayd\FilamentSmartExport\Actions\Concerns;

trait HasDefaultFormat
{
    protected ?string $defaultFormat = 'xlsx';

    public function defaultFormat(string $format): static
    {
        $this->defaultFormat = $format;

        return $this;
    }

    public function getDefaultFormat(): ?string
    {
        return $this->defaultFormat;
    }
}
