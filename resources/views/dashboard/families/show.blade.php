<x-layout :title="$family->name" :breadcrumbs="['dashboard.families.show', $family]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('families.attributes.name')</th>
                        <td>{{ $family->name }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.families.partials.actions.edit')
                    @include('dashboard.families.partials.actions.delete')
                    @include('dashboard.families.partials.actions.restore')
                    @include('dashboard.families.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
