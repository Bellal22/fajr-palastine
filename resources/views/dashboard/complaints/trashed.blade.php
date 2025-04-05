<x-layout :title="trans('complaints.trashed')" :breadcrumbs="['dashboard.complaints.trashed']">
    @include('dashboard.complaints.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('complaints.actions.list') ({{ $complaints->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\Complaint::class }}"
                    :resource="trans('complaints.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\Complaint::class }}"
                    :resource="trans('complaints.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('complaints.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($complaints as $complaint)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$complaint"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.complaints.trashed.show', $complaint) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $complaint->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.complaints.partials.actions.show')
                    @include('dashboard.complaints.partials.actions.restore')
                    @include('dashboard.complaints.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('complaints.empty')</td>
            </tr>
        @endforelse

        @if($complaints->hasPages())
            @slot('footer')
                {{ $complaints->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
