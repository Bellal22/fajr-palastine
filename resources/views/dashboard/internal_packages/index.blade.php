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
            <th>@lang('internal_packages.attributes.description')</th>
            <th>@lang('internal_packages.attributes.created_by')</th>
            <th style="width: 100px;">@lang('internal_packages.attributes.quantity')</th>
            <th style="width: 100px;">@lang('internal_packages.attributes.weight')</th>
            <th style="width: 120px;">الوزن الإجمالي (كجم)</th>
            <th style="width: 160px">الإجراءات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($internal_packages as $internal_package)
            @php
                $totalWeight = $internal_package->quantity * ($internal_package->weight ?? 0);
            @endphp
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$internal_package"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.internal_packages.show', $internal_package) }}"
                       class="text-decoration-none">
                        <strong>{{ $internal_package->name }}</strong>
                    </a>
                </td>
                <td>
                    {{ Str::limit($internal_package->description ?? '-', 50) }}
                </td>
                <td>
                    @if($internal_package->creator)
                        <span class="badge badge-secondary">{{ $internal_package->creator->name }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="text-center">{{ $internal_package->quantity }}</td>
                <td class="text-center">{{ $internal_package->weight ? number_format($internal_package->weight, 2) : '-' }}</td>
                <td class="text-center"><strong>{{ number_format($totalWeight, 2) }}</strong></td>
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
