<x-layout :title="$need_request->name" :breadcrumbs="['dashboard.need_requests.edit', $need_request]">
    {{ BsForm::resource('need_requests')->putModel($need_request, route('dashboard.need_requests.update', $need_request)) }}
    @component('dashboard::components.box')
        @slot('title', trans('need_requests.actions.edit'))

        @include('dashboard.need_requests.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('need_requests.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>