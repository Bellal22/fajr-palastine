<x-layout :title="trans('coupon_types.actions.create')" :breadcrumbs="['dashboard.coupon_types.create']">
    {{ BsForm::resource('coupon_types')->post(route('dashboard.coupon_types.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('coupon_types.actions.create'))

        @include('dashboard.coupon_types.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('coupon_types.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>