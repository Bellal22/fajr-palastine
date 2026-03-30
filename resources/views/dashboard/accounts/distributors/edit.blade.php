<x-layout :title="trans('distributors.actions.edit')" :breadcrumbs="['dashboard.distributors.edit', $distributor]">
    {{ BsForm::resource('distributors')->put(route('dashboard.distributors.update', $distributor)) }}
    @component('dashboard::components.box')
        @slot('title', trans('distributors.actions.edit'))

        @include('dashboard.accounts.distributors.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('distributors.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
