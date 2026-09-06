<?php

namespace Spiggle\ListView\Support;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ListView
{
    public static function key(HasTable $livewire): string
    {
        return 'slv:'.$livewire::class;
    }

    public static function mode(HasTable $livewire): string
    {
        return session(self::key($livewire), 'table') === 'list' ? 'list' : 'table';
    }

    public static function set(HasTable $livewire, string $mode): void
    {
        session([self::key($livewire) => $mode === 'list' ? 'list' : 'table']);
    }

    public static function isList(HasTable $livewire): bool
    {
        return self::mode($livewire) === 'list';
    }

    public static function titleAttribute(Table $table): string
    {
        $names = collect($table->getColumns())->map->getName();

        foreach (['name', 'title', 'number', 'email'] as $preferred) {
            if ($names->contains($preferred)) {
                return $preferred;
            }
        }

        foreach ($table->getColumns() as $column) {
            if ($column instanceof TextColumn && $column->isVisible()) {
                return $column->getName();
            }
        }

        return 'name';
    }

    public static function subtitleAttribute(Table $table): ?string
    {
        $title = self::titleAttribute($table);
        $names = collect($table->getColumns())->map->getName();

        foreach (['email', 'description', 'subtitle'] as $preferred) {
            if ($preferred !== $title && $names->contains($preferred)) {
                return $preferred;
            }
        }

        foreach ($table->getColumns() as $column) {
            if ($column instanceof TextColumn && $column->isVisible() && $column->getName() !== $title) {
                return $column->getName();
            }
        }

        return null;
    }

    public static function title(Table $table, mixed $record): string
    {
        $value = data_get($record, self::titleAttribute($table));

        return trim((string) $value);
    }

    public static function subtitle(Table $table, mixed $record): string
    {
        $attribute = self::subtitleAttribute($table);
        if (! $attribute) {
            return '';
        }

        return trim((string) data_get($record, $attribute));
    }

    public static function avatar(Table $table, mixed $record): string
    {
        foreach (['avatar', 'avatar_url', 'image'] as $attribute) {
            $value = data_get($record, $attribute);
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        $label = self::title($table, $record) ?: self::subtitle($table, $record);

        return ListAvatar::dataUri($label !== '' ? $label : 'Item');
    }

    public static function letter(string $title): string
    {
        $letter = strtoupper(substr(trim($title), 0, 1));

        return ctype_alpha($letter) ? $letter : '#';
    }

    /**
     * @param  Collection<int, mixed>  $records
     * @return Collection<string, Collection<int, mixed>>
     */
    public static function groups(Table $table, Collection $records): Collection
    {
        return $records
            ->mapToGroups(function (mixed $record) use ($table): array {
                $title = self::title($table, $record);

                return [self::letter($title) => $record];
            })
            ->sortKeys();
    }

    public static function recordHref(Table $table, mixed $record): ?string
    {
        $url = $table->getRecordUrl($record);

        return is_string($url) && $url !== '' ? $url : null;
    }

    public static function recordAction(Table $table, mixed $record): ?string
    {
        $action = $table->getRecordAction($record);

        return is_string($action) && $action !== '' ? $action : null;
    }

    public static function recordKey(mixed $record): string
    {
        if ($record instanceof Model) {
            return (string) $record->getKey();
        }

        return (string) data_get($record, 'id', spl_object_id($record));
    }
}
