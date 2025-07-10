@if($block)
    @if(method_exists($block, 'trashed') && $block->trashed())
        <a href="{{ route('dashboard.blocks.trashed.show', $block) }}" class="text-decoration-none text-ellipsis">
            {{ $block->name }}
        </a>
    @else
        <a href="{{ route('dashboard.blocks.show', $block) }}" class="text-decoration-none text-ellipsis">
            {{ $block->name }}
        </a>
    @endif
@else
    ---
@endif