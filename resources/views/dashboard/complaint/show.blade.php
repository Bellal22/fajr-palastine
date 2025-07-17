<x-layout :title="$complaint->name" :breadcrumbs="['dashboard.complaint.show', $complaint]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('complaint.attributes.id_num')</th>
                        <td>{{ $complaint->id_num }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('complaint.attributes.complaint_title')</th>
                        <td>{{ $complaint->complaint_title}}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('complaint.attributes.complaint_text')</th>
                        <td>{{ $complaint->complaint_text}}</td>
                    </tr>complaint_title
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.complaint.partials.actions.edit')
                    @include('dashboard.complaint.partials.actions.delete')
                    @include('dashboard.complaint.partials.actions.restore')
                    @include('dashboard.complaint.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>

    <div>
        {{$complaint = \App\Models\complaint::where('relative_id',$complaint->id_num)->paginate()}}

        @if(count($complaint) > 0)

        @component('dashboard::components.table-box')
            @slot('title')
                @lang('complaint.actions.list') ({{ $complaint->total() }})
            @endslot

            <thead>
            <tr>
                <th colspan="100">
                    <div class="d-flex">
                        <x-check-all-delete
                            type="{{ \App\Models\complaint::class }}"
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
                <th>@lang('complaint.attributes.first_name')</th>
                <th>@lang('complaint.attributes.father_name')</th>
                <th>@lang('complaint.attributes.grandfather_name')</th>
                <th>@lang('complaint.attributes.family_name')</th>
                <th>@lang('complaint.attributes.dob')</th>
                <th>@lang('complaint.attributes.social_status')</th>
                <th>@lang('complaint.attributes.city')</th>
                <th>@lang('complaint.attributes.has_condition')</th>
                <th style="width: 160px">...</th>
            </tr>
            </thead>
            <tbody>
            @forelse($complaint->familyMembers as $complaint)
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
                    <td>{{ $complaint->first_name }}</td>
                    <td>{{ $complaint->father_name }}</td>
                    <td>{{ $complaint->grandfather_name }}</td>
                    <td>{{ $complaint->family_name }}</td>
                    <td>{{ $complaint->dob }}</td>
                    <td>{{ $complaint->social_status }}</td>
                    <td>{{ $complaint->city }}</td>
                    <td>{{ $complaint->has_condition }}</td>

                    <td style="width: 160px">
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
        @endif


    </div>
</x-layout>

