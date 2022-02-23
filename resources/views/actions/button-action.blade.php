@php
    $action = $getAction();
    $record = $getRecord();

    if (! $action) {
        $clickAction = null;
    } elseif ($record) {
        $clickAction = "mountNotificationAction('{$getName()}', '{$record->getKey()}')";
    } else {
        $clickAction = "mountNotificationAction('{$getName()}')";
    }
@endphp

<x-filament-notification::button
    :tag="((! $action) && $url) ? 'a' : 'button'"
    :wire:click="$clickAction"
    :href="$getUrl()"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : null"
    :color="$getColor()"
    :outlined="$isOutlined()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
    size="sm"
>
    {{ $getLabel() }}
</x-filament-notification::button>
