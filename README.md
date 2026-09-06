# Spiggle ListView

A **Filament v5 plugin** that adds a table/list toggle to every table on the panel.

Table view stays the default (the current Filament table). List view uses Filament’s native stacked columns and A–Z grouping, so search, pagination, and row click (slide-over or full page) keep working.

## Install

```bash
composer require spiggle/list-view
```

Register it on the panel alongside your theme:

```php
use Spiggle\ListView\Filament\ListViewPlugin;
use Spiggle\MaterialTheme\Filament\MaterialThemePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(MaterialThemePlugin::make())
        ->plugin(ListViewPlugin::make());
}
```

The toggle is injected on every `HasTable` page. List mode sorts by the title column (`name`, `title`, `number`, or the first text column) and groups records by first letter.
