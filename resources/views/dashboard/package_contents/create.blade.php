<x-layout :title="trans('package_contents.actions.create')" :breadcrumbs="['dashboard.package_contents.create']">
    {{ BsForm::resource('package_contents')->post(route('dashboard.package_contents.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('package_contents.actions.create'))

        @include('dashboard.package_contents.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('package_contents.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>