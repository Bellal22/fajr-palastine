<x-layout :title="trans('items.actions.create')" :breadcrumbs="['dashboard.items.create']">
    {{ BsForm::resource('items')->post(route('dashboard.items.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('items.actions.create'))

        @include('dashboard.items.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('items.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
