<x-layout :title="trans('neighborhoods.plural')" :breadcrumbs="['dashboard.neighborhoods.index']">
    @include('dashboard.neighborhoods.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('neighborhoods.actions.list') ({{ $neighborhoods->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\Neighborhood::class }}"
                        :resource="trans('neighborhoods.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.neighborhoods.partials.actions.create')
                    @include('dashboard.neighborhoods.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('neighborhoods.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($neighborhoods as $neighborhood)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$neighborhood"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.neighborhoods.show', $neighborhood) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $neighborhood->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.neighborhoods.partials.actions.show')
                    @include('dashboard.neighborhoods.partials.actions.edit')
                    @include('dashboard.neighborhoods.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('neighborhoods.empty')</td>
            </tr>
        @endforelse

        @if($neighborhoods->hasPages())
            @slot('footer')
                {{ $neighborhoods->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
