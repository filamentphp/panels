<?php

namespace Filament\Panel\Concerns;

use Filament\Support\Enums\MaxWidth;

trait HasMaxContentWidth
{
    protected MaxWidth | string | null $maxContentWidth = null;

    protected MaxWidth | string | null $simplePageMaxContentWidth = null;

    public function maxContentWidth(MaxWidth | string | null $maxContentWidth): static
    {
        $this->maxContentWidth = $maxContentWidth;

        return $this;
    }

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return $this->maxContentWidth;
    }

    public function simplePageMaxContentWidth(MaxWidth | string | null $width): static
    {
        $this->simplePageMaxContentWidth = $width;

        return $this;
    }

    public function getSimplePageMaxContentWidth(): MaxWidth | string | null
    {
        return $this->simplePageMaxContentWidth;
    }
}
