<?php

namespace Spiggle\ListView\Support;

use Filament\Tables\Contracts\HasTable;
use Livewire\ComponentHook;

use function Livewire\on;

class ListViewHook extends ComponentHook
{
    public function render($view, $data): mixed
    {
        $component = $this->component;

        if ($component instanceof HasTable) {
            ListViewChrome::sync($component);
        }

        return function () use ($component): void {
            if ($component instanceof HasTable) {
                ListViewChrome::sync($component);
            }
        };
    }

    public function call($method, $params, $returnEarly, $metadata, $componentContext): mixed
    {
        return function () : void {
            $component = $this->component;
            if ($component instanceof HasTable) {
                ListViewChrome::sync($component);
            }
        };
    }

    public static function provide(): void
    {
        on('call', function ($component, $method, $params, $componentContext, $returnEarly): void {
            if ($method !== 'slvSetMode' || ! $component instanceof HasTable) {
                return;
            }

            $mode = is_string($params[0] ?? null) ? $params[0] : 'table';
            ListView::set($component, $mode);

            if (property_exists($component, 'tableGrouping')) {
                $component->tableGrouping = $mode === 'list' ? 'slv_letter:asc' : null;
            }

            if (method_exists($component, 'bootedInteractsWithTable')) {
                $component->bootedInteractsWithTable();
            }

            ListViewChrome::sync($component);

            $returnEarly();
        });
    }
}
