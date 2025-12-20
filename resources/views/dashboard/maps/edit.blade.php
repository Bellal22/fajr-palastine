<x-layout :title="$map->name" :breadcrumbs="['dashboard.maps.edit', $map]">
    {{ BsForm::resource('maps')->putModel($map, route('dashboard.maps.update', $map)) }}
    @component('dashboard::components.box')
        @slot('title', trans('maps.actions.edit'))

        @include('dashboard.maps.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('maps.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>