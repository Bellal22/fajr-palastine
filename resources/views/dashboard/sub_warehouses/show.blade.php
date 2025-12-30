<x-layout :title="$sub_warehouse->name" :breadcrumbs="['dashboard.sub_warehouses.show', $sub_warehouse]">
    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title', 'معلومات المستودع الفرعي')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle mb-0">
                    <tbody>
                    <tr>
                        <th width="200">@lang('sub_warehouses.attributes.name')</th>
                        <td><strong>{{ $sub_warehouse->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>@lang('sub_warehouses.attributes.address')</th>
                        <td>{{ $sub_warehouse->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('sub_warehouses.attributes.contact_person')</th>
                        <td>
                            @if($sub_warehouse->contact_person)
                                <i class="fas fa-user text-secondary"></i> {{ $sub_warehouse->contact_person }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('sub_warehouses.attributes.phone')</th>
                        <td>
                            @if($sub_warehouse->phone)
                                <i class="fas fa-phone text-success"></i>
                                <a href="tel:{{ $sub_warehouse->phone }}" class="text-decoration-none">{{ $sub_warehouse->phone }}</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('sub_warehouses.attributes.created_at')</th>
                        <td>{{ $sub_warehouse->created_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                    <tr>
                        <th>@lang('sub_warehouses.attributes.updated_at')</th>
                        <td>{{ $sub_warehouse->updated_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.sub_warehouses.partials.actions.edit')
                    @include('dashboard.sub_warehouses.partials.actions.delete')
                    @include('dashboard.sub_warehouses.partials.actions.restore')
                    @include('dashboard.sub_warehouses.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
