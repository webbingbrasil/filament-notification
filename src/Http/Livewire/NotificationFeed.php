<?php

namespace Webbingbrasil\FilamentNotification\Http\Livewire;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Notifications\DatabaseNotification;
use Webbingbrasil\FilamentNotification\Concerns\HasActions;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;

class NotificationFeed extends Component implements Forms\Contracts\HasForms
{
    use WithPagination;
    use HasActions;
    use Forms\Concerns\InteractsWithForms;

    protected $feed;

    public $totalUnread;

    public function boot()
    {
        $this->refresh();
    }

    public function refresh()
    {
        $this->hydrateNotificationFeed();
        $this->prepareActions();
    }

    public function hydrateNotificationFeed()
    {
        $perPage = config('filament-notification::feed.perPage', 10);
        $notifications = config('filament-notification::feed.displayReadNotifications') 
            ? Auth::user()->notifications()
            : Auth::user()->unreadNotifications();
        $notifications = $notifications->orderByDesc('created_at');

        $onlyTypes = config('filament-notification::feed.onlyTypes', []);

        if (! empty($onlyTypes)) {
            $this->feed->whereIn('type', $onlyTypes);
        }

        $interval = config('filament-notification::feed.interval');

        if (! empty($interval)) {
            $this->feed->where('created_at', '>=', Carbon::now()->sub(CarbonInterval::create($interval)));
        }

        $this->feed = $notifications->paginate($perPage);

        $this->totalUnread = Auth::user()->unreadNotifications()->count();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        $this->refresh();
    }

    protected function getForms(): array
    {
        return [
            'mountedNotificationActionForm' => $this->makeForm()
                ->schema(($action = $this->getMountedNotificationAction()) ? $action->getFormSchema() : [])
                ->model($this->getMountedNotificationActionRecord() ?? DatabaseNotification::class)
                ->statePath('mountedNotificationActionData'),
        ];
    }

    protected function resolveNotificationRecord(?string $key): ?Model
    {
        return DatabaseNotification::find($key);
    }

    protected function prepareActions(): void
    {
        foreach ($this->feed as $notification) {
            if (isset($this->cachedNotificationActions[$notification->type])) {
                continue;
            }
            $actions = [];
            if(method_exists($notification->type, 'notificationFeedActions')) {
                $actions = call_user_func([$notification->type, 'notificationFeedActions']);
            }
            $this->cacheNotificationActions($notification->type, $actions);
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('filament-notification::feed', [
            'notifications' => $this->feed
        ]);
    }
}
