<x-layout :title="$item->name" :breadcrumbs="['dashboard.items.edit', $item]">
    {{ BsForm::resource('items')->putModel($item, route('dashboard.items.update', $item)) }}
    @component('dashboard::components.box')
        @slot('title', trans('items.actions.edit'))

        @include('dashboard.items.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('items.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>
