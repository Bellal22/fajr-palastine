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
                        <th width="200">@lang('people.attributes.dob')</th>
                        <td>{{ $person->dob }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.social_status')</th>
                        <td>{{ $person->social_status }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.city')</th>
                        <td>{{ $person->city }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.current_city')</th>
                        <td>{{ $person->current_city }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.neighborhood')</th>
                        <td>{{ $person->neighborhood }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.landmark')</th>
                        <td>{{ $person->landmark }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.housing_type')</th>
                        <td>{{ $person->housing_type }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.housing_damage_status')</th>
                        <td>{{ $person->housing_damage_status }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.has_condition')</th>
                        <td>{{ $person->has_condition }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('people.attributes.condition_description')</th>
                        <td>{{ $person->condition_description }}</td>
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
</x-layout>
