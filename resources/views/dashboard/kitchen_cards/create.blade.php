<x-layout :title="trans('kitchen_cards.actions.create')" :breadcrumbs="[]">
    {{ BsForm::resource('kitchen_cards')->post(route('dashboard.kitchen_cards.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('kitchen_cards.actions.create'))

        @include('dashboard.kitchen_cards.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('kitchen_cards.actions.create')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
