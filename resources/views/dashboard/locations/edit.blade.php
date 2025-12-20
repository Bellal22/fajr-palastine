<x-layout :title="$location->name" :breadcrumbs="['dashboard.locations.edit', $location]">
    {{ BsForm::resource('locations')->putModel($location, route('dashboard.locations.update', $location)) }}
    @component('dashboard::components.box')
        @slot('title', trans('locations.actions.edit'))

        @include('dashboard.locations.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('locations.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>