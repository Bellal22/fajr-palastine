<x-layout :title="$region->name" :breadcrumbs="['dashboard.regions.edit', $region]">
    {{ BsForm::resource('regions')->putModel($region, route('dashboard.regions.update', $region)) }}
    @component('dashboard::components.box')
        @slot('title', trans('regions.actions.edit'))

        @include('dashboard.regions.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('regions.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>