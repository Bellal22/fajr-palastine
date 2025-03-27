<x-layout :title="$person->name" :breadcrumbs="['dashboard.complaint.edit', $person]">
    {{ BsForm::resource('complaint')->putModel($person, route('dashboard.complaint.update', $person)) }}
    @component('dashboard::components.box')
        @slot('title', trans('complaint.actions.edit'))

        @include('dashboard.complaint.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('complaint.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
