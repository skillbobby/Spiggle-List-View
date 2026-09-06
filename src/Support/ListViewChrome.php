<?php

namespace Spiggle\ListView\Support;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Components\ComponentManager;
use Filament\Support\Components\Contracts\ScopedComponentManager;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ListViewChrome
{
    /**
     * @var array<int, true>
     */
    protected static array $appliedTo = [];

    public static function apply(): void
    {
        $managers = [app(ComponentManager::class)];

        if (app()->bound(ScopedComponentManager::class)) {
            $managers[] = app(ScopedComponentManager::class);
        }

        foreach ($managers as $manager) {
            $id = spl_object_id($manager);

            if (isset(self::$appliedTo[$id])) {
                continue;
            }

            self::$appliedTo[$id] = true;
            $manager->configureUsing(Table::class, function (Table $table): void {
                self::decorate($table);
            }, isImportant: true);
        }
    }

    public static function sync(HasTable $livewire): void
    {
        if (! ListView::isList($livewire)) {
            return;
        }

        $table = $livewire->getTable();
        self::applyListLayout($table);

        if (property_exists($livewire, 'tableGrouping')) {
            $livewire->tableGrouping = 'slv_letter:asc';
        }
    }

    protected static function decorate(Table $table): void
    {
        $livewire = $table->getLivewire();

        $table->pushHeaderActions([
            ActionGroup::make([
                Action::make('slvModeTable')
                    ->label('Table view')
                    ->icon(Heroicon::OutlinedTableCells)
                    ->iconButton()
                    ->iconSize(IconSize::Small)
                    ->color(fn (): string => ListView::mode($livewire) === 'table' ? 'primary' : 'gray')
                    ->action(function () use ($livewire): void {
                        ListView::set($livewire, 'table');
                    }),
                Action::make('slvModeList')
                    ->label('List view')
                    ->icon(Heroicon::OutlinedQueueList)
                    ->iconButton()
                    ->iconSize(IconSize::Small)
                    ->color(fn (): string => ListView::mode($livewire) === 'list' ? 'primary' : 'gray')
                    ->action(function () use ($livewire): void {
                        ListView::set($livewire, 'list');
                    }),
            ])
                ->buttonGroup()
                ->extraAttributes(['class' => 'slv-toggle']),
        ]);

        $table->extraAttributes(function () use ($livewire): array {
            return [
                'class' => ListView::isList($livewire) ? 'slv-is-list' : 'slv-is-table',
            ];
        }, merge: true);
    }

    public static function applyListLayout(Table $table): void
    {
        if (array_key_exists('slv_avatar', $table->getColumns())) {
            return;
        }

        $title = ListView::titleAttribute($table);
        $subtitle = ListView::subtitleAttribute($table);

        $stack = [
            TextColumn::make($title)
                ->weight(FontWeight::Medium)
                ->searchable()
                ->sortable(),
        ];

        if (is_string($subtitle) && $subtitle !== '') {
            $stack[] = TextColumn::make($subtitle)
                ->color('gray')
                ->size(TextSize::ExtraSmall)
                ->searchable();
        }

        $table->columns([
            Split::make([
                ViewColumn::make('slv_avatar')
                    ->label('')
                    ->view('list-view::avatar')
                    ->getStateUsing(fn (mixed $record): string => ListView::avatar($table, $record))
                    ->grow(false),
                Stack::make($stack)->space(1),
            ]),
        ]);

        $group = Group::make('slv_letter')
            ->label('')
            ->titlePrefixedWithLabel(false)
            ->getKeyFromRecordUsing(function (Model $record) use ($table): string {
                return ListView::letter(ListView::title($table, $record));
            })
            ->getTitleFromRecordUsing(function (Model $record) use ($table): string {
                return ListView::letter(ListView::title($table, $record));
            })
            ->groupQueryUsing(fn ($query) => $query)
            ->orderQueryUsing(function ($query, string $direction) use ($title) {
                if ($title === '' || str_contains($title, '.')) {
                    return $query;
                }

                return $query->orderBy($query->getModel()->qualifyColumn($title), $direction);
            })
            ->scopeQueryByKeyUsing(fn ($query) => $query);

        $table
            ->groups([$group])
            ->defaultGroup($group)
            ->groupingSettingsHidden()
            ->defaultSort($title, 'asc');
    }
}
