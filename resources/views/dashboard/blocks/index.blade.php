<x-layout :title="trans('blocks.plural')" :breadcrumbs="['dashboard.blocks.index']">
    @include('dashboard.blocks.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('blocks.actions.list') ({{ $blocks->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\Block::class }}"
                        :resource="trans('blocks.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
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
            <th>@lang('blocks.attributes.name')</th>
            <th>@lang('blocks.attributes.title')</th>
            <th>@lang('blocks.attributes.phone')</th>
            <th>@lang('blocks.attributes.limit_num')</th>
            <th>@lang('blocks.attributes.area_responsible')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($blocks as $block)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$block"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.blocks.show', $block) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $block->name }}
                    </a>
                </td>

                <td>{{ $block->title }}</td>
                <td>{{ $block->phone }}</td>
                <td>{{ $block->limit_num }}</td>
                <td>{{ $block->areaResponsible->name }}</td>

                <td style="width: 160px">
                    @include('dashboard.blocks.partials.actions.show')
                    @include('dashboard.blocks.partials.actions.edit')
                    @include('dashboard.blocks.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('blocks.empty')</td>
            </tr>
        @endforelse

        @if($blocks->hasPages())
            @slot('footer')
                {{ $blocks->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
