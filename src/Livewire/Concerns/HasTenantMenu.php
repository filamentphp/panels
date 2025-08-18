<?php

namespace Filament\Livewire\Concerns;

use Filament\Actions\Action;
use Filament\Facades\Filament;

trait HasTenantMenu
{
    /**
     * @var ?array<Action>
     */
    protected ?array $tenantMenuItems;

    /**
     * @return array<Action>
     */
    protected function getTenantMenuItems(): array
    {
        if (isset($this->tenantMenuItems)) {
            return $this->tenantMenuItems;
        }

        $this->tenantMenuItems = Filament::getTenantMenuItems();

        foreach ($this->tenantMenuItems as $action) {
            $action->defaultView($action::GROUPED_VIEW);

            $this->cacheAction($action);
        }

        return $this->tenantMenuItems;
    }
}
