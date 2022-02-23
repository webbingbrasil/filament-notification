# Filament Notification

Notification feed for Filament with actions support

## Installation

You can install the package via composer:

```bash
composer require webbingbrasil/filament-notification
```

## Add notification feed icon

First you will need to publish filament views 

```bash
php artisan vendor:publish --tag=filament-views
```

add `@livewire('filament-notification.feed')` to `resources/views/vendor/filament/components/layouts/app.blade.php` before top bar user menu:

```php
...
    <div class="flex-1 flex gap-4 items-center justify-between">
        <x-filament::layouts.app.topbar.breadcrumbs :breadcrumbs="$breadcrumbs" />

        @livewire('filament.core.global-search')
        
        <!–– add notification feed icon before top bar user menu ––>
        @livewire('filament-notification.feed') 
        
        <x-filament::layouts.app.topbar.user-menu />
    </div>
...
```

After that, delete unused views from `resources/views/vendor/filament`

## Configure notification

All database notification are displayed in feed, so you will need to configure `via()` to use database provider and message in `toArray()` or `toDatabase()` methods.

```php
class UserNotification extends Notification
{

    public function via($notifiable)
    {
        return [
            'database'
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'level' => NotificationLevel::INFO, 
            'title' => 'Info notification', 
            'message' => 'Lorem ipsum'
        ];
    }
}
```

## Notification actions

You can add actions to any notification displayed in feed using `notificationFeedActions()` method:

```php
class UserNotification extends Notification
{
    static public function notificationFeedActions()
    {
        return [
            ButtonAction::make('markRead')->icon('heroicon-o-check')
                ->label('Mark as read')
                ->hidden(fn($record) => $record->read()) // Use $record to access/update notification, this is DatabaseNotification model
                ->action(function ($record, $livewire) {
                    $record->markAsRead();
                    $livewire->refresh(); // $livewire can be used to refresh ou reset notification feed
                })
                ->outlined()
                ->color('secondary'),
            ButtonAction::make('profile')
                ->label('Complete Profile')
                ->hidden(fn($record) => $record->read())
                ->icon('heroicon-o-user')
                ->action(function ($record, $livewire, $data) {
                    $record->markAsRead();
                    $livewire->refresh();
                    Auth::user()->update($data);
                })
                ->form([
                    DatePicker::make('birthday')
                        ->label('Birthday')
                        ->required(),
                ])
                ->modalHeading('Complete Profile')
                ->modalSubheading('Complete you profile information')
                ->modalButton('Save')
                ->outlined()
                ->color('secondary'),
        ];
    }
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [dmadrnade](https://github.com/dmadrnade)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
