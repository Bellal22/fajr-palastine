<x-layout :title="$outbound_shipment->name" :breadcrumbs="['dashboard.outbound_shipments.edit', $outbound_shipment]">
    {{ BsForm::resource('outbound_shipments')->putModel($outbound_shipment, route('dashboard.outbound_shipments.update', $outbound_shipment)) }}
    @component('dashboard::components.box')
        @slot('title', trans('outbound_shipments.actions.edit'))

        @include('dashboard.outbound_shipments.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('outbound_shipments.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>