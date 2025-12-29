<x-layout :title="$coupon_type->name" :breadcrumbs="['dashboard.coupon_types.edit', $coupon_type]">
    {{ BsForm::resource('coupon_types')->putModel($coupon_type, route('dashboard.coupon_types.update', $coupon_type)) }}
    @component('dashboard::components.box')
        @slot('title', trans('coupon_types.actions.edit'))

        @include('dashboard.coupon_types.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('coupon_types.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>