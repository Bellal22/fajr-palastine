@if($need_request)
    @if(method_exists($need_request, 'trashed') && $need_request->trashed())
        <a href="{{ route('dashboard.need_requests.trashed.show', $need_request) }}" class="text-decoration-none text-ellipsis">
            {{ $need_request->name }}
        </a>
    @else
        <a href="{{ route('dashboard.need_requests.show', $need_request) }}" class="text-decoration-none text-ellipsis">
            {{ $need_request->name }}
        </a>
    @endif
@else
    ---
@endif