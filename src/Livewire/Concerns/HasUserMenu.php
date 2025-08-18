<?php

namespace Filament\Livewire\Concerns;

use Filament\Actions\Action;
use Filament\Facades\Filament;

trait HasUserMenu
{
    /**
     * @var ?array<Action>
     */
    protected ?array $userMenuItems = null;

    /**
     * @return array<Action>
     */
    protected function getUserMenuItems(): array
    {
        if (isset($this->userMenuItems)) {
            return $this->userMenuItems;
        }

        $this->userMenuItems = Filament::getUserMenuItems();

        foreach ($this->userMenuItems as $action) {
            $action->defaultView($action::GROUPED_VIEW);

            $this->cacheAction($action);
        }

        return $this->userMenuItems;
    }
}
