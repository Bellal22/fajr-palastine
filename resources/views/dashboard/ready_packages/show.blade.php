<x-layout :title="$ready_package->name" :breadcrumbs="['dashboard.ready_packages.show', $ready_package]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('ready_packages.attributes.name')</th>
                        <td>{{ $ready_package->name }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.ready_packages.partials.actions.edit')
                    @include('dashboard.ready_packages.partials.actions.delete')
                    @include('dashboard.ready_packages.partials.actions.restore')
                    @include('dashboard.ready_packages.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
