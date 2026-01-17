<x-layout :title="trans('locations.actions.create')" :breadcrumbs="['dashboard.locations.create']">
    {{ BsForm::resource('locations')->post(route('dashboard.locations.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('locations.actions.create'))

        @include('dashboard.locations.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('locations.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
