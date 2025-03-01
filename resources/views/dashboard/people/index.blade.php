<x-layout :title="trans('people.plural')" :breadcrumbs="['dashboard.people.index']">
    @include('dashboard.people.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('people.actions.list') ({{ $people->total() }})
        @endslot

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
            <th>@lang('people.attributes.has_condition')</th>
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
                <td>{{ $person->dob }}</td>
                <td>{{ $person->social_status }}</td>
                <td>{{ $person->city }}</td>
                <td>{{ $person->has_condition }}</td>

                <td style="width: 160px">
                    @include('dashboard.people.partials.actions.family')
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
                {{ $people->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
