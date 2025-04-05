<x-layout :title="$complaint->name" :breadcrumbs="['dashboard.complaints.edit', $complaint]">
    {{ BsForm::resource('complaints')->putModel($complaint, route('dashboard.complaints.update', $complaint)) }}
    @component('dashboard::components.box')
        @slot('title', trans('complaints.actions.edit'))

        @include('dashboard.complaints.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('complaints.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>