<x-layout :title="trans('inbound_shipments.actions.create')" :breadcrumbs="['dashboard.inbound_shipments.create']">
    {{ BsForm::resource('inbound_shipments')->post(route('dashboard.inbound_shipments.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('inbound_shipments.actions.create'))

        @include('dashboard.inbound_shipments.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('inbound_shipments.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>