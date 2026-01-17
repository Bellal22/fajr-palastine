@if($choose)
    @if(method_exists($choose, 'trashed') && $choose->trashed())
        <a href="{{ route('dashboard.chooses.trashed.show', $choose) }}" class="text-decoration-none text-ellipsis">
            {{ $choose->name }}
        </a>
    @else
        <a href="{{ route('dashboard.chooses.show', $choose) }}" class="text-decoration-none text-ellipsis">
            {{ $choose->name }}
        </a>
    @endif
@else
    ---
@endif