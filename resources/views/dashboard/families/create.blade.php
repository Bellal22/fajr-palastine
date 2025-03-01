<x-layout :title="trans('families.actions.create')" :breadcrumbs="['dashboard.families.create']">
    {{ BsForm::resource('families')->post(route('dashboard.families.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('families.actions.create'))

        @include('dashboard.families.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('families.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>