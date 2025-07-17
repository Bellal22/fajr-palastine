<x-layout :title="trans('blocks.trashed')" :breadcrumbs="['dashboard.blocks.trashed']">
    @include('dashboard.blocks.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('blocks.actions.list') ({{ $blocks->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\Block::class }}"
                    :resource="trans('blocks.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\Block::class }}"
                    :resource="trans('blocks.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('blocks.attributes.name')</th>
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
                    <a href="{{ route('dashboard.blocks.trashed.show', $block) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $block->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.blocks.partials.actions.show')
                    @include('dashboard.blocks.partials.actions.restore')
                    @include('dashboard.blocks.partials.actions.forceDelete')
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
