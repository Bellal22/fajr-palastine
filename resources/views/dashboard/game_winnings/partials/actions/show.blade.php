@if(method_exists($game_winning, 'trashed') && $game_winning->trashed())
    @can('view', $game_winning)
        <a href="{{ route('dashboard.game_winnings.trashed.show', $game_winning) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@else
    @can('view', $game_winning)
        <a href="{{ route('dashboard.game_winnings.show', $game_winning) }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa fa-fw fa-eye"></i>
        </a>
    @endcan
@endif