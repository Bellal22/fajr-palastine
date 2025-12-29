<x-layout :title="trans('projects.actions.create')" :breadcrumbs="['dashboard.projects.create']">
    {{ BsForm::resource('projects')->post(route('dashboard.projects.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('projects.actions.create'))

        @include('dashboard.projects.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('projects.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>