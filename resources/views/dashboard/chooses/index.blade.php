<x-layout :title="trans('chooses.plural')" :breadcrumbs="['dashboard.chooses.index']">
    @include('dashboard.chooses.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('chooses.actions.list') ({{ $chooses->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\Choose::class }}"
                        :resource="trans('chooses.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.chooses.partials.actions.create')
                    @include('dashboard.chooses.partials.actions.trashed')
                </div>
            </div>
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
                    <a href="{{ route('dashboard.chooses.show', $choose) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $choose->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.chooses.partials.actions.show')
                    @include('dashboard.chooses.partials.actions.edit')
                    @include('dashboard.chooses.partials.actions.delete')
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
