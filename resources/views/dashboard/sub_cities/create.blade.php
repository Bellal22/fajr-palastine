<x-layout :title="trans('sub_cities.actions.create')" :breadcrumbs="['dashboard.sub_cities.create']">
    {{ BsForm::resource('sub_cities')->post(route('dashboard.sub_cities.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('sub_cities.actions.create'))

        @include('dashboard.sub_cities.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('sub_cities.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>