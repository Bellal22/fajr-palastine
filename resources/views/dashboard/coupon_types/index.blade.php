<x-layout :title="trans('coupon_types.plural')" :breadcrumbs="['dashboard.coupon_types.index']">
    @include('dashboard.coupon_types.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('coupon_types.actions.list') ({{ $coupon_types->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\CouponType::class }}"
                        :resource="trans('coupon_types.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.coupon_types.partials.actions.create')
                    @include('dashboard.coupon_types.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('coupon_types.attributes.name')</th>
            <th style="width: 150px;">@lang('coupon_types.attributes.created_at')</th>
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
                    <a href="{{ route('dashboard.coupon_types.show', $coupon_type) }}"
                       class="text-decoration-none text-ellipsis">
                        <strong>{{ $coupon_type->name }}</strong>
                    </a>
                </td>
                <td>
                    <span class="text-muted">
                        <i class="fas fa-calendar text-info"></i> {{ $coupon_type->created_at->format('Y-m-d') }}
                    </span>
                </td>

                <td style="width: 160px">
                    @include('dashboard.coupon_types.partials.actions.show')
                    @include('dashboard.coupon_types.partials.actions.edit')
                    @include('dashboard.coupon_types.partials.actions.delete')
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
