<x-layout :title="$choose->name" :breadcrumbs="['dashboard.chooses.show', $choose]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('chooses.attributes.name')</th>
                        <td>{{ $choose->name }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.chooses.partials.actions.edit')
                    @include('dashboard.chooses.partials.actions.delete')
                    @include('dashboard.chooses.partials.actions.restore')
                    @include('dashboard.chooses.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
