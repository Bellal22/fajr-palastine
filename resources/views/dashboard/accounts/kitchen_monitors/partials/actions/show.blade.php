@if(method_exists($kitchenMonitor, 'trashed') && $kitchenMonitor->trashed())
    @can('view', $kitchenMonitor)
        <a href="{{ route('dashboard.kitchen_monitors.trashed.show', $kitchenMonitor) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $kitchenMonitor)
        <a href="{{ route('dashboard.kitchen_monitors.show', $kitchenMonitor) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif
