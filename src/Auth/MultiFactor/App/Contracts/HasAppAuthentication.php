<?php

namespace Filament\Auth\MultiFactor\App\Contracts;

interface HasAppAuthentication
{
    public function hasAppAuthentication(): bool;

    public function getAppAuthenticationSecret(): ?string;

    public function saveAppAuthenticationSecret(?string $secret): void;

    public function getAppAuthenticationHolderName(): string;
}
