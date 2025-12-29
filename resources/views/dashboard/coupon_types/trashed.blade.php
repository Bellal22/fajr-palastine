<x-layout :title="trans('coupon_types.trashed')" :breadcrumbs="['dashboard.coupon_types.trashed']">
    @include('dashboard.coupon_types.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('coupon_types.actions.list') ({{ $coupon_types->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\CouponType::class }}"
                    :resource="trans('coupon_types.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\CouponType::class }}"
                    :resource="trans('coupon_types.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('coupon_types.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($coupon_types as $coupon_type)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$coupon_type"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.coupon_types.trashed.show', $coupon_type) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $coupon_type->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.coupon_types.partials.actions.show')
                    @include('dashboard.coupon_types.partials.actions.restore')
                    @include('dashboard.coupon_types.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('coupon_types.empty')</td>
            </tr>
        @endforelse

        @if($coupon_types->hasPages())
            @slot('footer')
                {{ $coupon_types->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
