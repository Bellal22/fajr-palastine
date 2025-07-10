@if(method_exists($block, 'trashed') && $block->trashed())
    @can('view', $block)
        <a href="{{ route('dashboard.blocks.trashed.show', $block) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $block)
        <a href="{{ route('dashboard.blocks.show', $block) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif