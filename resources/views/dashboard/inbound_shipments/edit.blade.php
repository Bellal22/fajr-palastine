<x-layout :title="$inbound_shipment->name" :breadcrumbs="['dashboard.inbound_shipments.edit', $inbound_shipment]">
    {{ BsForm::resource('inbound_shipments')->putModel($inbound_shipment, route('dashboard.inbound_shipments.update', $inbound_shipment)) }}
    @component('dashboard::components.box')
        @slot('title', trans('inbound_shipments.actions.edit'))

        @include('dashboard.inbound_shipments.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('inbound_shipments.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>