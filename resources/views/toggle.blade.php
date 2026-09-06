@php
    use Spiggle\ListView\Support\ListView;

    $mode = ListView::mode($livewire);
@endphp

<div class="slv-toggle" role="group" aria-label="Table or list view">
    <button
        type="button"
        class="slv-toggle-btn {{ $mode === 'table' ? 'is-active' : '' }}"
        wire:click="slvSetMode('table')"
        aria-pressed="{{ $mode === 'table' ? 'true' : 'false' }}"
        aria-label="Table view"
        title="Table view"
    >
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="3" y="5" width="18" height="14" rx="2"/>
            <path d="M3 10h18M9 5v14"/>
        </svg>
    </button>
    <button
        type="button"
        class="slv-toggle-btn {{ $mode === 'list' ? 'is-active' : '' }}"
        wire:click="slvSetMode('list')"
        aria-pressed="{{ $mode === 'list' ? 'true' : 'false' }}"
        aria-label="List view"
        title="List view"
    >
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
        </svg>
    </button>
</div>
