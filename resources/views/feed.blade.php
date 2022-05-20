<div
    x-data="{
    isOpen: false,
}"
    class="relative"
>
    <button
        x-on:click="isOpen = ! isOpen"
        @class([
            'flex items-center justify-center w-10 h-10 '
        ])

    >
        <x-heroicon-o-bell class="h-5 -mr-1 align-text-top  @if($this->totalUnread) animate-swing @endif origin-top" />
        @if($this->totalUnread)
            <sup class="inline-flex items-center justify-center p-1 text-xs leading-none text-white bg-danger-600 rounded-full">
                {{ $this->totalUnread }}
            </sup>
        @endif
    </button>

    <div
        x-show="isOpen"
        x-on:click.away="isOpen = false"
        x-transition:enter="transition"
        x-transition:enter-start="-translate-y-1 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-1 opacity-0"
        x-cloak
        @class([
        'absolute z-10 right-0 rtl:right-auto rtl:left-0 mt-2 shadow-xl bg-white rounded-xl w-80 top-full',
        'dark:border-gray-600 dark:bg-gray-700' => config('filament.dark_mode'),
    ])
    >
        @if($notifications->isNotEmpty())
        <ul @class([
        'py-1 px-1 space-y-1 overflow-hidden divide-y divide-gray-300',
        'dark:border-gray-600 dark:bg-gray-700' => config('filament.dark_mode'),
    ])>

            @foreach($notifications as $notification)
                <li @class([
                    'relative',
                    $notification->read() ? 'opacity-50' : '',
                    ])>
                        <div class="flex items-center w-full h-8 px-3 text-sm font-medium">
                            @php
                                $icon = match (Arr::get($notification->data, 'level', 'info')) {
                                    'info' => 'heroicon-o-information-circle',
                                    'warning' => 'heroicon-o-exclamation-circle',
                                    'error' => 'heroicon-o-x-circle',
                                    'success' => 'heroicon-o-check-circle',
                                }
                            @endphp
                            @svg($icon, ['class' => 'mr-2 -ml-1 rtl:ml-2 rtl:-mr-1 w-6 h-6 text-gray-500'])

                            {{ Arr::get($notification->data, 'title') }}
                        </div>
                        <small class="px-3 text-sm font-normal">{{ Arr::get($notification->data, 'message') }}</small>

                        <x-filament-notification::actions :actions="$this->getCachedNotificationActions($notification->type)" :record="$notification" />
                </li>
            @endforeach
        </ul>

        {{ $notifications->links() }}


        <div class="p-2">
            <x-filament-notification::button
                wire:click="markAllAsRead"
                :color="config('filament-notification.buttons.markAllRead.color', 'primary')"
                :outlined="config('filament-notification.buttons.markAllRead.outlined', false)"
                :icon="config('filament-notification.buttons.markAllRead.icon', 'filament-notification::icon-check-all')"
                :size="config('filament-notification.buttons.markAllRead.size', 'sm')"
                class="w-full mt-2 h-8"
            >
                {{ trans('filament-notification::component.buttons.markAllRead') }}
            </x-filament-notification::button>
        </div>
        @else

        <div class="flex items-center w-full h-8 px-3 text-sm font-medium">
            Empty
        </div>
        @endif
    </div>

    <form wire:submit.prevent="callMountedNotificationAction">
        @php
            $action = $this->getMountedNotificationAction();
        @endphp

        <x-tables::modal :id="\Illuminate\Support\Str::of(static::class)->replace('\\', '\\\\') . '-action'" :width="$action?->getModalWidth()" display-classes="block">
            @if ($action)
                @if ($action->isModalCentered())
                    <x-slot name="heading">
                        {{ $action->getModalHeading() }}
                    </x-slot>

                    @if ($subheading = $action->getModalSubheading())
                        <x-slot name="subheading">
                            {{ $subheading }}
                        </x-slot>
                    @endif
                @else
                    <x-slot name="header">
                        <x-tables::modal.heading>
                            {{ $action->getModalHeading() }}
                        </x-tables::modal.heading>
                    </x-slot>
                @endif

                @if ($action->hasFormSchema())
                    {{ $this->getMountedNotificationActionForm() }}
                @endif

                <x-slot name="footer">
                    <x-tables::modal.actions :full-width="$action->isModalCentered()">
                        @foreach ($action->getModalActions() as $modalAction)
                            {{ $modalAction }}
                        @endforeach
                    </x-tables::modal.actions>
                </x-slot>
            @endif
        </x-tables::modal>
    </form>
</div>
