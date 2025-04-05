<x-layout :title="$complaint->name" :breadcrumbs="['dashboard.complaints.show', $complaint]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('complaints.attributes.name')</th>
                        <td>{{ $complaint->name }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.complaints.partials.actions.edit')
                    @include('dashboard.complaints.partials.actions.delete')
                    @include('dashboard.complaints.partials.actions.restore')
                    @include('dashboard.complaints.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
