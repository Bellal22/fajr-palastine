<x-layout :title="trans('people.trashed')" :breadcrumbs="['dashboard.people.trashed']">
    @include('dashboard.people.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('people.actions.list') ({{ $people->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\Person::class }}"
                    :resource="trans('people.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\Person::class }}"
                    :resource="trans('people.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('people.attributes.name')</th>
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
                    <a href="{{ route('dashboard.people.trashed.show', $person) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $person->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.people.partials.actions.show')
                    @include('dashboard.people.partials.actions.restore')
                    @include('dashboard.people.partials.actions.forceDelete')
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
