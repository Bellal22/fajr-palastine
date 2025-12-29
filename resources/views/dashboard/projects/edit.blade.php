<x-layout :title="$project->name" :breadcrumbs="['dashboard.projects.edit', $project]">
    {{ BsForm::resource('projects')->putModel($project, route('dashboard.projects.update', $project)) }}
    @component('dashboard::components.box')
        @slot('title', trans('projects.actions.edit'))

        @include('dashboard.projects.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('projects.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>