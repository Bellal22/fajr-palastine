<x-layout :title="$person->name" :breadcrumbs="['dashboard.people.edit', $person]">
    {{ BsForm::resource('people')->putModel($person, route('dashboard.people.update', $person)) }}
    @component('dashboard::components.box')
        @slot('title', trans('people.actions.edit'))

        @include('dashboard.people.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('people.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>