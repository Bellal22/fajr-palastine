<x-layout :title="$choose->name" :breadcrumbs="['dashboard.chooses.edit', $choose]">
    {{ BsForm::resource('chooses')->putModel($choose, route('dashboard.chooses.update', $choose)) }}
    @component('dashboard::components.box')
        @slot('title', trans('chooses.actions.edit'))

        @include('dashboard.chooses.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('chooses.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>