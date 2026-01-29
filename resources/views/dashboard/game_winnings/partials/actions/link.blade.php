@if($game_winning)
    @if(method_exists($game_winning, 'trashed') && $game_winning->trashed())
        <a href="{{ route('dashboard.game_winnings.trashed.show', $game_winning) }}" class="text-decoration-none text-ellipsis">
            {{ $game_winning->name }}
        </a>
    @else
        <a href="{{ route('dashboard.game_winnings.show', $game_winning) }}" class="text-decoration-none text-ellipsis">
            {{ $game_winning->name }}
        </a>
    @endif
@else
    ---
@endif