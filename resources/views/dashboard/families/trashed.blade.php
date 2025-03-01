<x-layout :title="trans('families.trashed')" :breadcrumbs="['dashboard.families.trashed']">
    @include('dashboard.families.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('families.actions.list') ({{ $families->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\Family::class }}"
                    :resource="trans('families.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\Family::class }}"
                    :resource="trans('families.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('families.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($families as $family)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$family"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.families.trashed.show', $family) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $family->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.families.partials.actions.show')
                    @include('dashboard.families.partials.actions.restore')
                    @include('dashboard.families.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('families.empty')</td>
            </tr>
        @endforelse

        @if($families->hasPages())
            @slot('footer')
                {{ $families->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
