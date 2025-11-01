<x-layout :title="trans('items.trashed')" :breadcrumbs="['dashboard.items.trashed']">
    @include('dashboard.items.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('items.actions.list') ({{ $items->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\Item::class }}"
                    :resource="trans('items.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\Item::class }}"
                    :resource="trans('items.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('items.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$item"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.items.trashed.show', $item) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $item->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.items.partials.actions.show')
                    @include('dashboard.items.partials.actions.restore')
                    @include('dashboard.items.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('items.empty')</td>
            </tr>
        @endforelse

        @if($items->hasPages())
            @slot('footer')
                {{ $items->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
