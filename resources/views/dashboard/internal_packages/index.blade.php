<x-layout :title="trans('internal_packages.plural')" :breadcrumbs="['dashboard.internal_packages.index']">
    @include('dashboard.internal_packages.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('internal_packages.actions.list') ({{ $internal_packages->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\InternalPackage::class }}"
                        :resource="trans('internal_packages.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.internal_packages.partials.actions.create')
                    @include('dashboard.internal_packages.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('internal_packages.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($internal_packages as $internal_package)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$internal_package"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.internal_packages.show', $internal_package) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $internal_package->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.internal_packages.partials.actions.show')
                    @include('dashboard.internal_packages.partials.actions.edit')
                    @include('dashboard.internal_packages.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('internal_packages.empty')</td>
            </tr>
        @endforelse

        @if($internal_packages->hasPages())
            @slot('footer')
                {{ $internal_packages->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
