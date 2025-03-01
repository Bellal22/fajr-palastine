<x-layout :title="$family->name" :breadcrumbs="['dashboard.families.edit', $family]">
    {{ BsForm::resource('families')->putModel($family, route('dashboard.families.update', $family)) }}
    @component('dashboard::components.box')
        @slot('title', trans('families.actions.edit'))

        @include('dashboard.families.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('families.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>