@php
    use Illuminate\Contracts\Pagination\Paginator;
    use Spiggle\ListView\Support\ListView;

    $items = $records instanceof Paginator
        ? collect($records->items())
        : collect($records ?? []);
    $items = $items->sortBy(fn ($record) => strtolower(ListView::title($table, $record)))->values();
    $groups = ListView::groups($table, $items);
@endphp

<div class="slv-board">
    @forelse ($groups as $letter => $items)
        <section class="slv-group">
            <span class="slv-letter">{{ $letter }}</span>
            <div class="slv-rows">
                @foreach ($items as $record)
                    @php
                        $title = ListView::title($table, $record);
                        $subtitle = ListView::subtitle($table, $record);
                        $avatar = ListView::avatar($table, $record);
                        $href = ListView::recordHref($table, $record);
                        $action = ListView::recordAction($table, $record);
                        $key = ListView::recordKey($record);
                        $tag = $href ? 'a' : 'button';
                    @endphp
                    <{{ $tag }}
                        @if ($href)
                            href="{{ $href }}"
                        @else
                            type="button"
                            @if ($action)
                                wire:click="mountTableAction('{{ $action }}', '{{ $key }}')"
                            @endif
                        @endif
                        class="slv-row"
                    >
                        <span class="slv-avatar" aria-hidden="true">
                            <img src="{{ $avatar }}" alt="">
                        </span>
                        <span class="slv-copy">
                            <strong>{{ $title !== '' ? $title : 'Untitled' }}</strong>
                            @if ($subtitle !== '')
                                <small>{{ $subtitle }}</small>
                            @endif
                        </span>
                    </{{ $tag }}>
                @endforeach
            </div>
        </section>
    @empty
        <p class="slv-empty">No records to list.</p>
    @endforelse
</div>
