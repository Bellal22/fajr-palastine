<x-layout :title="$sub_warehouse->name" :breadcrumbs="['dashboard.sub_warehouses.edit', $sub_warehouse]">
    {{ BsForm::resource('sub_warehouses')->putModel($sub_warehouse, route('dashboard.sub_warehouses.update', $sub_warehouse)) }}
    @component('dashboard::components.box')
        @slot('title', trans('sub_warehouses.actions.edit'))

        @include('dashboard.sub_warehouses.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('sub_warehouses.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>