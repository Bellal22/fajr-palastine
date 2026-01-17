<x-layout :title="trans('chooses.trashed')" :breadcrumbs="['dashboard.chooses.trashed']">
    @include('dashboard.chooses.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('chooses.actions.list') ({{ $chooses->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\Choose::class }}"
                    :resource="trans('chooses.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\Choose::class }}"
                    :resource="trans('chooses.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('chooses.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($chooses as $choose)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$choose"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.chooses.trashed.show', $choose) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $choose->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.chooses.partials.actions.show')
                    @include('dashboard.chooses.partials.actions.restore')
                    @include('dashboard.chooses.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('chooses.empty')</td>
            </tr>
        @endforelse

        @if($chooses->hasPages())
            @slot('footer')
                {{ $chooses->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
