<x-layout :title="trans('internal_packages.trashed')" :breadcrumbs="['dashboard.internal_packages.trashed']">
    @include('dashboard.internal_packages.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('internal_packages.actions.list') ({{ $internal_packages->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\InternalPackage::class }}"
                    :resource="trans('internal_packages.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\InternalPackage::class }}"
                    :resource="trans('internal_packages.plural')"></x-check-all-restore>
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
                    <a href="{{ route('dashboard.internal_packages.trashed.show', $internal_package) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $internal_package->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.internal_packages.partials.actions.show')
                    @include('dashboard.internal_packages.partials.actions.restore')
                    @include('dashboard.internal_packages.partials.actions.forceDelete')
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
