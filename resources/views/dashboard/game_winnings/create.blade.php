<x-layout :title="trans('game_winnings.actions.create')" :breadcrumbs="['dashboard.game_winnings.create']">
    {{ BsForm::resource('game_winnings')->post(route('dashboard.game_winnings.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('game_winnings.actions.create'))

        @include('dashboard.game_winnings.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('game_winnings.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>