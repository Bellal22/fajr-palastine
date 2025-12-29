<x-layout :title="trans('sub_warehouses.actions.create')" :breadcrumbs="['dashboard.sub_warehouses.create']">
    {{ BsForm::resource('sub_warehouses')->post(route('dashboard.sub_warehouses.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('sub_warehouses.actions.create'))

        @include('dashboard.sub_warehouses.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('sub_warehouses.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>