<x-layout :title="$person->name" :breadcrumbs="['dashboard.people.show', $person]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('people.attributes.id_num')</th>
                        <td>{{ $person->id_num }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.first_name')</th>
                        <td>{{ $person->first_name }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.father_name')</th>
                        <td>{{ $person->father_name }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.grandfather_name')</th>
                        <td>{{ $person->grandfather_name }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.family_name')</th>
                        <td>{{ $person->family_name }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.passkey')</th>
                        <td>{{ $person->passkey }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.gender')</th>
                        <td>{{ $person->gender }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.dob')</th>
                        <td>{{ $person->dob }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.social_status')</th>
                        <td>{{ __($person->social_status) }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.relatives_count')</th>
                        <td>{{ $person->relatives_count }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.city')</th>
                        <td>{{ __($person->city) }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.current_city')</th>
                        <td>{{ __($person->current_city) }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.neighborhood')</th>
                        <td>{{ __($person->neighborhood) }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.landmark')</th>
                        <td>{{ $person->landmark }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.housing_type')</th>
                        <td>{{ __($person->housing_type) }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.housing_damage_status')</th>
                        <td>{{ __($person->housing_damage_status) }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.has_condition')</th>
                        <td>{{ $person->has_condition == 1 ? 'نعم' : ($person->has_condition == 0 ? 'لا' : $person->has_condition) }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.condition_description')</th>
                        <td>{{ $person->condition_description ?? 'لا يوجد' }}</td>
                    </tr>

                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.people.partials.actions.edit')
                    @include('dashboard.people.partials.actions.delete')
                    @include('dashboard.people.partials.actions.restore')
                    @include('dashboard.people.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>

    <div>
        {{$people = \App\Models\Person::where('relative_id',$person->id_num)->paginate()}}

        @if(count($people) > 0)

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
                <th>@lang('people.attributes.relationship')</th>
                <th>@lang('people.attributes.has_condition')</th>
                <th>@lang('people.attributes.condition_description')</th>
                <th style="width: 160px">...</th>
            </tr>
            </thead>
            <tbody>
            @forelse($person->familyMembers as $person)
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
                    <td>{{ __($person->relationship) }}</td>
                    <td>{{ $person->has_condition == 1 ? 'نعم' : ($person->has_condition == 0 ? 'لا' : $person->has_condition) }}</td>
                    <td>{{ $person->condition_description ?? 'لا يوجد' }}</td>

                    <td style="width: 160px">
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
        @endif


    </div>

    <div>
        {{$complaints = \App\Models\Complaint::where('id_num',$person->id_num)->paginate()}}

{{--        @if(count($complaints) > 0)--}}

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
{{--                                    @include('dashboard.complaints.partials.actions.create')--}}
                                    @include('dashboard.complaints.partials.actions.trashed')
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 30px;" class="text-center">
                            <x-check-all></x-check-all>
                        </th>
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

{{--        @endif--}}
    </div>
</x-layout>

