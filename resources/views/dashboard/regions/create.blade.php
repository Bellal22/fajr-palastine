<x-layout :title="trans('regions.actions.create')" :breadcrumbs="['dashboard.regions.create']">
    {{ BsForm::resource('regions')->post(route('dashboard.regions.store'), ['id' => 'regionForm']) }}
        @component('dashboard::components.box')
            @slot('title', trans('regions.actions.create'))

            @include('dashboard.regions.partials.form')

            @slot('footer')
                {{ BsForm::submit()->label(trans('regions.actions.save')) }}
            @endslot
        @endcomponent
    {{ BsForm::close() }}
</x-layout>
