@can('create', \App\Models\GameWinning::class)
    <a href="{{ route('dashboard.game_winnings.create') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa fa-fw fa-plus"></i>
        @lang('game_winnings.actions.create')
    </a>
@endcan
