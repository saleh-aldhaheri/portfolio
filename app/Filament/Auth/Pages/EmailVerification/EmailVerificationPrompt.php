<?php

namespace App\Filament\Auth\Pages\EmailVerification;

use App\Models\User;
use App\Services\EmailVerificationCodeService;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property-read Action $resendNotificationAction
 * @property-read Schema $form
 */
class EmailVerificationPrompt extends SimplePage
{
    use RestrictsFileUploadsToSchemaComponents;
    use WithRateLimiting;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        if (! Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());

            return;
        }

        /** @var User $user */
        $user = Filament::auth()->user();

        if ($user->hasVerifiedEmail()) {
            redirect()->intended(Filament::getUrl());

            return;
        }

        $this->form->fill();
    }

    public function verify(): void
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        $data = $this->form->getState();
        $code = (string) ($data['code'] ?? '');

        $service = app(EmailVerificationCodeService::class);

        if ($service->isLockedOut($user)) {
            Notification::make()
                ->title(__('Too many failed attempts. Please request a new code.'))
                ->danger()
                ->send();

            return;
        }

        if (! $service->verify($user, $code)) {
            Notification::make()
                ->title(__('The verification code is incorrect or has expired.'))
                ->danger()
                ->send();

            return;
        }

        $user->markEmailAsVerified();

        Notification::make()
            ->title(__('Your email has been verified successfully.'))
            ->success()
            ->send();

        redirect()->intended(Filament::getUrl());
    }

    public function resendNotificationAction(): Action
    {
        return Action::make('resendNotification')
            ->link()
            ->label('Resend verification code')
            ->size('sm')
            ->action(function (): void {
                /** @var User $user */
                $user = Filament::auth()->user();

                $sent = app(EmailVerificationCodeService::class)->resend($user);

                if (! $sent) {
                    Notification::make()
                        ->title(__('Please wait before requesting another code.'))
                        ->warning()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title(__('A new verification code has been sent to your email.'))
                    ->success()
                    ->send();
            });
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getCodeFormComponent(),
            ]);
    }

    protected function getCodeFormComponent(): Component
    {
        return TextInput::make('code')
            ->label('Verification code')
            ->required()
            ->length(6)
            ->inputMode('numeric')
            ->autocomplete('one-time-code')
            ->extraInputAttributes([
                'maxlength' => '6',
                'aria-describedby' => 'code-hint',
            ])
            ->placeholder('000000')
            ->autofocus();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Verify your email';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Email verification';
    }

    public function content(Schema $schema): Schema
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        return $schema
            ->components([
                Text::make(
                    "We sent a 6-digit verification code to {$user->email}. Enter it below to continue."
                )->id('code-hint'),
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('verify')
            ->footer([
                Actions::make($this->getFormActions())
                    ->fullWidth(true)
                    ->key('form-actions'),
            ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('verify')
                ->label('Verify email')
                ->submit('verify'),
            $this->resendNotificationAction,
        ];
    }
}
