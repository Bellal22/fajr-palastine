<x-layout :title="$supplier->name" :breadcrumbs="['dashboard.suppliers.edit', $supplier]">
    {{ BsForm::resource('suppliers')->putModel($supplier, route('dashboard.suppliers.update', $supplier)) }}
    @component('dashboard::components.box')
        @slot('title', trans('suppliers.actions.edit'))

        @include('dashboard.suppliers.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('suppliers.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>