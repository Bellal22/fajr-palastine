<x-layout :title="trans('maps.actions.create')" :breadcrumbs="['dashboard.maps.create']">
    {{ BsForm::resource('maps')->post(route('dashboard.maps.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('maps.actions.create'))

        @include('dashboard.maps.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('maps.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>