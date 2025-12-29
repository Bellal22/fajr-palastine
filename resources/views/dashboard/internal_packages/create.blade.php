<x-layout :title="trans('internal_packages.actions.create')" :breadcrumbs="['dashboard.internal_packages.create']">
    {{ BsForm::resource('internal_packages')->post(route('dashboard.internal_packages.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('internal_packages.actions.create'))

        @include('dashboard.internal_packages.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('internal_packages.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>