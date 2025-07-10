<x-layout :title="$block->name" :breadcrumbs="['dashboard.blocks.show', $block]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                        <tr>
                            <th width="200">@lang('blocks.attributes.name')</th>
                            <td>{{ $block->name }}</td>
                        </tr>
                        <tr>
                            <th width="200">@lang('blocks.attributes.area_responsible')</th>
                            <td>{{ $block->areaResponsible?->name }}</td>
                        </tr>
                        <tr>
                            <th width="200">@lang('blocks.attributes.title')</th>
                            <td>{{ $block->title }}</td>
                        </tr>
                        <tr>
                            <th width="200">@lang('blocks.attributes.phone')</th>
                            <td>{{ $block->phone }}</td>
                        </tr>
                        <tr>
                            <th width="200">@lang('blocks.attributes.limit_num')</th>
                            <td>{{ $block->limit_num }}</td>
                        </tr>
                        <tr>
                            <th width="200">@lang('blocks.attributes.lat')</th>
                            <td>{{ $block->lat }}</td>
                        </tr>
                        <tr>
                            <th width="200">@lang('blocks.attributes.lan')</th>
                            <td>{{ $block->lan }}</td>
                        </tr>
                        <tr>
                            <th width="200">@lang('blocks.attributes.note')</th>
                            <td>{{ $block->note }}</td>
                        </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.blocks.partials.actions.edit')
                    @include('dashboard.blocks.partials.actions.delete')
                    @include('dashboard.blocks.partials.actions.restore')
                    @include('dashboard.blocks.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
