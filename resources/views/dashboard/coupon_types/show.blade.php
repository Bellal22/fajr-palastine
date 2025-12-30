<x-layout :title="$coupon_type->name" :breadcrumbs="['dashboard.coupon_types.show', $coupon_type]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('title', 'معلومات نوع الكوبون')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle mb-0">
                    <tbody>
                    <tr>
                        <th width="200">@lang('coupon_types.attributes.name')</th>
                        <td><strong>{{ $coupon_type->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>@lang('coupon_types.attributes.created_at')</th>
                        <td>{{ $coupon_type->created_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                    <tr>
                        <th>@lang('coupon_types.attributes.updated_at')</th>
                        <td>{{ $coupon_type->updated_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.coupon_types.partials.actions.edit')
                    @include('dashboard.coupon_types.partials.actions.delete')
                    @include('dashboard.coupon_types.partials.actions.restore')
                    @include('dashboard.coupon_types.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
