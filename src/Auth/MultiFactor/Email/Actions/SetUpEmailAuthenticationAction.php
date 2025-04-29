<?php

namespace Filament\Auth\MultiFactor\Email\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class SetUpEmailAuthenticationAction
{
    public static function make(EmailAuthentication $emailAuthentication): Action
    {
        return Action::make('setUpEmailAuthentication')
            ->label(__('filament-panels::auth/multi-factor/email/actions/set-up.label'))
            ->color('primary')
            ->icon(Heroicon::LockClosed)
            ->link()
            ->mountUsing(function (HasActions $livewire) use ($emailAuthentication): void {
                $livewire->mergeMountedActionArguments([
                    'encrypted' => encrypt([
                        'secret' => $secret = $emailAuthentication->generateSecret(),
                        'userId' => Filament::auth()->id(),
                    ]),
                ]);

                /** @var HasEmailAuthentication $user */
                $user = Filament::auth()->user();

                $emailAuthentication->sendCode($user, $secret);
            })
            ->modalWidth(Width::Large)
            ->modalIcon(Heroicon::OutlinedLockClosed)
            ->modalIconColor('primary')
            ->modalHeading(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.heading'))
            ->modalDescription(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.description'))
            ->schema(fn (array $arguments): array => [
                OneTimeCodeInput::make('code')
                    ->label(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.form.code.label'))
                    ->belowContent(Action::make('resend')
                        ->label(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.form.code.actions.resend.label'))
                        ->link()
                        ->action(function () use ($arguments, $emailAuthentication): void {
                            /** @var HasEmailAuthentication $user */
                            $user = Filament::auth()->user();

                            $emailAuthentication->sendCode($user, decrypt($arguments['encrypted'])['secret']);

                            Notification::make()
                                ->title(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.form.code.actions.resend.notifications.resent.title'))
                                ->success()
                                ->send();
                        }))
                    ->validationAttribute(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.form.code.validation_attribute'))
                    ->required()
                    ->rule(function () use ($arguments, $emailAuthentication): Closure {
                        return function (string $attribute, $value, Closure $fail) use ($arguments, $emailAuthentication): void {
                            if ($emailAuthentication->verifyCode($value, decrypt($arguments['encrypted'])['secret'])) {
                                return;
                            }

                            $fail(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.form.code.messages.invalid'));
                        };
                    }),
            ])
            ->modalSubmitAction(fn (Action $action) => $action
                ->label(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.actions.submit.label')))
            ->action(function (array $arguments) use ($emailAuthentication): void {
                /** @var Authenticatable&HasEmailAuthentication $user */
                $user = Filament::auth()->user();

                $encrypted = decrypt($arguments['encrypted']);

                if ($user->getAuthIdentifier() !== $encrypted['userId']) {
                    // Avoid encrypted arguments being passed between users by verifying that the authenticated
                    // user is the same as the user that the encrypted arguments were issued for.
                    return;
                }

                DB::transaction(function () use ($emailAuthentication, $encrypted, $user): void {
                    $emailAuthentication->saveSecret($user, $encrypted['secret']);
                });

                Notification::make()
                    ->title(__('filament-panels::auth/multi-factor/email/actions/set-up.notifications.enabled.title'))
                    ->success()
                    ->icon(Heroicon::OutlinedLockClosed)
                    ->send();
            })
            ->rateLimit(5);
    }
}
