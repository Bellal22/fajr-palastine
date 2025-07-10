<x-layout :title="trans('blocks.actions.create')" :breadcrumbs="['dashboard.blocks.create']">
    {{ BsForm::resource('blocks')->post(route('dashboard.blocks.store')) }}
    @component('dashboard::components.box')
        @slot('title', trans('blocks.actions.create'))

        @include('dashboard.blocks.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('blocks.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>