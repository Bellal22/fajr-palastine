<x-layout :title="'تعديل بيانات متابع المطبخ'" :breadcrumbs="['dashboard.kitchen_monitors.edit', $kitchenMonitor]">
    {{ BsForm::resource('kitchen_monitors')->put(route('dashboard.kitchen_monitors.update', $kitchenMonitor)) }}
    @component('dashboard::components.box')
        @slot('title', 'تعديل بيانات متابع المطبخ')

        @include('dashboard.accounts.kitchen_monitors.partials.form', ['supervisor' => $kitchenMonitor])

        @slot('footer')
            {{ BsForm::submit()->label(trans('supervisors.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
