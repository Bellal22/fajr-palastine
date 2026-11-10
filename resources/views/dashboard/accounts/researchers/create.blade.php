<x-layout :title="trans('researchers.actions.create')" :breadcrumbs="['dashboard.researchers.create']">
    {{ BsForm::resource('researchers')->post(route('dashboard.researchers.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('researchers.actions.create'))

        @include('dashboard.accounts.researchers.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('researchers.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
