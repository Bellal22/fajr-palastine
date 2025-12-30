<x-layout :title="trans('outbound_shipments.plural')" :breadcrumbs="['dashboard.outbound_shipments.index']">
    @include('dashboard.outbound_shipments.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('outbound_shipments.actions.list') ({{ $outbound_shipments->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\OutboundShipment::class }}"
                        :resource="trans('outbound_shipments.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.outbound_shipments.partials.actions.create')
                    @include('dashboard.outbound_shipments.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('outbound_shipments.attributes.shipment_number')</th>
            <th>@lang('outbound_shipments.attributes.project_id')</th>
            <th>@lang('outbound_shipments.attributes.sub_warehouse_id')</th>
            <th>@lang('outbound_shipments.attributes.driver_name')</th>
            <th>تاريخ الإنشاء</th>
            <th style="width: 160px">الإجراءات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($outbound_shipments as $outbound_shipment)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$outbound_shipment"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.outbound_shipments.show', $outbound_shipment) }}"
                       class="text-decoration-none text-ellipsis">
                        <strong>{{ $outbound_shipment->shipment_number }}</strong>
                    </a>
                </td>
                <td>
                    {{ $outbound_shipment->project->name ?? '-' }}
                </td>
                <td>
                    {{ $outbound_shipment->subWarehouse->name ?? '-' }}
                </td>
                <td>
                    {{ $outbound_shipment->driver_name ?? '-' }}
                </td>
                <td>
                    <small class="text-muted">{{ $outbound_shipment->created_at->format('Y-m-d') }}</small>
                </td>
                <td style="width: 160px">
                    @include('dashboard.outbound_shipments.partials.actions.show')
                    @include('dashboard.outbound_shipments.partials.actions.edit')
                    @include('dashboard.outbound_shipments.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('outbound_shipments.empty')</td>
            </tr>
        @endforelse

        @if($outbound_shipments->hasPages())
            @slot('footer')
                {{ $outbound_shipments->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
