<x-layout :title="trans('ready_packages.plural')" :breadcrumbs="['dashboard.ready_packages.index']">
    @include('dashboard.ready_packages.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('ready_packages.actions.list') ({{ $ready_packages->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\ReadyPackage::class }}"
                        :resource="trans('ready_packages.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.ready_packages.partials.actions.create')
                    @include('dashboard.ready_packages.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('ready_packages.attributes.name')</th>
            <th>@lang('ready_packages.attributes.description')</th>
            <th>@lang('ready_packages.attributes.inbound_shipment_id')</th>
            <th style="width: 100px;">@lang('ready_packages.attributes.quantity')</th>
            <th style="width: 100px;">@lang('ready_packages.attributes.weight')</th>
            <th style="width: 120px;">الوزن الإجمالي (كجم)</th>
            <th style="width: 160px">الإجراءات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($ready_packages as $ready_package)
            @php
                $totalWeight = $ready_package->quantity * ($ready_package->weight ?? 0);
            @endphp
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$ready_package"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.ready_packages.show', $ready_package) }}"
                       class="text-decoration-none">
                        <strong>{{ $ready_package->name }}</strong>
                    </a>
                </td>
                <td>
                    {{ Str::limit($ready_package->description ?? '-', 50) }}
                </td>
                <td>
                    @if($ready_package->inboundShipment)
                        <a href="{{ route('dashboard.inbound_shipments.show', $ready_package->inboundShipment) }}"
                           class="badge badge-info">
                            {{ $ready_package->inboundShipment->shipment_number }}
                        </a>
                    @else
                        <span class="badge badge-secondary">-</span>
                    @endif
                </td>
                <td class="text-center">{{ $ready_package->quantity }}</td>
                <td class="text-center">{{ $ready_package->weight ? number_format($ready_package->weight, 2) : '-' }}</td>
                <td class="text-center"><strong>{{ number_format($totalWeight, 2) }}</strong></td>
                <td style="width: 160px">
                    @include('dashboard.ready_packages.partials.actions.show')
                    @include('dashboard.ready_packages.partials.actions.edit')
                    @include('dashboard.ready_packages.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('ready_packages.empty')</td>
            </tr>
        @endforelse

        @if($ready_packages->hasPages())
            @slot('footer')
                {{ $ready_packages->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
