<x-layout :title="$internal_package->name" :breadcrumbs="['dashboard.internal_packages.show', $internal_package]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('internal_packages.attributes.name')</th>
                        <td>{{ $internal_package->name }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.internal_packages.partials.actions.edit')
                    @include('dashboard.internal_packages.partials.actions.delete')
                    @include('dashboard.internal_packages.partials.actions.restore')
                    @include('dashboard.internal_packages.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
