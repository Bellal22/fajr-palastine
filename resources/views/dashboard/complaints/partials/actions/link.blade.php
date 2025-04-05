@if($complaint)
    @if(method_exists($complaint, 'trashed') && $complaint->trashed())
        <a href="{{ route('dashboard.complaints.trashed.show', $complaint) }}" class="text-decoration-none text-ellipsis">
            {{ $complaint->name }}
        </a>
    @else
        <a href="{{ route('dashboard.complaints.show', $complaint) }}" class="text-decoration-none text-ellipsis">
            {{ $complaint->name }}
        </a>
    @endif
@else
    ---
@endif