<x-layout :title="$area_responsible->name" :breadcrumbs="['dashboard.area_responsibles.edit', $area_responsible]">
    {{ BsForm::resource('area_responsibles')->putModel($area_responsible, route('dashboard.area_responsibles.update', $area_responsible)) }}
    @component('dashboard::components.box')
        @slot('title', trans('area_responsibles.actions.edit'))

        @include('dashboard.area_responsibles.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('area_responsibles.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>