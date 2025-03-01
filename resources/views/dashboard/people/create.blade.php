<x-layout :title="trans('people.actions.create')" :breadcrumbs="['dashboard.people.create']">
    {{ BsForm::resource('people')->post(route('dashboard.people.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('people.actions.create'))

        @include('dashboard.people.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('people.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>