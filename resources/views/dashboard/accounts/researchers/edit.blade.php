<x-layout :title="$researcher->name" :breadcrumbs="['dashboard.researchers.edit', $researcher]">
    {{ BsForm::resource('researchers')->putModel($researcher, route('dashboard.researchers.update', $researcher), ['files' => true]) }}
    @component('dashboard::components.box')
        @slot('title', trans('researchers.actions.edit'))

        @include('dashboard.accounts.researchers.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('researchers.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
