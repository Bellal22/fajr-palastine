<x-layout :title="trans('people.plural')" :breadcrumbs="['dashboard.people.index']">
    @include('dashboard.people.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('people.actions.list') ({{ $people->total() }})
        @endslot

        {{-- <a href="{{ route('people.export', request()->all()) }}" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel"></i> تصدير حسب الفلاتر
        </a> --}}

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.people.partials.actions.search')
                </div>
            </div>
          </th>
        </tr>
        <tr>
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
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($people as $person)
            <tr>
                <td>
                    <a href="{{ route('dashboard.people.show', $person) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $person->id_num }}
                    </a>
                </td>
                <td>{{ $person->first_name }} {{ $person->father_name }} {{ $person->grandfather_name }} {{ $person->family_name }}</td>
                <td>{{ $person->phone}}</td>
                <td>{{ __($person->social_status) }}</td>
                <td>{{ __($person->current_city) }}</td>
                <td>{{ $person->areaResponsible->name ?? 'لم يتم تحديده' }}</td>
                <td>{{ $person->block->name ?? 'لم يتم تحديده' }}</td>
                <td>{{ $person->has_condition == 1 ? 'نعم' : ($person->has_condition == 0 ? 'لا' : $person->has_condition) }}</td>
                <td>{{ $person->relatives_count }}</td>
                <td>{{ $person->family_members_count }}</td>

                <td style="width: 160px">
                    @include('dashboard.people.partials.actions.assignBlock')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('people.empty')</td>
            </tr>
        @endforelse

        @if($people->hasPages())
            @slot('footer')
                {{ $people->appends(request()->query())->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
