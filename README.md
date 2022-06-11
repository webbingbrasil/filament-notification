# Filament Notification (WIP)

Notification feed for Filament with actions support

## Installation

You can install the package via composer:

```bash
composer require webbingbrasil/filament-notification
```

## Add notification feed icon

Use [render-hooks](https://filamentphp.com/docs/2.x/admin/appearance#render-hooks) to register notification feed component after global search

```bash
Filament::registerRenderHook(
    'global-search.end',
    fn (): string => Blade::render('@livewire(\'filament-notification.feed\')'),
);
```

## Configure notification

All database notification are displayed in feed, so you will need to configure `via()` to use database provider and message in `toArray()` or `toDatabase()` methods.

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Webbingbrasil\FilamentNotification\Notifications\NotificationLevel;

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
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Webbingbrasil\FilamentNotification\Actions\ButtonAction;

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

- [dmandrade](https://github.com/dmandrade)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
