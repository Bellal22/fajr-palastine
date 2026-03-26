<x-layout :title="'الأشخاص غير المزامنين'" :breadcrumbs="['dashboard.people.unsynced']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.people.partials.filter')
    </div>

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-sync-alt"></i> الأشخاص المعتمدين غير المزامنين
            <span class="badge badge-primary badge-pill">{{ count_formatted($people->total()) }}</span>
        @endslot

        <thead>
            {{-- Actions Row --}}
            <tr>
                <th colspan="100">
                    <div class="d-flex align-items-center justify-content-between">
                        {{-- Left Actions --}}
                        <div class="d-flex align-items-center">
                            @if (auth()->user()?->isAdmin())
                                <div class="mr-2">
                                    <x-check-all-delete
                                        :type="\App\Models\Person::class"
                                        :resource="trans('people.plural')">
                                    </x-check-all-delete>
                                </div>

                                <div class="mr-2">
                                    <x-check-all-api
                                        :type="\App\Models\Person::class"
                                        :resource="trans('people.plural')">
                                    </x-check-all-api>
                                </div>

                                <div class="mr-2">
                                    <x-check-all-freeze></x-check-all-freeze>
                                </div>
                            @endif
                        </div>

                        {{-- Right Actions --}}
                        <div class="d-flex align-items-center">
                            @if (auth()->user()?->isAdmin())
                                <div class="btn-group" role="group">
                                    <a href="{{ route('dashboard.people.export.view', array_merge(request()->all(), ['unsynced_only' => 1])) }}"
                                       class="btn btn-outline-success btn-sm">
                                        <i class="fa-fw fas fa-file-excel"></i>
                                        تصدير غير المزامنين
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </th>
            </tr>

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
                     <i class="fas fa-clock"></i> تاريخ التحديث
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
                                @if($person->is_frozen)
                                    <span class="badge badge-danger ml-1" title="مجمّد">
                                        <i class="fas fa-snowflake"></i>
                                    </span>
                                @endif
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

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

                    {{-- Updated At --}}
                    <td>
                        <span class="badge badge-info shadow-none">
                            {{ $person->updated_at->diffForHumans() }}
                        </span>
                        <small class="d-block text-muted">{{ $person->updated_at->format('Y-m-d H:i') }}</small>
                    </td>

                    {{-- Actions --}}
                    <td class="text-center">
                        <a href="{{ route('dashboard.people.show', $person) }}" class="btn btn-sm btn-outline-primary shadow-sm">
                            <i class="fas fa-eye"></i> @lang('people.actions.show')
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="100" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-sync-alt fa-3x mb-3 d-block"></i>
                            <h5>لا توجد بيانات غیر مزامنة معتمدة حالياً</h5>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- Pagination --}}
        @if($people->hasPages())
            @slot('footer')
                {{ $people->appends(request()->query())->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
