<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Pages\EmailVerification\EmailVerificationPrompt;
use App\Filament\Auth\Pages\Register;
use App\Filament\Pages\Dashboard;
use App\Http\Middleware\FilamentAuthenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->domain('admin.'.parse_url(config('app.url'), PHP_URL_HOST))
            ->path('')
            ->login()
            ->registration(Register::class)
            ->emailVerification(EmailVerificationPrompt::class)
            ->passwordReset()
            ->profile()
            ->colors([
                'primary' => Color::hex('#22c55e'),
                'secondary' => Color::hex('#3b82f6'),
                'success' => Color::hex('#50D26E'),
                'warning' => Color::hex('#DC9632'),
                'info' => Color::hex('#32BEC8'),
                'danger' => Color::hex('#F85149'),
                'gray' => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                FilamentAuthenticate::class,
            ]);
    }
}
