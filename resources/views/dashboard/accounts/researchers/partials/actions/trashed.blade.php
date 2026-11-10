@can('viewAnyTrash', \App\Models\Researcher::class)
    <a href="{{ route('dashboard.researchers.trashed', request()->only('type')) }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('researchers.trashed')
    </a>
@endcan
