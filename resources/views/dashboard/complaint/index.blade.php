<x-layout :title="trans('complaint.plural')" :breadcrumbs="['dashboard.complaint.index']">
    @include('dashboard.complaint.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('complaint.actions.list') ({{ $complaint->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\Complaint::class }}"
                        :resource="trans('complaint.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.complaint.partials.actions.create')
                    @include('dashboard.complaint.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('complaint.attributes.id_num')</th>
            <th>@lang('complaint.attributes.complaint_title')</th>
            <th>@lang('complaint.attributes.complaint_text')</th>
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
                    <a href="{{ route('dashboard.complaint.show', $complaint) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $complaint->id_num }}
                    </a>
                </td>
                <td>{{ $complaint->complaint_title	}}</td>
                <td>{{ $complaint->complaint_text }}</td>

                <td style="width: 160px">
                    {{-- @include('dashboard.complaint.partials.actions.family') --}}
                    @include('dashboard.complaint.partials.actions.show')
                    @include('dashboard.complaint.partials.actions.edit')
                    @include('dashboard.complaint.partials.actions.delete')
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
