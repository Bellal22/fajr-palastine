<x-layout :title="trans('chooses.plural')" :breadcrumbs="['dashboard.chooses.index']">
    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.chooses.partials.filter')
    </div>

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-cogs text-primary"></i> @lang('chooses.actions.list')
            <span class="badge badge-primary badge-pill ml-2">{{ count_formatted($types->count()) }}</span>
        @endslot

        <thead>
        <tr>
          <th colspan="100" class="py-3 px-4">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small font-weight-bold">
                    <i class="fas fa-info-circle mr-1"></i> @lang('chooses.all_types_hint')
                </span>
                <div class="d-flex align-items-center">
                    @include('dashboard.chooses.partials.actions.create')
                    <div class="ml-2">
                        @include('dashboard.chooses.partials.actions.trashed')
                    </div>
                </div>
            </div>
          </th>
        </tr>
        <tr class="bg-light border-top border-bottom">
            <th style="width: 80px;" class="text-center py-3">#</th>
            <th class="py-3"><i class="fas fa-folder-open text-info mr-2"></i> @lang('chooses.attributes.type')</th>
            <th style="width: 150px" class="text-center py-3"><i class="fas fa-list-ol text-info mr-1"></i> @lang('chooses.count')</th>
            <th style="width: 150px" class="text-center py-3"><i class="fas fa-tools text-info mr-1"></i> @lang('chooses.actions.options')</th>
        </tr>
        </thead>
        <tbody>
        @forelse($types as $typeObj)
            <tr class="align-middle border-bottom">
                <td class="text-center font-weight-bold text-muted">
                    {{ $loop->iteration }}
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="folder-icon mr-3">
                            <i class="fas fa-folder fa-2x text-warning shadow-sm"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <a href="{{ route('dashboard.chooses.show', $typeObj->type) }}"
                               class="text-decoration-none font-weight-bold text-dark h6 mb-0">
                                @lang('chooses.types.' . $typeObj->type)
                            </a>
                            <small class="text-muted mt-1">
                                @lang('chooses.singular') #{{ $typeObj->type }}
                            </small>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge badge-light border rounded-pill px-3 py-2 font-weight-bold shadow-sm">
                        {{ $typeObj->total_count }} @lang('chooses.singular')
                    </span>
                </td>

                <td class="text-center">
                    <a href="{{ route('dashboard.chooses.show', $typeObj->type) }}"
                       class="btn btn-sm btn-primary shadow-sm px-4 font-weight-bold">
                        <i class="fas fa-eye"></i> @lang('chooses.actions.show')
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3 d-block border p-4 rounded-circle bg-light d-inline-block"></i>
                        <h5>@lang('chooses.empty')</h5>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    @endcomponent
</x-layout>
