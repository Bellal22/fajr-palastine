<x-layout :title="trans('people.plural')" :breadcrumbs="['dashboard.people.index']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.people.partials.filter')
    </div>

    @if(request()->hasAny(['search', 'id_num', 'block_id', 'area_responsible_id', 'first_name', 'father_name', 'grandfather_name', 'family_name', 'phone', 'gender', 'social_status', 'current_city', 'neighborhood', 'has_condition']))
        @component('dashboard::components.table-box')
            @slot('title')
                <i class="fas fa-users"></i> @lang('people.actions.list')
                @php
                    $totalIncomplete = (isset($incompletePeople) ? $incompletePeople->count() : 0);
                    $totalResults = $people->total() + $totalIncomplete;
                @endphp
                <span class="badge badge-primary badge-pill">{{ count_formatted($totalResults) }}</span>
            @endslot

            <thead>
                {{-- Actions Row --}}
                <tr>
                    <th colspan="100">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center">
                                @include('dashboard.people.partials.actions.search')
                            </div>

                            @if (auth()->user()?->isAdmin())
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="mr-2">
                                        <x-check-all-export type="{{ \App\Models\Person::class }}" :resource="trans('people.plural')"></x-check-all-export>
                                    </div>

                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dashboard.people.export.view', array_merge(request()->all(), ['export_source' => 'search'])) }}" class="btn btn-outline-success btn-sm"><i class="fa-fw fas fa-file-excel"></i> @lang('تصدير الكل')</a>
                                        <a href="{{ route('dashboard.people.export.exportChildren', array_merge(request()->all(), ['export_source' => 'search'])) }}" class="btn btn-outline-success btn-sm"><i class="fa-fw fas fa-child"></i> @lang('تصدير الأطفال')</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </th>
                </tr>

                {{-- Table Headers --}}
                <tr class="bg-light">
                    <th style="width: 30px;" class="text-center"><x-check-all></x-check-all></th>
                    <th><i class="fas fa-id-card"></i> @lang('people.attributes.id_num')</th>
                    <th><i class="fas fa-user"></i> @lang('people.attributes.name')</th>
                    <th><i class="fas fa-phone"></i> @lang('people.attributes.phone')</th>
                    <th><i class="fas fa-heart"></i> @lang('people.attributes.social_status')</th>
                    <th><i class="fas fa-map-marker-alt"></i> @lang('people.attributes.city')</th>
                    <th><i class="fas fa-user-tie"></i> @lang('people.attributes.area_responsible')</th>
                    <th><i class="fas fa-users"></i> @lang('people.attributes.block')</th>
                    <th class="text-center"><i class="fas fa-notes-medical"></i> @lang('people.attributes.has_condition')</th>
                    <th class="text-center"><i class="fas fa-users"></i> @lang('people.attributes.relatives_count')</th>
                    <th class="text-center">@lang('الخيارات')</th>
                </tr>
            </thead>

            <tbody>
                @php $rowCount = 0; @endphp
                
                {{-- 1. Main Table Results --}}
                @foreach($people as $person)
                    @php $rowCount++; @endphp
                    <tr class="align-middle">
                        <td class="text-center"><x-check-all-item :model="$person"></x-check-all-item></td>
                        <td>
                            <a href="{{ route('dashboard.people.show', $person) }}" class="text-decoration-none text-primary font-weight-bold">
                                {{ $person->id_num }}
                            </a>
                        </td>
                        <td class="font-weight-bold">{{ $person->getFullName() }}</td>
                        <td>
                            @if($person->phone)
                                <a href="tel:{{ $person->phone }}" class="text-decoration-none text-success"><i class="fas fa-phone"></i> {{ $person->phone }}</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $person->social_status ? trans('people.social_status.' . $person->social_status) : '-' }}</td>
                        <td>{{ $person->current_city ? trans('people.cities.' . $person->current_city) : '-' }}</td>
                        <td>{{ ($person->areaResponsible) ? $person->areaResponsible->name : 'لم يتم تحديده' }}</td>
                        <td>{{ ($person->block) ? $person->block->name : 'لم يتم تحديده' }}</td>
                        <td class="text-center">
                            @if($person->has_condition) <span class="badge badge-warning">نعم</span> @else <span class="badge badge-secondary">لا</span> @endif
                        </td>
                        <td class="text-center"><span class="badge badge-info badge-pill">{{ $person->relatives_count }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('dashboard.people.show', $person->id) }}" class="btn btn-xs btn-primary shadow-sm"><i class="fas fa-eye"></i> عرض</a>
                        </td>
                    </tr>
                @endforeach

                {{-- 2. Incomplete People Results (Merged) --}}
                @if(isset($incompletePeople) && $incompletePeople->count() > 0)
                    @foreach($incompletePeople as $scrap)
                        @php $rowCount++; @endphp
                        <tr class="align-middle">
                            <td class="text-center text-muted small">{{ $rowCount }}</td>
                            <td>
                                <a href="{{ route('dashboard.incomplete_people.show', $scrap->id) }}" class="text-decoration-none text-primary font-weight-bold">
                                    {{ $scrap->id_num }}
                                </a>
                                <span class="badge badge-warning ml-1" style="font-size: 10px;">@lang('سجل مؤقت غير مكتمل')</span>
                            </td>
                            <td class="font-weight-bold">{{ $scrap->getFullName() }}</td>
                            <td>
                                @if($scrap->phone)
                                    <a href="tel:{{ $scrap->phone }}" class="text-decoration-none text-success"><i class="fas fa-phone"></i> {{ $scrap->phone }}</a>
                                @else <span class="text-muted">-</span> @endif
                            </td>
                            <td>{{ $scrap->social_status ? __($scrap->social_status) : '-' }}</td>
                            <td>{{ $scrap->current_city ? __($scrap->current_city) : ($scrap->neighborhood ?? '-') }}</td>
                            <td>{{ ($scrap->areaResponsible) ? $scrap->areaResponsible->name : 'لم يتم تحديده' }}</td>
                            <td>{{ ($scrap->block) ? $scrap->block->name : 'لم يتم تحديده' }}</td>
                            <td class="text-center">
                                @if($scrap->has_condition) <span class="badge badge-warning">نعم</span> @else <span class="badge badge-secondary">لا</span> @endif
                            </td>
                            <td class="text-center"><span class="badge badge-info badge-pill">{{ $scrap->relatives_count ?? 0 }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('dashboard.incomplete_people.show', $scrap->id) }}" class="btn btn-xs btn-outline-info shadow-sm"><i class="fas fa-eye"></i> عرض</a>
                            </td>
                        </tr>
                    @endforeach
                @endif

                @if($rowCount == 0)
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3 d-block"></i>
                            <h5>@lang('people.empty')</h5>
                        </td>
                    </tr>
                @endif
            </tbody>

            @if($people->hasPages())
                @slot('footer')
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">عرض {{ $people->firstItem() }} - {{ $people->lastItem() }} من {{ $people->total() }}</div>
                        {{ $people->appends(request()->query())->links() }}
                    </div>
                @endslot
            @endif
        @endcomponent
    @else
        {{-- Empty Search State --}}
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-search fa-3x mb-3 d-block text-info"></i>
            <h4>@lang('people.messages.search_placeholder')</h4>
            <p class="mb-0 text-muted">@lang('people.messages.empty_search')</p>
        </div>
    @endif
</x-layout>
