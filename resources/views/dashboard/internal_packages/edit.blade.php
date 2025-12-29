<x-layout :title="$internal_package->name" :breadcrumbs="['dashboard.internal_packages.edit', $internal_package]">
    {{ BsForm::resource('internal_packages')->putModel($internal_package, route('dashboard.internal_packages.update', $internal_package)) }}
    @component('dashboard::components.box')
        @slot('title', trans('internal_packages.actions.edit'))

        @include('dashboard.internal_packages.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('internal_packages.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>