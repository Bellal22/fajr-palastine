<x-layout :title="trans('kitchen_cards.actions.edit')" :breadcrumbs="['dashboard.kitchen_cards.edit', $kitchenCard]">
    {{ BsForm::resource('kitchen_cards')->putModel($kitchenCard, route('dashboard.kitchen_cards.update', $kitchenCard)) }}
    @component('dashboard::components.box')
        @slot('title', trans('kitchen_cards.actions.edit'))

        @include('dashboard.kitchen_cards.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('kitchen_cards.actions.edit')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
