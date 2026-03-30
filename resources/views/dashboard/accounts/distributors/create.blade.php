<x-layout :title="trans('distributors.actions.create')" :breadcrumbs="['dashboard.distributors.create']">
    {{ BsForm::resource('distributors')->post(route('dashboard.distributors.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('distributors.actions.create'))

        @include('dashboard.accounts.distributors.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('distributors.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
