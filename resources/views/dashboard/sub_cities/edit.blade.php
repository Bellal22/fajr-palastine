<x-layout :title="$sub_city->name" :breadcrumbs="['dashboard.sub_cities.edit', $sub_city]">
    {{ BsForm::resource('sub_cities')->putModel($sub_city, route('dashboard.sub_cities.update', $sub_city)) }}
    @component('dashboard::components.box')
        @slot('title', trans('sub_cities.actions.edit'))

        @include('dashboard.sub_cities.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('sub_cities.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>