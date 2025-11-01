<x-layout :title="$item->name" :breadcrumbs="['dashboard.items.show', $item]">
    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('items.attributes.name')</th>
                        <td>{{ $item->name }}</td>
                    </tr>
                    <tr>
                        <th>@lang('items.attributes.description')</th>
                        <td>{{ $item->description }}</td>
                    </tr>
                    <tr>
                        <th>@lang('items.attributes.package')</th>
                        <td>
                            <x-boolean :value="$item->package" />
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('items.attributes.type')</th>
                        <td>{{ $item->type }}</td>
                    </tr>
                    <tr>
                        <th>@lang('items.attributes.weight')</th>
                        <td>{{ $item->weight }}</td>
                    </tr>
                    <tr>
                        <th>@lang('items.attributes.quantity')</th>
                        <td>{{ $item->quantity }}</td>
                    </tr>
                    <tr>
                        <th>@lang('items.attributes.created_at')</th>
                        <td>{{ optional($item->created_at)->toDayDateTimeString() }}</td>
                    </tr>
                    <tr>
                        <th>@lang('items.attributes.updated_at')</th>
                        <td>{{ optional($item->updated_at)->toDayDateTimeString() }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.items.partials.actions.edit')
                    @include('dashboard.items.partials.actions.delete')
                    @include('dashboard.items.partials.actions.restore')
                    @include('dashboard.items.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
