<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasTopbar
{
    protected bool | Closure $hasTopbar = true;

    public function topbar(bool | Closure $condition = true): static
    {
        $this->hasTopbar = $condition;

        return $this;
    }

    public function hasTopbar(): bool
    {
        return (bool) $this->evaluate($this->hasTopbar);
    }
}
