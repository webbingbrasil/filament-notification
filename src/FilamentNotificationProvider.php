<?php

namespace Webbingbrasil\FilamentNotification;

use Livewire\Livewire;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Webbingbrasil\FilamentNotification\Http\Livewire\NotificationFeed;

class FilamentNotificationProvider extends PluginServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-notification')
            ->hasConfigFile('filament-notification')
            ->hasTranslations()
            ->hasViewComponents('filament-notification')
            ->hasViews();
    }

    public function packageBooted(): void
    {
        $this->bootLivewireComponents();
    }

    protected function bootLivewireComponents(): void
    {
        Livewire::component('filament-notification.feed', NotificationFeed::class);
    }
}
