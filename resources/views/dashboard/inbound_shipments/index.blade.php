<x-layout :title="trans('inbound_shipments.plural')" :breadcrumbs="['dashboard.inbound_shipments.index']">
    @include('dashboard.inbound_shipments.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('inbound_shipments.actions.list') ({{ $inbound_shipments->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\InboundShipment::class }}"
                        :resource="trans('inbound_shipments.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.inbound_shipments.partials.actions.create')
                    @include('dashboard.inbound_shipments.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('inbound_shipments.attributes.shipment_number')</th>
            <th>@lang('inbound_shipments.attributes.supplier_id')</th>
            <th>@lang('inbound_shipments.attributes.inbound_type')</th>
            <th>تاريخ الإنشاء</th>
            <th style="width: 160px">الإجراءات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($inbound_shipments as $inbound_shipment)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$inbound_shipment"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.inbound_shipments.show', $inbound_shipment) }}"
                       class="text-decoration-none text-ellipsis">
                        <strong>{{ $inbound_shipment->shipment_number }}</strong>
                    </a>
                </td>
                <td>
                    {{ $inbound_shipment->supplier->name ?? '-' }}
                </td>
                <td>
                    @if($inbound_shipment->inbound_type === 'ready_package')
                        <span class="badge badge-success">طرد جاهز</span>
                    @else
                        <span class="badge badge-info">صنف مفرد</span>
                    @endif
                </td>
                <td>
                    <small class="text-muted">{{ $inbound_shipment->created_at->format('Y-m-d') }}</small>
                </td>
                <td style="width: 160px">
                    @include('dashboard.inbound_shipments.partials.actions.show')
                    @include('dashboard.inbound_shipments.partials.actions.edit')
                    @include('dashboard.inbound_shipments.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('inbound_shipments.empty')</td>
            </tr>
        @endforelse

        @if($inbound_shipments->hasPages())
            @slot('footer')
                {{ $inbound_shipments->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
