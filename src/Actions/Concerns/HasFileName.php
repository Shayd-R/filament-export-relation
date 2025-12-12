<?php

namespace ShaydR\FilamentSmartExport\Actions\Concerns;

use Closure;

trait HasFileName
{
    protected string | Closure | null $fileName = null;
    protected string | Closure | null $fileNamePrefix = null;

    public function fileName(string | Closure $name): static
    {
        $this->fileName = $name;

        return $this;
    }

    public function fileNamePrefix(string | Closure $prefix): static
    {
        $this->fileNamePrefix = $prefix;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->evaluate($this->fileName);
    }

    public function getFileNamePrefix(): ?string
    {
        return $this->evaluate($this->fileNamePrefix);
    }
}
