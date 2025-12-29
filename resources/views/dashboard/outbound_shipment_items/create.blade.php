<x-layout :title="trans('outbound_shipment_items.actions.create')" :breadcrumbs="['dashboard.outbound_shipment_items.create']">
    {{ BsForm::resource('outbound_shipment_items')->post(route('dashboard.outbound_shipment_items.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('outbound_shipment_items.actions.create'))

        @include('dashboard.outbound_shipment_items.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('outbound_shipment_items.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>