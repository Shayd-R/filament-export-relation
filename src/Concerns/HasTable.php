<?php

namespace Shayd\FilamentSmartExport\Concerns;

use Filament\Tables\Table;

trait HasTable
{
    protected ?Table $table = null;

    public function table(Table $table): static
    {
        $this->table = $table;

        return $this;
    }

    public function getTable(): ?Table
    {
        return $this->table;
    }
}
