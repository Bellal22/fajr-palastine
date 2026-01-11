<x-layout :title="trans('blocks.plural')" :breadcrumbs="['dashboard.blocks.index']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.blocks.partials.filter')
    </div>

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-walking"></i> @lang('blocks.actions.list')
            <span class="badge badge-primary badge-pill">{{ count_formatted($blocks->total()) }}</span>
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex align-items-center flex-wrap justify-content-between">
                    <div class="d-flex align-items-center flex-wrap">
                        {{-- <div class="mr-2">
                            <x-check-all-delete
                                type="{{ \App\Models\Block::class }}"
                                :resource="trans('blocks.plural')">
                            </x-check-all-delete>
                        </div> --}}
                    </div>

                    <div class="d-flex align-items-center flex-wrap">
                        <div class="mr-2">
                            @include('dashboard.blocks.partials.actions.trashed')
                        </div>

                        <div>
                            @include('dashboard.blocks.partials.actions.create')
                        </div>
                    </div>
                </div>
            </th>
        </tr>

        {{-- Table Column Headers --}}
        <tr class="bg-light">
            {{-- <th style="width: 50px">
                <x-check-all></x-check-all>
            </th> --}}
            <th><i class="fas fa-walking"></i> @lang('blocks.attributes.name')</th>
            <th><i class="fas fa-user-tie"></i> @lang('blocks.attributes.title')</th>
            <th class="d-none d-lg-table-cell"><i class="fas fa-phone"></i> @lang('blocks.attributes.phone')</th>
            <th class="text-center"><i class="fas fa-user-friends"></i> @lang('blocks.attributes.people_count')</th>
            <th class="d-none d-md-table-cell"><i class="fas fa-user-shield"></i> @lang('blocks.attributes.area_responsible')</th>
            <th class="text-center" style="width: 100px"><i class="fas fa-cog"></i> @lang('blocks.actions.actions')</th>
        </tr>
        </thead>

        <tbody>
        @forelse($blocks as $block)
            <tr class="align-middle">
                {{-- <td>
                    <x-check-all-item :model="$block"></x-check-all-item>
                </td> --}}

                {{-- Block Name --}}
                <td>
                    <a href="{{ route('dashboard.blocks.show', $block) }}"
                       class="text-decoration-none font-weight-bold text-primary">
                        <i class="fas fa-walking text-muted"></i>
                        {{ $block->name }}
                    </a>
                </td>

                {{-- Title --}}
                <td>
                    <div class="font-weight-bold">{{ $block->title }}</div>
                    <small class="text-muted d-lg-none">
                        <i class="fas fa-phone"></i> {{ $block->phone }}
                    </small>
                </td>

                {{-- Phone --}}
                <td class="d-none d-lg-table-cell">
                    <a href="tel:{{ $block->phone }}" class="text-decoration-none text-success">
                        <i class="fas fa-phone"></i> {{ $block->phone }}
                    </a>
                </td>

                {{-- People Count --}}
                <td class="text-center">
                    <span class="badge badge-primary badge-pill">
                        <i class="fas fa-users"></i> {{ $block->people_count }}
                    </span>
                </td>

                {{-- Area Responsible --}}
                <td class="d-none d-md-table-cell">
                    <i class="fas fa-user-shield text-info"></i>
                    {{ $block->areaResponsible->name }}
                </td>

                {{-- Actions Dropdown --}}
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                type="button"
                                id="dropdownMenuButton{{ $block->id }}"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right"
                            aria-labelledby="dropdownMenuButton{{ $block->id }}">

                            {{-- عرض التفاصيل --}}
                            <a href="{{ route('dashboard.blocks.show', $block) }}"
                            class="dropdown-item">
                                <i class="fas fa-eye text-primary"></i>
                                <span class="mr-2">@lang('blocks.actions.show')</span>
                            </a>

                            {{-- تعديل --}}
                            <a href="{{ route('dashboard.blocks.edit', $block) }}"
                            class="dropdown-item">
                                <i class="fas fa-edit text-info"></i>
                                <span class="mr-2">@lang('blocks.actions.edit')</span>
                            </a>

                            {{-- <div class="dropdown-divider"></div>

                            <form action="{{ route('dashboard.blocks.destroy', $block) }}"
                                method="POST"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="dropdown-item"
                                        onclick="return confirm('@lang('blocks.dialogs.delete.info')')">
                                    <i class="fas fa-trash text-danger"></i>
                                    <span class="mr-2">@lang('blocks.actions.delete')</span>
                                </button>
                            </form> --}}
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-walking fa-3x mb-3 d-block"></i>
                        <h5>@lang('blocks.empty')</h5>
                        <p class="mb-0">@lang('blocks.empty_hint')</p>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($blocks->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        @lang('blocks.pagination_info', [
                            'from' => $blocks->firstItem() ?? 0,
                            'to' => $blocks->lastItem() ?? 0,
                            'total' => $blocks->total()
                        ])
                    </div>
                    {{ $blocks->appends(request()->query())->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
