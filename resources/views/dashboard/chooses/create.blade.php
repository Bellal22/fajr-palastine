<x-layout :title="trans('chooses.actions.create')" :breadcrumbs="['dashboard.chooses.create']">
    {{ BsForm::resource('chooses')->post(route('dashboard.chooses.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('chooses.actions.create'))

        @include('dashboard.chooses.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('chooses.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>