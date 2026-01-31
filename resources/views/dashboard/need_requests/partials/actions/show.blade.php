@if(method_exists($need_request, 'trashed') && $need_request->trashed())
    @can('view', $need_request)
        <a href="{{ route('dashboard.need_requests.trashed.show', $need_request) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $need_request)
        <a href="{{ route('dashboard.need_requests.show', $need_request) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif