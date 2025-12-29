<x-layout :title="$coupon_type->name" :breadcrumbs="['dashboard.coupon_types.show', $coupon_type]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('coupon_types.attributes.name')</th>
                        <td>{{ $coupon_type->name }}</td>
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
