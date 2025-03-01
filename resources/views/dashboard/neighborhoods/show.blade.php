<x-layout :title="$neighborhood->name" :breadcrumbs="['dashboard.neighborhoods.show', $neighborhood]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('neighborhoods.attributes.name')</th>
                        <td>{{ $neighborhood->name }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.neighborhoods.partials.actions.edit')
                    @include('dashboard.neighborhoods.partials.actions.delete')
                    @include('dashboard.neighborhoods.partials.actions.restore')
                    @include('dashboard.neighborhoods.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
