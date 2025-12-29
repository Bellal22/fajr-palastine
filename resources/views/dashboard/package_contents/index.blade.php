<x-layout :title="trans('package_contents.plural')" :breadcrumbs="['dashboard.package_contents.index']">
    @include('dashboard.package_contents.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('package_contents.actions.list') ({{ $package_contents->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\PackageContent::class }}"
                        :resource="trans('package_contents.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.package_contents.partials.actions.create')
                    @include('dashboard.package_contents.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('package_contents.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($package_contents as $package_content)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$package_content"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.package_contents.show', $package_content) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $package_content->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.package_contents.partials.actions.show')
                    @include('dashboard.package_contents.partials.actions.edit')
                    @include('dashboard.package_contents.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('package_contents.empty')</td>
            </tr>
        @endforelse

        @if($package_contents->hasPages())
            @slot('footer')
                {{ $package_contents->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
