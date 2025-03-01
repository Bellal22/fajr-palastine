<x-layout :title="$neighborhood->name" :breadcrumbs="['dashboard.neighborhoods.edit', $neighborhood]">
    {{ BsForm::resource('neighborhoods')->putModel($neighborhood, route('dashboard.neighborhoods.update', $neighborhood)) }}
    @component('dashboard::components.box')
        @slot('title', trans('neighborhoods.actions.edit'))

        @include('dashboard.neighborhoods.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('neighborhoods.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>