<x-layout :title="trans('complaint.trashed')" :breadcrumbs="['dashboard.complaint.trashed']">
    @include('dashboard.complaint.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('complaint.actions.list') ({{ $complaint->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\Complaint::class }}"
                    :resource="trans('complaint.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\Complaint::class }}"
                    :resource="trans('complaint.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('complaint.attributes.name')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($complaint as $complaint)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$complaint"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.complaint.trashed.show', $complaint) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $complaint->complaint_title	 }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.complaint.partials.actions.show')
                    @include('dashboard.complaint.partials.actions.restore')
                    @include('dashboard.complaint.partials.actions.forceDelete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('complaint.empty')</td>
            </tr>
        @endforelse

        @if($complaint->hasPages())
            @slot('footer')
                {{ $complaint->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
