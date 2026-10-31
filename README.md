# Spiggle ListView

A Filament v5 plugin that adds a table / list toggle to every table on the panel.

Table view stays the default (the current Filament table). List view switches to stacked rows, groups records A–Z, and keeps search, pagination, and row click (slide-over or full page).

## Screenshots

**Table view (default)** — the normal Filament table, with the toggle in the toolbar:

![Table view](docs/screenshots/table-view.png)

**List view** — alphabetical groups, initials, title + subtitle. Click a row to view or edit:

![List view](docs/screenshots/list-view.png)

**Row click** — same action as a table row (Users open a slide-over):

![List view edit](docs/screenshots/list-view-edit.png)

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13
- Filament 5.0+

## Install

The package is not on Packagist yet. Add the GitHub repository, then require it:

```bash
composer config repositories.spiggle-list-view vcs https://github.com/skillbobby/Spiggle-List-View.git
composer require spiggle/list-view:^1.0
```

Register the plugin on your Filament panel:

```php
use Spiggle\ListView\Filament\ListViewPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(ListViewPlugin::make());
}
```

It works alongside other panel plugins (for example a theme):

```php
$panel
    ->plugin(MaterialThemePlugin::make())
    ->plugin(ListViewPlugin::make());
```

Laravel auto-discovers the package service provider. After the Composer require, registering `ListViewPlugin::make()` on the panel is the only application setup: the toggle is added to every table toolbar, and styles are included with the plugin, so there is no asset publish step.

## How it works

1. A table / list control is injected at the end of every Filament table toolbar.
2. **Table** is the default. The existing columns, filters, search, and pagination stay as they are.
3. **List** rebuilds the current table as stacked rows:
   - Title from `name`, `title`, `number`, or `email`, then the first visible text column
   - Subtitle from `email`, `description`, or `subtitle` when that is not the title, otherwise the next visible text column
   - Colored initials when the record has no `avatar`, `avatar_url`, or `image`
   - Groups by the first letter of the title (A–Z); titles that do not start with a letter are grouped under `#`
4. The choice is stored in the session per Livewire table class, so each resource remembers its own mode.
5. Clicking a list row uses the same `recordAction` / `recordUrl` as the table (slide-over, modal, or full page).

No changes are required on individual resources. Any `HasTable` page gets the toggle.

## Sponsors

If this package is useful, consider supporting development:

[![GitHub Sponsors](https://img.shields.io/badge/sponsor-30363D?style=for-the-badge&logo=GitHub-Sponsors&logoColor=%23EA4AAA)](https://github.com/sponsors/skillbobby)

## License

MIT
