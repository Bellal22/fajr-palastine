<x-layout :title="$outbound_shipment_item->name" :breadcrumbs="['dashboard.outbound_shipment_items.edit', $outbound_shipment_item]">
    {{ BsForm::resource('outbound_shipment_items')->putModel($outbound_shipment_item, route('dashboard.outbound_shipment_items.update', $outbound_shipment_item)) }}
    @component('dashboard::components.box')
        @slot('title', trans('outbound_shipment_items.actions.edit'))

        @include('dashboard.outbound_shipment_items.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('outbound_shipment_items.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>