<?php

namespace Webbingbrasil\FilamentNotification\Concerns;

use Filament\Forms\ComponentContainer;
use Webbingbrasil\FilamentNotification\Actions\Action;
use Illuminate\Database\Eloquent\Model;

/**
 * @property ComponentContainer $mountedNotificationActionForm
 */
trait HasActions
{
    public $mountedNotificationAction = null;

    public $mountedNotificationActionData = [];

    public $mountedNotificationActionRecord = null;

    protected array $cachedNotificationActions;

    public function cacheNotificationActions($type, $actions): void
    {
        $this->cachedNotificationActions[$type] = collect($actions)
            ->mapWithKeys(function (Action $action): array {
                $action->livewire($this);

                return [$action->getName() => $action];
            })
            ->toArray();
    }

    public function callMountedNotificationAction()
    {
        $action = $this->getMountedNotificationAction();

        if (! $action) {
            return;
        }

        if ($action->isHidden()) {
            return;
        }

        $data = $this->getMountedNotificationActionForm()->getState();

        try {
            return $action->call($data);
        } finally {
            $this->dispatchBrowserEvent('close-modal', [
                'id' => static::class . '-action',
            ]);
        }
    }

    public function mountNotificationAction(string $name, ?string $record = null)
    {
        $this->mountedNotificationAction = $name;
        $this->mountedNotificationActionRecord = $record;

        $action = $this->getMountedNotificationAction();

        if (! $action) {
            return;
        }

        if ($action->isHidden()) {
            return;
        }

        $this->cacheForm('mountedNotificationActionForm');

        app()->call($action->getMountUsing(), [
            'action' => $action,
            'form' => $this->getMountedNotificationActionForm(),
            'record' => $this->getMountedNotificationActionRecord(),
        ]);

        if (! $action->shouldOpenModal()) {
            return $this->callMountedNotificationAction();
        }

        $this->dispatchBrowserEvent('open-modal', [
            'id' => static::class . '-action',
        ]);
    }

    public function getCachedNotificationActions($type): array
    {
        return $this->cachedNotificationActions[$type] ?? [];
    }

    public function getMountedNotificationAction(): ?Action
    {
        if (! $this->mountedNotificationAction) {
            return null;
        }

        return $this->getCachedNotificationAction($this->mountedNotificationAction);
    }

    public function getMountedNotificationActionForm(): ComponentContainer
    {
        return $this->mountedNotificationActionForm;
    }

    public function getMountedNotificationActionRecord(): ?Model
    {
        return $this->resolveNotificationRecord($this->mountedNotificationActionRecord);
    }

    protected function getCachedNotificationAction(string $name): ?Action
    {
        $record = $this->getMountedNotificationActionRecord();
        $action = $this->getCachedNotificationActions($record->type)[$name] ?? null;
        $action?->record($record);

        return $action;
    }
}
