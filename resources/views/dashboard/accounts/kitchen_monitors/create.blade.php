  <x-layout :title="'إضافة متابع مطبخ'" :breadcrumbs="['dashboard.kitchen_monitors.create']">
    {{ BsForm::resource('kitchen_monitors')->post(route('dashboard.kitchen_monitors.store')) }}
    @component('dashboard::components.box')
        @slot('title', 'إضافة متابع مطبخ جديد')

        @include('dashboard.accounts.kitchen_monitors.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('supervisors.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
