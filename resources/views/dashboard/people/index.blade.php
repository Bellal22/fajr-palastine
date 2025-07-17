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
                <x-check-all-delete
                        type="{{ \App\Models\Person::class }}"
                        :resource="trans('people.plural')"></x-check-all-delete>


                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.people.partials.actions.create')
                    @include('dashboard.people.partials.actions.trashed')

                    <a href="{{ route('dashboard.people.export.selected', request()->all()) }}" class="btn btn-outline-success btn-sm">
                        <i class="fa-fw fas fa-file-excel"></i>
                        @lang('تصدير الكل (تطبق نتائج البحث)')
                    </a>
                    <x-check-all-export
                        type="{{ \App\Models\Person::class }}"
                        :resource="trans('people.plural')">
                    </x-check-all-export>
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('people.attributes.id_num')</th>
            <th>@lang('people.attributes.first_name')</th>
            <th>@lang('people.attributes.father_name')</th>
            <th>@lang('people.attributes.grandfather_name')</th>
            <th>@lang('people.attributes.family_name')</th>
            <th>@lang('people.attributes.dob')</th>
            <th>@lang('people.attributes.social_status')</th>
            <th>@lang('people.attributes.city')</th>
            <th>@lang('people.attributes.phone')</th>
            <th>@lang('people.attributes.relatives_count')</th>
            <th>@lang('people.attributes.relatives_count')(المسجل)</th>
            <th style="width: 160px">...</th>
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
                <td>{{ $person->first_name }}</td>
                <td>{{ $person->father_name }}</td>
                <td>{{ $person->grandfather_name }}</td>
                <td>{{ $person->family_name }}</td>
                <td>{{ $person->dob ? $person->dob->toDateString() : 'N/A' }}</td>
                <td>{{ __($person->social_status) }}</td>
                <td>{{ __($person->current_city) }}</td>
                <td>{{ $person->phone }}</td>
                <td>{{ $person->relatives_count }}</td>
                <td>{{ $person->family_members_count }}</td>

                <td style="width: 160px">
                    {{-- @include('dashboard.people.partials.actions.family') --}}
                    @include('dashboard.people.partials.actions.show')
                    @include('dashboard.people.partials.actions.edit')
                    @include('dashboard.people.partials.actions.delete')
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
