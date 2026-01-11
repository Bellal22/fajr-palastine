<x-layout :title="trans('area_responsibles.plural')" :breadcrumbs="['dashboard.area_responsibles.index']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.area_responsibles.partials.filter')
    </div>

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-user-tie"></i> @lang('area_responsibles.actions.list')
            <span class="badge badge-primary badge-pill">{{ count_formatted($area_responsibles->total()) }}</span>
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex align-items-center flex-wrap justify-content-between">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="mr-2">
                            <x-check-all-delete
                                type="{{ \App\Models\AreaResponsible::class }}"
                                :resource="trans('area_responsibles.plural')">
                            </x-check-all-delete>
                        </div>
                    </div>

                    <div class="d-flex align-items-center flex-wrap">
                        <div class="mr-2">
                            @include('dashboard.area_responsibles.partials.actions.create')
                        </div>

                        <div>
                            @include('dashboard.area_responsibles.partials.actions.trashed')
                        </div>
                    </div>
                </div>
            </th>
        </tr>

        {{-- Table Column Headers --}}
        <tr class="bg-light">
            <th style="width: 50px">
                <x-check-all></x-check-all>
            </th>
            <th><i class="fas fa-user"></i> @lang('area_responsibles.attributes.name')</th>
            <th><i class="fas fa-phone"></i> @lang('area_responsibles.attributes.phone')</th>
            <th class="text-center"><i class="fas fa-users"></i> @lang('area_responsibles.attributes.block_count')</th>
            <th class="text-center"><i class="fas fa-user-check"></i> @lang('area_responsibles.attributes.person_count')</th>
            <th class="d-none d-lg-table-cell"><i class="fas fa-map-marker-alt"></i> @lang('area_responsibles.attributes.address')</th>
            <th class="text-center" style="width: 100px"><i class="fas fa-cog"></i> @lang('area_responsibles.actions.actions')</th>
        </tr>
        </thead>

        <tbody>
        @forelse($area_responsibles as $area_responsible)
            <tr class="align-middle">
                <td>
                    <x-check-all-item :model="$area_responsible"></x-check-all-item>
                </td>

                {{-- Name --}}
                <td>
                    <a href="{{ route('dashboard.area_responsibles.show', $area_responsible) }}"
                       class="text-decoration-none font-weight-bold">
                        <i class="fas fa-user-tie text-muted"></i>
                        {{ $area_responsible->name }}
                    </a>
                </td>

                {{-- Phone --}}
                <td>
                    @if($area_responsible->phone)
                        <a href="tel:{{ $area_responsible->phone }}" class="text-success">
                            <i class="fas fa-phone"></i> {{ $area_responsible->phone }}
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                {{-- Block Count --}}
                <td class="text-center">
                    <span class="badge badge-secondary badge-pill">
                        <i class="fas fa-users"></i> {{ $area_responsible->blocks->count() }}
                    </span>
                </td>

                {{-- Person Count --}}
                <td class="text-center">
                    <span class="badge badge-info badge-pill">
                        <i class="fas fa-user-check"></i> {{ $area_responsible->people_count }}
                    </span>
                </td>

                {{-- Address --}}
                <td class="d-none d-lg-table-cell">
                    {{ $area_responsible->address ?? '-' }}
                </td>

                {{-- Actions Dropdown --}}
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                type="button"
                                id="dropdownMenuButton{{ $area_responsible->id }}"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right"
                            aria-labelledby="dropdownMenuButton{{ $area_responsible->id }}">

                            {{-- عرض التفاصيل --}}
                            <a href="{{ route('dashboard.area_responsibles.show', $area_responsible) }}"
                            class="dropdown-item">
                                <i class="fas fa-eye text-primary"></i>
                                <span class="mr-2">@lang('area_responsibles.actions.show')</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            {{-- تعديل --}}
                            <a href="{{ route('dashboard.area_responsibles.edit', $area_responsible) }}"
                            class="dropdown-item">
                                <i class="fas fa-edit text-primary"></i>
                                <span class="mr-2">@lang('area_responsibles.actions.edit')</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            {{-- تحديث العدد --}}
                            <button type="button"
                                    class="dropdown-item"
                                    onclick="refreshCount({{ $area_responsible->id }})">
                                <i class="fas fa-sync text-secondary"></i>
                                <span class="mr-2">@lang('area_responsibles.actions.refresh_count')</span>
                            </button>

                            <div class="dropdown-divider"></div>

                            {{-- حذف --}}
                            <form action="{{ route('dashboard.area_responsibles.destroy', $area_responsible) }}"
                                method="POST"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="dropdown-item"
                                        onclick="return confirm('@lang('area_responsibles.dialogs.delete.info')')">
                                    <i class="fas fa-trash text-danger"></i>
                                    <span class="mr-2">@lang('area_responsibles.actions.delete')</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-user-tie fa-3x mb-3 d-block"></i>
                        <h5>@lang('area_responsibles.empty')</h5>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($area_responsibles->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        @lang('area_responsibles.pagination_info', [
                            'from' => $area_responsibles->firstItem() ?? 0,
                            'to' => $area_responsibles->lastItem() ?? 0,
                            'total' => $area_responsibles->total()
                        ])
                    </div>
                    {{ $area_responsibles->appends(request()->query())->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>

@push('scripts')
<script>
function refreshCount(areaResponsibleId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (!confirm('@lang('area_responsibles.messages.confirm_refresh_count')')) {
        return;
    }

    fetch(`/dashboard/area-responsibles/${areaResponsibleId}/refresh-count`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(async response => {
        const text = await response.text();
        try {
            const data = JSON.parse(text);
            if (data.success) {
                alert('@lang('area_responsibles.messages.count_refreshed')');
                location.reload();
            } else {
                alert('@lang('area_responsibles.messages.refresh_error'): ' + (data.message || '@lang('area_responsibles.messages.unknown_error')'));
            }
        } catch (err) {
            console.error('Response is not JSON:', text);
            alert('@lang('area_responsibles.messages.invalid_response')');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('@lang('area_responsibles.messages.refresh_error'): ' + error.message);
    });
}
</script>
@endpush
