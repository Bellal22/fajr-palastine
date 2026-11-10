@component('dashboard::components.sidebarItem')
    @slot('can', ['ability' => 'viewAny', 'model' => \App\Models\KitchenCard::class])
    @slot('url', route('dashboard.kitchen_cards.index'))
    @slot('name', trans('kitchen_cards.plural'))
    @slot('active', request()->routeIs('*kitchen_cards*'))
    @slot('icon', 'fas fa-id-card')
    @slot('tree', [
        [
            'name' => trans('kitchen_cards.actions.list'),
            'url' => route('dashboard.kitchen_cards.index'),
            'can' => ['ability' => 'viewAny', 'model' => \App\Models\KitchenCard::class],
            'active' => request()->routeIs('*kitchen_cards.index')
            || request()->routeIs('*kitchen_cards.show'),
        ],
        [
            'name' => trans('kitchen_cards.actions.create'),
            'url' => route('dashboard.kitchen_cards.create'),
            'can' => ['ability' => 'create', 'model' => \App\Models\KitchenCard::class],
            'active' => request()->routeIs('*kitchen_cards.create'),
        ],
    ])
@endcomponent
