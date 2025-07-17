<x-layout :title="trans('area_responsibles.actions.create')" :breadcrumbs="['dashboard.area_responsibles.create']">
    {{ BsForm::resource('area_responsibles')->post(route('dashboard.area_responsibles.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('area_responsibles.actions.create'))

        @include('dashboard.area_responsibles.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('area_responsibles.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>