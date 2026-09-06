<?php

namespace Spiggle\ListView\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Spiggle\ListView\ListViewServiceProvider;
use Spiggle\ListView\Support\ListViewChrome;

class ListViewPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'spiggle-list-view';
    }

    public function register(Panel $panel): void
    {
        $cssPath = ListViewServiceProvider::cssPath();

        $panel
            ->assets([
                Css::make('spiggle-list-view', $cssPath),
            ], 'spiggle/list-view')
            ->renderHook(PanelsRenderHook::HEAD_END, function () use ($cssPath): HtmlString {
                $css = is_file($cssPath) ? (string) file_get_contents($cssPath) : '';

                return new HtmlString('<style id="spiggle-list-view">'.$css.'</style>');
            });
    }

    public function boot(Panel $panel): void
    {
        ListViewChrome::apply();
    }
}
