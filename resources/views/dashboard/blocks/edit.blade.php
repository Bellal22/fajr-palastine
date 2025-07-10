<x-layout :title="$block->name" :breadcrumbs="['dashboard.blocks.edit', $block]">
    {{ BsForm::resource('blocks')->putModel($block, route('dashboard.blocks.update', $block)) }}
    @component('dashboard::components.box')
        @slot('title', trans('blocks.actions.edit'))

        @include('dashboard.blocks.partials.form')

        @slot('footer')
            {{ BsForm::submit()->label(trans('blocks.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>