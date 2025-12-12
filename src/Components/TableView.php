<?php

namespace Shayd\FilamentSmartExport\Components;

use Filament\Support\Components\ViewComponent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;

class TableView extends ViewComponent
{
    protected ?LengthAwarePaginator $paginator = null;
    protected array $columns = [];
    protected array $extraViewData = [];

    public function paginator(LengthAwarePaginator $paginator): static
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function extraViewData(array $data): static
    {
        $this->extraViewData = $data;

        return $this;
    }

    public function render(): View
    {
        return view('filament-smart-export::components.table_view', [
            'paginator' => $this->paginator,
            'columns' => $this->columns,
            ...$this->extraViewData,
        ]);
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
