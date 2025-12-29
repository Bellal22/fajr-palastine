<x-layout :title="$package_content->name" :breadcrumbs="['dashboard.package_contents.show', $package_content]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('package_contents.attributes.name')</th>
                        <td>{{ $package_content->name }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.package_contents.partials.actions.edit')
                    @include('dashboard.package_contents.partials.actions.delete')
                    @include('dashboard.package_contents.partials.actions.restore')
                    @include('dashboard.package_contents.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
