<x-layout :title="trans('suppliers.actions.create')" :breadcrumbs="['dashboard.suppliers.create']">
    {{ BsForm::resource('suppliers')->post(route('dashboard.suppliers.store'), ['files' => true]) }}
    @component('dashboard::components.box')
        @slot('title', trans('suppliers.actions.create'))

        @include('dashboard.suppliers.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('suppliers.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
