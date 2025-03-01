<x-layout :title="trans('neighborhoods.actions.create')" :breadcrumbs="['dashboard.neighborhoods.create']">
    {{ BsForm::resource('neighborhoods')->post(route('dashboard.neighborhoods.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('neighborhoods.actions.create'))

        @include('dashboard.neighborhoods.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('neighborhoods.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>