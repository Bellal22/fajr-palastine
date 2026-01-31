<x-layout :title="trans('need_requests.actions.create')" :breadcrumbs="['dashboard.need_requests.create']">
    {{ BsForm::resource('need_requests')->post(route('dashboard.need_requests.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('need_requests.actions.create'))

        @include('dashboard.need_requests.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('need_requests.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
    @include('dashboard.need_requests.partials.skipped_modal')
</x-layout>