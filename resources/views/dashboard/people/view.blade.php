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
            {{-- Actions Row --}}
            <tr>
                <th colspan="100">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        {{-- Left Actions --}}
                        <div class="d-flex align-items-center flex-wrap">
                            @if (auth()->user()?->isAdmin())
                                <div class="mr-2">
                                    <x-check-all-delete
                                        type="{{ \App\Models\Person::class }}"
                                        :resource="trans('people.plural')">
                                    </x-check-all-delete>
                                </div>

                                <div class="mr-2">
                                    <x-check-all-api
                                        type="{{ \App\Models\Person::class }}"
                                        :resource="trans('people.plural')">
                                    </x-check-all-api>
                                </div>

                                <div class="mr-2">
                                    <x-check-all-deleteAreaResponsibles
                                        type="{{ \App\Models\Person::class }}"
                                        :resource="trans('people.plural')">
                                    </x-check-all-deleteAreaResponsibles>
                                </div>

                                <div class="mr-2">
                                    <x-check-all-assign-users
                                        type="{{ \App\Models\Person::class }}"
                                        :resource="trans('people.plural')">
                                    </x-check-all-assign-users>
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

                        {{-- Right Actions --}}
                        <div class="d-flex align-items-center flex-wrap">
                            {{-- قسم البحث --}}
                            <div class="mr-2">
                                @include('dashboard.people.partials.actions.search')
                            </div>

                            {{-- قسم الأزرار --}}
                            @if (auth()->user()?->isAdmin())
                                <div class="btn-group" role="group">
                                    {{-- زر تصدير الكل --}}
                                    <a href="{{ route('dashboard.people.export.view', request()->all()) }}"
                                       class="btn btn-outline-success btn-sm"
                                       title="@lang('people.actions.export_all_hint')">
                                        <i class="fa-fw fas fa-file-excel"></i>
                                        @lang('people.actions.export_all_short')
                                    </a>

                                    {{-- زر تصدير الأطفال --}}
                                    <a href="{{ route('dashboard.people.export.exportChildren', request()->all()) }}"
                                       class="btn btn-outline-success btn-sm"
                                       title="@lang('people.actions.export_children_hint')">
                                        <i class="fa-fw fas fa-child"></i>
                                        @lang('people.actions.export_children')
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </th>
            </tr>

            {{-- Table Column Headers --}}
            <tr class="bg-light">
                <th style="width: 30px;" class="text-center">
                    <x-check-all></x-check-all>
                </th>
                <th>
                    <i class="fas fa-id-card"></i> @lang('people.attributes.id_num')
                </th>
                <th>
                    <i class="fas fa-user"></i> @lang('people.attributes.name')
                </th>
                <th>
                    <i class="fas fa-phone"></i> @lang('people.attributes.phone')
                </th>
                <th>
                    <i class="fas fa-heart"></i> @lang('people.attributes.social_status')
                </th>
                <th>
                    <i class="fas fa-user-tie"></i> @lang('people.attributes.area_responsible')
                </th>
                <th>
                    <i class="fas fa-users"></i> @lang('people.attributes.block')
                </th>
                <th>
                    <i class="fas fa-notes-medical"></i> @lang('people.attributes.has_condition')
                </th>
                <th>
                    <i class="fas fa-users"></i> @lang('people.attributes.relatives_count')
                </th>
                <th style="width: 160px" class="text-center">
                    <i class="fas fa-cog"></i> @lang('people.actions.actions')
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse($people as $person)
                <tr class="align-middle">
                    {{-- Checkbox --}}
                    <td class="text-center">
                        <x-check-all-item :model="$person"></x-check-all-item>
                    </td>

                    {{-- ID Number --}}
                    <td>
                        @if($person->id_num)
                            <a href="{{ route('dashboard.people.show', $person) }}"
                               class="text-decoration-none text-primary font-weight-bold">
                                <i class="fas fa-id-card text-muted"></i>
                                {{ $person->id_num }}
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Full Name --}}
                    <td>
                        @if($person->first_name || $person->father_name || $person->grandfather_name || $person->family_name)
                            <span class="font-weight-bold">
                                {{ $person->first_name }}
                                {{ $person->father_name }}
                                {{ $person->grandfather_name }}
                                {{ $person->family_name }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Phone --}}
                    <td>
                        @if($person->phone)
                            <a href="tel:{{ $person->phone }}"
                               class="text-decoration-none text-success">
                                <i class="fas fa-phone"></i> {{ $person->phone }}
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Social Status --}}
                    <td>
                        @if($person->social_status)
                            <i class="fas fa-heart text-danger"></i>
                            {{ __($person->social_status) }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Area Responsible --}}
                    <td>
                        @if($person->areaResponsible)
                            <i class="fas fa-user-tie text-primary"></i>
                            {{ $person->areaResponsible->name }}
                        @else
                            <span class="text-muted">@lang('people.messages.not_assigned')</span>
                        @endif
                    </td>

                    {{-- Block --}}
                    <td>
                        @if($person->block)
                            <i class="fas fa-users text-success"></i>
                            {{ $person->block->name }}
                        @else
                            <span class="text-muted">@lang('people.messages.not_assigned')</span>
                        @endif
                    </td>

                    {{-- Has Condition --}}
                    <td class="text-center">
                        @if($person->has_condition == 1)
                            <span class="badge badge-warning">
                                <i class="fas fa-check"></i> @lang('people.condition.yes')
                            </span>
                        @elseif($person->has_condition == 0)
                            <span class="badge badge-secondary">
                                <i class="fas fa-times"></i> @lang('people.condition.no')
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Relatives Count --}}
                    <td class="text-center">
                        @if($person->relatives_count)
                            <span class="badge badge-info badge-pill">
                                <i class="fas fa-users"></i> {{ $person->relatives_count }}
                            </span>
                        @else
                            <span class="text-muted">0</span>
                        @endif
                    </td>

                    {{-- Actions Dropdown --}}
                    <td style="width: 160px" class="text-center">
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

                                    {{-- حذف --}}
                                    @php
                                        $deleteMsg = __('people.dialogs.delete_confirm');
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
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($people->hasPages())
            @slot('footer')
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        @lang('people.pagination_results', [
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
