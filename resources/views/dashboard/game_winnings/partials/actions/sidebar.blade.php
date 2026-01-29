@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\GameWinning::class])
    @slot('url', route('dashboard.game_winnings.index'))
    @slot('name', trans('game_winnings.plural'))
    @slot('active', request()->routeIs('*game_winnings*'))
    @slot('icon', 'fas fa-dharmachakra')
    @slot('tree', [
        [
            'name' => trans('game_winnings.actions.list'),
            'url' => route('dashboard.game_winnings.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\GameWinning::class],
            'active' => request()->routeIs('*game_winnings.index')
            || request()->routeIs('*game_winnings.show'),
        ],
        [
            'name' => trans('game_winnings.actions.create'),
            'url' => route('dashboard.game_winnings.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\GameWinning::class],
            'active' => request()->routeIs('*game_winnings.create'),
        ],
        [
            'name' => trans('game_winnings.actions.verify'),
            'url' => route('dashboard.game_winnings.verify'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\GameWinning::class],
            'active' => request()->routeIs('*game_winnings.verify*'),
        ],
    ])
@endcomponent
