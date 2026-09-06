<?php

namespace Spiggle\ListView;

use Filament\Support\Facades\FilamentView;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Spiggle\ListView\Support\ListView;
use Spiggle\ListView\Support\ListViewChrome;
use Spiggle\ListView\Support\ListViewHook;

class ListViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'list-view');
        Livewire::componentHook(ListViewHook::class);
        ListViewChrome::apply();

        View::composer('filament-tables::index', function ($view): void {
            $table = $view->getData()['table'] ?? null;
            if (! $table instanceof Table) {
                return;
            }

            $livewire = $table->getLivewire();
            if (! $livewire instanceof HasTable || ! ListView::isList($livewire)) {
                return;
            }

            ListViewChrome::sync($livewire);
        });

        FilamentView::registerRenderHook(TablesRenderHook::TOOLBAR_END, function (): string {
            $livewire = Livewire::current();
            if (! $livewire instanceof HasTable) {
                return '';
            }

            return Blade::render('@include(\'list-view::toggle\', [\'livewire\' => $livewire])', [
                'livewire' => $livewire,
            ]);
        });

        $this->publishes([
            __DIR__.'/../resources/css/list-view.css' => public_path('vendor/spiggle-list-view/list-view.css'),
        ], 'list-view-assets');
    }

    public static function cssPath(): string
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'list-view.css';
    }
}
