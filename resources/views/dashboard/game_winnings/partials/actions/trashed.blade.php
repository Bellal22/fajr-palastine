@can('viewAnyTrash', \App\Models\GameWinning::class)
    <a href="{{ route('dashboard.game_winnings.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        @lang('game_winnings.trashed')
    </a>
@endcan
