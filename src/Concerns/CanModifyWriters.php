<?php

namespace Shayd\FilamentSmartExport\Concerns;

use Closure;

trait CanModifyWriters
{
    protected ?Closure $modifyXlsxWriterUsing = null;
    protected ?Closure $modifyCsvWriterUsing = null;

    public function modifyXlsxWriterUsing(?Closure $callback): static
    {
        $this->modifyXlsxWriterUsing = $callback;

        return $this;
    }

    public function modifyCsvWriterUsing(?Closure $callback): static
    {
        $this->modifyCsvWriterUsing = $callback;

        return $this;
    }

    public function getXlsxWriterModifier(): ?Closure
    {
        return $this->modifyXlsxWriterUsing;
    }

    public function getCsvWriterModifier(): ?Closure
    {
        return $this->modifyCsvWriterUsing;
    }
}
