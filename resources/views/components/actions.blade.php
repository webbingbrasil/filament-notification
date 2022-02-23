@props([
    'actions',
    'record',
    'align' => 'right',
])

<div
    {{ $attributes->class([
        'flex flex-wrap items-center gap-4 filament-page-actions',
        match ($align) {
            'center' => 'justify-center',
            'right' => 'justify-end',
            default => 'justify-start',
        },
    ]) }}
>
    @foreach ($actions as $action)
        @if (! $action->record($record)->isHidden())
            {{ $action }}
        @endif
    @endforeach
</div>
