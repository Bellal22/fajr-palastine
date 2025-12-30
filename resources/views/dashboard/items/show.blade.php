<x-layout :title="'صنف: ' . $item->name" :breadcrumbs="['dashboard.items.show', $item]">
    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title', 'معلومات الصنف')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle mb-0">
                    <tbody>
                    <tr>
                        <th width="200">@lang('items.attributes.name')</th>
                        <td><strong>{{ $item->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>@lang('items.attributes.description')</th>
                        <td>{{ $item->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('items.attributes.inbound_shipment_id')</th>
                        <td>
                            @if($item->inboundShipment)
                                <a href="{{ route('dashboard.inbound_shipments.show', $item->inboundShipment) }}"
                                   class="badge badge-info">
                                    {{ $item->inboundShipment->shipment_number }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('items.attributes.quantity')</th>
                        <td><span class="badge badge-primary">{{ $item->quantity }}</span></td>
                    </tr>
                    <tr>
                        <th>@lang('items.attributes.weight')</th>
                        <td>{{ $item->weight ? number_format($item->weight, 2) . ' كجم' : '-' }}</td>
                    </tr>
                    <tr>
                        <th>الوزن الإجمالي</th>
                        <td>
                            <strong>{{ number_format($item->quantity * ($item->weight ?? 0), 2) }} كجم</strong>
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('items.attributes.created_at')</th>
                        <td>{{ $item->created_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.items.partials.actions.edit')
                    @include('dashboard.items.partials.actions.delete')
                    @include('dashboard.items.partials.actions.restore')
                    @include('dashboard.items.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
