<x-layout :title="trans('complaints.actions.create')" :breadcrumbs="['dashboard.complaints.create']">
    {{ BsForm::resource('complaints')->post(route('dashboard.complaints.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('complaints.actions.create'))

        @include('dashboard.complaints.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('complaints.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>