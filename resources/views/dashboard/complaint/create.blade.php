<x-layout :title="trans('complaint.actions.create')" :breadcrumbs="['dashboard.complaint.create']">
    {{ BsForm::resource('complaint')->post(route('dashboard.complaint.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('complaint.actions.create'))

        @include('dashboard.complaint.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('complaint.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
