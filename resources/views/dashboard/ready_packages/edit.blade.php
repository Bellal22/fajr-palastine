<x-layout :title="$ready_package->name" :breadcrumbs="['dashboard.ready_packages.edit', $ready_package]">
    {{ BsForm::resource('ready_packages')->putModel($ready_package, route('dashboard.ready_packages.update', $ready_package)) }}
    @component('dashboard::components.box')
        @slot('title', trans('ready_packages.actions.edit'))

        @include('dashboard.ready_packages.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('ready_packages.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>