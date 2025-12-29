<x-layout :title="$package_content->name" :breadcrumbs="['dashboard.package_contents.edit', $package_content]">
    {{ BsForm::resource('package_contents')->putModel($package_content, route('dashboard.package_contents.update', $package_content)) }}
    @component('dashboard::components.box')
        @slot('title', trans('package_contents.actions.edit'))

        @include('dashboard.package_contents.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('package_contents.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>