<x-layout :title="trans('people.plural')" :breadcrumbs="['dashboard.people.index']">
    @include('dashboard.people.partials.filter')

    @if(request()->hasAny(['search', 'id_num', 'block_id', 'area_responsible_id']))
        @component('dashboard::components.table-box')
            @slot('title')
                @lang('people.actions.list') ({{ $people->total() }})
            @endslot

            <thead>
            <tr>
                <th colspan="100">
                    <div class="d-flex">
                        <div class="ml-2 d-flex justify-content-between flex-grow-1">
                            @include('dashboard.people.partials.actions.search')

                            @if (auth()->user()?->isAdmin())
                                <x-check-all-export
                                    type="{{ \App\Models\Person::class }}"
                                    :resource="trans('people.plural')">
                                </x-check-all-export>

                                <a href="{{ route('dashboard.people.export.selected', request()->all()) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fa-fw fas fa-file-excel"></i>
                                    @lang('تصدير الكل (تطبق نتائج البحث)')
                                </a>
                            @endif
                        </div>
                    </div>
                </th>
            </tr>
            <tr>
                <th style="width: 30px;" class="text-center">
                    <x-check-all></x-check-all>
                </th>
                <th>@lang('people.attributes.id_num')</th>
                <th>@lang('people.attributes.name')</th>
                <th>@lang('people.attributes.phone')</th>
                <th>@lang('people.attributes.social_status')</th>
                <th>@lang('people.attributes.city')</th>
                <th>@lang('people.attributes.area_responsible')</th>
                <th>@lang('people.attributes.block')</th>
                <th>@lang('people.attributes.has_condition')</th>
                <th>@lang('people.attributes.relatives_count')</th>
                <th>@lang('people.attributes.relatives_count')(المسجل)</th>
            </tr>
            </thead>
            <tbody>
            @forelse($people as $person)
                <tr>
                    <td class="text-center">
                        <x-check-all-item :model="$person"></x-check-all-item>
                    </td>
                    <td>
                        <a href="{{ route('dashboard.people.show', $person) }}"
                           class="text-decoration-none text-ellipsis">
                            {{ $person->id_num }}
                        </a>
                    </td>
                    <td>{{ $person->first_name }} {{ $person->father_name }} {{ $person->grandfather_name }} {{ $person->family_name }}</td>
                    <td>{{ $person->phone }}</td>
                    <td>{{ __($person->social_status) }}</td>
                    <td>{{ __($person->current_city) }}</td>
                    <td>{{ $person->areaResponsible->name ?? 'لم يتم تحديده' }}</td>
                    <td>{{ $person->block->name ?? 'لم يتم تحديده' }}</td>
                    <td>{{ $person->has_condition == 1 ? 'نعم' : 'لا' }}</td>
                    <td>{{ $person->relatives_count }}</td>
                    <td>{{ $person->family_members_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="100" class="text-center">@lang('people.empty')</td>
                </tr>
            @endforelse
            </tbody>

            @if($people->hasPages())
                @slot('footer')
                    {{ $people->appends(request()->query())->links() }}
                @endslot
            @endif
        @endcomponent
    @else
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-search fa-2x mb-3"></i>
            <h4>@lang('people.messages.search_placeholder')</h4>
            <p class="mb-0">@lang('people.messages.empty_search')</p>
        </div>
    @endif
</x-layout>
