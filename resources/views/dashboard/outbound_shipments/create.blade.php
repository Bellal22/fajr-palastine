<x-layout :title="trans('outbound_shipments.actions.create')" :breadcrumbs="['dashboard.outbound_shipments.create']">
    {{ BsForm::resource('outbound_shipments')->post(route('dashboard.outbound_shipments.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('outbound_shipments.actions.create'))

        @include('dashboard.outbound_shipments.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('outbound_shipments.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>