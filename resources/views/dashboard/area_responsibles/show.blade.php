<x-layout :title="$area_responsible->name" :breadcrumbs="['dashboard.area_responsibles.show', $area_responsible]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('area_responsibles.attributes.name')</th>
                        <td>{{ $area_responsible->name }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('area_responsibles.attributes.phone')</th>
                        <td>{{ $area_responsible->phone }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('area_responsibles.attributes.address')</th>
                        <td>{{ $area_responsible->address }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.area_responsibles.partials.actions.edit')
                    @include('dashboard.area_responsibles.partials.actions.delete')
                    @include('dashboard.area_responsibles.partials.actions.restore')
                    @include('dashboard.area_responsibles.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>

    <div>
        {{-- Assuming $area_responsible is an instance of the AreaResponsible model and is passed to this view. --}}
        @isset($area_responsible)
            @php
                // Get the aid_id from the current AreaResponsible object.
                $currentAreaResponsibleAidId = $area_responsible->id;

                // Fetch all blocks associated with this area_responsible_id (which is the aid_id of the current area responsible)
                $blocksInArea = \App\Models\Block::where('area_responsible_id', $currentAreaResponsibleAidId)->paginate();
            @endphp

            @if(count($blocksInArea) > 0)
                @component('dashboard::components.table-box')
                    @slot('title')
                        @lang('blocks.actions.list_for_area_responsible')
                        ({{ $area_responsible->name }}) {{-- Displaying the name of the current area responsible --}}
                        ({{ $blocksInArea->total() }})
                    @endslot

                    <thead>
                        <tr>
                            <th colspan="100">
                                <div class="d-flex">
                                    {{-- Adjusting check-all-delete for Block model --}}
                                    <x-check-all-delete
                                        type="{{ \App\Models\Block::class }}"
                                        :resource="trans('blocks.plural')"></x-check-all-delete>

                                    <div class="ml-2 d-flex justify-content-between flex-grow-1">
                                        {{-- You might need to adjust these actions if they are specific to 'people' --}}
                                        @include('dashboard.blocks.partials.actions.create')
                                        @include('dashboard.blocks.partials.actions.trashed')
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 30px;" class="text-center">
                                <x-check-all></x-check-all>
                            </th>
                            <th>@lang('blocks.attributes.id')</th>
                            <th>@lang('blocks.attributes.name')</th>
                            <th>@lang('blocks.attributes.title')</th>
                            <th>@lang('blocks.attributes.phone')</th>
                            <th>@lang('blocks.attributes.lan')</th>
                            <th>@lang('blocks.attributes.lat')</th>
                            <th>@lang('blocks.attributes.note')</th>
                            <th style="width: 160px">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Iterating over the retrieved blocks in the same area --}}
                        @forelse($blocksInArea as $blockItem) {{-- Renamed $block to $blockItem to avoid conflict if $block was used elsewhere --}}
                            <tr>
                                <td class="text-center">
                                    <x-check-all-item :model="$blockItem"></x-check-all-item>
                                </td>
                                <td>
                                    <a href="{{ route('dashboard.blocks.show', $blockItem) }}"
                                       class="text-decoration-none text-ellipsis">
                                        {{ $blockItem->id }}
                                    </a>
                                </td>
                                <td>{{ $blockItem->name }}</td>
                                <td>{{ $blockItem->title }}</td>
                                <td>{{ $blockItem->phone }}</td>
                                <td>{{ $blockItem->lan }}</td>
                                <td>{{ $blockItem->lat }}</td>
                                <td>{{ $blockItem->note ?? 'لا يوجد' }}</td>

                                <td style="width: 160px">
                                    {{-- Adjusting actions for Block model --}}
                                    @include('dashboard.blocks.partials.actions.show', ['block' => $blockItem])
                                    @include('dashboard.blocks.partials.actions.edit', ['block' => $blockItem])
                                    @include('dashboard.blocks.partials.actions.delete', ['block' => $blockItem])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100" class="text-center">@lang('blocks.empty_for_area')</td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if($blocksInArea->hasPages())
                        @slot('footer')
                            {{ $blocksInArea->links() }}
                        @endslot
                    @endif
                @endcomponent
            @else
                <div class="alert alert-danger text-center">
                    @lang('area_responsibles.messages.no_blocks_for_this_area') {{-- New translation key for clarity --}}
                </div>
            @endif
        @else
            {{-- Message if $area_responsible is not defined --}}
            <div class="alert alert-warning text-center">
                @lang('area_responsibles.messages.area_responsible_not_defined') {{-- New translation key for clarity --}}
            </div>
        @endisset
    </div>

</x-layout>
