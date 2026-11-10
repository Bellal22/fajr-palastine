@can('create', \App\Models\Researcher::class)
    <a href="{{ route('dashboard.researchers.create', request()->only('type')) }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('researchers.actions.create')
    </a>
@endcan
