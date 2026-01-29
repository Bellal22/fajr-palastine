<x-layout :title="$game_winning->name" :breadcrumbs="['dashboard.game_winnings.edit', $game_winning]">
    {{ BsForm::resource('game_winnings')->putModel($game_winning, route('dashboard.game_winnings.update', $game_winning)) }}
    @component('dashboard::components.box')
        @slot('title', trans('game_winnings.actions.edit'))

        @include('dashboard.game_winnings.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('game_winnings.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>