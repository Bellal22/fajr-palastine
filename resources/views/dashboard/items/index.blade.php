<x-layout :title="trans('items.plural')" :breadcrumbs="['dashboard.items.index']">
    @include('dashboard.items.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('items.actions.list') ({{ $items->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\Item::class }}"
                        :resource="trans('items.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.items.partials.actions.create')
                    @include('dashboard.items.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('items.attributes.name')</th>
            <th>@lang('items.attributes.description')</th>
            <th>@lang('items.attributes.inbound_shipment_id')</th>
            <th style="width: 100px;">@lang('items.attributes.quantity')</th>
            <th style="width: 100px;">@lang('items.attributes.weight')</th>
            <th style="width: 120px;">الوزن الإجمالي (كجم)</th>
            <th style="width: 160px">الإجراءات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            @php
                $totalWeight = $item->quantity * ($item->weight ?? 0);
            @endphp
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$item"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.items.show', $item) }}"
                       class="text-decoration-none">
                        <strong>{{ $item->name }}</strong>
                    </a>
                </td>
                <td>
                    {{ Str::limit($item->description ?? '-', 50) }}
                </td>
                <td>
                    @if($item->inboundShipment)
                        <a href="{{ route('dashboard.inbound_shipments.show', $item->inboundShipment) }}"
                           class="badge badge-info">
                            {{ $item->inboundShipment->shipment_number }}
                        </a>
                    @else
                        <span class="badge badge-secondary">-</span>
                    @endif
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-center">{{ $item->weight ? number_format($item->weight, 2) : '-' }}</td>
                <td class="text-center"><strong>{{ number_format($totalWeight, 2) }}</strong></td>
                <td style="width: 160px">
                    @include('dashboard.items.partials.actions.show')
                    @include('dashboard.items.partials.actions.edit')
                    @include('dashboard.items.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('items.empty')</td>
            </tr>
        @endforelse

        @if($items->hasPages())
            @slot('footer')
                {{ $items->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
