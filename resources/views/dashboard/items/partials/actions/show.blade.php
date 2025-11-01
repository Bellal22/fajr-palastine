@if(method_exists($item, 'trashed') && $item->trashed())
    @can('view', $item)
        <a href="{{ route('dashboard.items.trashed.show', $item) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $item)
        <a href="{{ route('dashboard.items.show', $item) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif