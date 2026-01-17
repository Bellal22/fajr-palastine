<x-layout :title="trans('people.plural')" :breadcrumbs="['dashboard.people.index']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.people.partials.filter')
    </div>

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-users"></i> @lang('people.actions.list')
            <span class="badge badge-primary badge-pill">{{ count_formatted($people->total()) }}</span>
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex align-items-center flex-wrap justify-content-between">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="mr-2">
                            <x-check-all-deleteAreaResponsibles
                                type="{{ \App\Models\Person::class }}"
                                :resource="trans('people.plural')">
                            </x-check-all-deleteAreaResponsibles>
                        </div>

                        @if (auth()->user()?->isAdmin())
                            <div class="mr-2">
                                <x-check-all-assign-users
                                    type="{{ \App\Models\Person::class }}"
                                    :resource="trans('people.plural')">
                                </x-check-all-assign-users>
                            </div>

                            <div class="mr-2">
                                <x-check-all-delete
                                    type="{{ \App\Models\Person::class }}"
                                    :resource="trans('people.plural')">
                                </x-check-all-delete>
                            </div>
                        @endif

                        @if (auth()->user()?->isSupervisor())
                            <div class="mr-2">
                                <x-check-all-assignBlock
                                    type="{{ \App\Models\Person::class }}"
                                    :resource="trans('people.plural')"
                                    :blocks="$blocks">
                                </x-check-all-assignBlock>
                            </div>
                        @endif
                    </div>

                    {{-- View Button in Center --}}
                    <div class="mx-auto">
                        @include('dashboard.people.partials.actions.view')
                    </div>

                    <div class="d-flex align-items-center flex-wrap">
                        <div class="mr-2">
                            @include('dashboard.people.partials.actions.search')
                        </div>

                        <div class="mr-2">
                            @include('dashboard.people.partials.actions.trashed')
                        </div>

                        @if (auth()->user()?->isAdmin())
                            <div>
                                <a href="{{ route('dashboard.people.export.selected', request()->all()) }}"
                                class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-excel"></i>
                                    @lang('people.actions.export_all')
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </th>
        </tr>

        {{-- Table Column Headers --}}
        <tr class="bg-light">
            <th style="width: 50px">
                <x-check-all></x-check-all>
            </th>
            <th><i class="fas fa-id-card"></i> @lang('people.attributes.id_num')</th>
            <th><i class="fas fa-user"></i> @lang('people.attributes.name')</th>
            <th class="d-none d-lg-table-cell"><i class="fas fa-phone"></i> @lang('people.attributes.phone')</th>
            <th class="d-none d-xl-table-cell"><i class="fas fa-birthday-cake"></i> @lang('people.attributes.dob')</th>
            <th class="d-none d-lg-table-cell"><i class="fas fa-heart"></i> @lang('people.attributes.social_status')</th>
            <th class="d-none d-md-table-cell"><i class="fas fa-map-marker-alt"></i> @lang('people.attributes.city')</th>
            <th class="d-none d-xl-table-cell"><i class="fas fa-notes-medical"></i> @lang('people.attributes.has_condition')</th>
            <th class="d-none d-lg-table-cell"><i class="fas fa-users"></i> @lang('people.attributes.relatives_count')</th>
            <th class="d-none d-lg-table-cell"><i class="fas fa-user-check"></i> @lang('people.attributes.registered_relatives')</th>
            <th class="text-center" style="width: 100px"><i class="fas fa-cog"></i> @lang('people.actions.actions')</th>
        </tr>
        </thead>

        <tbody>
        @forelse($people as $person)
            <tr class="align-middle">
                <td>
                    <x-check-all-item :model="$person"></x-check-all-item>
                </td>

                {{-- ID Number --}}
                <td>
                    <a href="{{ route('dashboard.people.show', $person) }}"
                       class="text-decoration-none font-weight-bold text-primary">
                        <i class="fas fa-id-card text-muted"></i>
                        {{ $person->id_num }}
                    </a>
                </td>

                {{-- Full Name --}}
                <td>
                    <div class="font-weight-bold">{{ $person->first_name }} {{ $person->father_name }} {{ $person->grandfather_name }} {{ $person->family_name }}</div>
                    <small class="text-muted d-lg-none">
                        <i class="fas fa-phone"></i> {{ $person->phone }}
                    </small>
                </td>

                {{-- Phone --}}
                <td class="d-none d-lg-table-cell">
                    <a href="tel:{{ $person->phone }}" class="text-decoration-none text-success">
                        <i class="fas fa-phone"></i> {{ $person->phone }}
                    </a>
                </td>

                {{-- Date of Birth --}}
                <td class="d-none d-xl-table-cell">
                    <span class="text-muted">
                        <i class="fas fa-calendar"></i>
                        {{ $person->dob ? $person->dob->format('Y-m-d') : '-' }}
                    </span>
                </td>

                {{-- Social Status --}}
                <td class="d-none d-lg-table-cell">
                    <i class="fas fa-heart text-danger"></i>
                    {{ __($person->social_status) }}
                </td>

                {{-- City --}}
                <td class="d-none d-md-table-cell">
                    <i class="fas fa-map-marker-alt text-info"></i>
                    {{ __($person->current_city) }}
                </td>

                {{-- Has Condition --}}
                <td class="d-none d-xl-table-cell text-center">
                    @if($person->has_condition == 1)
                        <span class="badge badge-warning">
                            <i class="fas fa-check"></i> @lang('people.condition.yes')
                        </span>
                    @elseif($person->has_condition == 0)
                        <span class="badge badge-secondary">
                            <i class="fas fa-times"></i> @lang('people.condition.no')
                        </span>
                    @else
                        <span class="text-muted">{{ $person->has_condition }}</span>
                    @endif
                </td>

                {{-- Relatives Count --}}
                <td class="d-none d-lg-table-cell text-center">
                    <span class="badge badge-info badge-pill">
                        <i class="fas fa-users"></i> {{ $person->relatives_count }}
                    </span>
                </td>

                {{-- Registered Relatives --}}
                <td class="d-none d-lg-table-cell text-center">
                    <span class="badge badge-success badge-pill">
                        <i class="fas fa-user-check"></i> {{ $person->family_members_count }}
                    </span>
                </td>

                {{-- Actions Dropdown --}}
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                type="button"
                                id="dropdownMenuButton{{ $person->id }}"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right"
                            aria-labelledby="dropdownMenuButton{{ $person->id }}">

                            {{-- عرض التفاصيل --}}
                            <a href="{{ route('dashboard.people.show', $person) }}"
                            class="dropdown-item">
                                <i class="fas fa-eye text-primary"></i>
                                <span class="mr-2">@lang('people.actions.show')</span>
                            </a>

                            @if(auth()->user()?->isAdmin())
                                <div class="dropdown-divider"></div>

                                {{-- حذف مسؤول المنطقة --}}
                                @php
                                    $removeMsg = __('people.dialogs.remove_responsible');
                                @endphp
                                <form action="{{ route('dashboard.people.areaResponsible.delete', $person) }}"
                                    method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="dropdown-item"
                                            onclick="return confirm('{{ $removeMsg }}')">
                                        <i class="fas fa-user-times text-secondary"></i>
                                        <span class="mr-2">@lang('people.actions.remove_responsible')</span>
                                    </button>
                                </form>

                                <div class="dropdown-divider"></div>

                                {{-- حذف السجل --}}
                                @php
                                    $deleteMsg = __('people.dialogs.delete_record');
                                @endphp
                                <form action="{{ route('dashboard.people.destroy', $person) }}"
                                    method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="dropdown-item"
                                            onclick="return confirm('{{ $deleteMsg }}')">
                                        <i class="fas fa-trash text-danger"></i>
                                        <span class="mr-2">@lang('people.actions.delete')</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-users fa-3x mb-3 d-block"></i>
                        <h5>@lang('people.empty')</h5>
                        <p class="mb-0">@lang('people.empty_hint')</p>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($people->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        @lang('people.pagination_info', [
                            'from' => $people->firstItem() ?? 0,
                            'to' => $people->lastItem() ?? 0,
                            'total' => $people->total()
                        ])
                    </div>
                    {{ $people->appends(request()->query())->links() }}
                </div>
            @endslot
        @endif
    @endcomponent
</x-layout>
