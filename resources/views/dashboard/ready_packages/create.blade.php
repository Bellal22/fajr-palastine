<x-layout :title="trans('ready_packages.actions.create')" :breadcrumbs="['dashboard.ready_packages.create']">
    {{ BsForm::resource('ready_packages')->post(route('dashboard.ready_packages.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('ready_packages.actions.create'))

        @include('dashboard.ready_packages.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('ready_packages.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>