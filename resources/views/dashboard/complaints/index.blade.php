<x-layout :title="trans('complaints.plural')" :breadcrumbs="['dashboard.complaints.index']">
    @include('dashboard.complaints.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('complaints.actions.list') ({{ $complaints->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\Complaint::class }}"
                        :resource="trans('complaints.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.complaints.partials.actions.create')
                    @include('dashboard.complaints.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            {{-- <th>@lang('complaints.attributes.id')</th> --}}
            <th>@lang('complaints.attributes.id_num')</th>
            <th>@lang('complaints.attributes.complaint_title')</th>
            <th>@lang('complaints.attributes.complaint_text')</th>
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
                    <a href="{{ route('dashboard.complaints.show', $complaint) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $complaint->id_num }}
                    </a>
                </td>
                {{-- <td>{{ $complaint->id_num}}</td> --}}
                <td>{{ $complaint->complaint_title	}}</td>
                <td>{{ $complaint->complaint_text }}</td>

                <td style="width: 160px">
                    @include('dashboard.complaints.partials.actions.show')
                    @include('dashboard.complaints.partials.actions.edit')
                    @include('dashboard.complaints.partials.actions.delete')
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
