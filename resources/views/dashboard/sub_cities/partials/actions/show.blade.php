@if(method_exists($sub_city, 'trashed') && $sub_city->trashed())
    @can('view', $sub_city)
        <a href="{{ route('dashboard.sub_cities.trashed.show', $sub_city) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $sub_city)
        <a href="{{ route('dashboard.sub_cities.show', $sub_city) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif