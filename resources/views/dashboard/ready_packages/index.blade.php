<x-layout :title="trans('ready_packages.plural')" :breadcrumbs="['dashboard.ready_packages.index']">
    @include('dashboard.ready_packages.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('ready_packages.actions.list') ({{ $ready_packages->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\ReadyPackage::class }}"
                        :resource="trans('ready_packages.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.ready_packages.partials.actions.create')
                    @include('dashboard.ready_packages.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('ready_packages.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($ready_packages as $ready_package)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$ready_package"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.ready_packages.show', $ready_package) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $ready_package->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.ready_packages.partials.actions.show')
                    @include('dashboard.ready_packages.partials.actions.edit')
                    @include('dashboard.ready_packages.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('ready_packages.empty')</td>
            </tr>
        @endforelse

        @if($ready_packages->hasPages())
            @slot('footer')
                {{ $ready_packages->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
