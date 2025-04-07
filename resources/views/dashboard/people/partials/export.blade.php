<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('people.export_title')</title>
</head>
<body>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <th>#</th>
        <th>@lang('people.attributes.id_num')</th>
        <th>@lang('people.attributes.first_name')</th>
        <th>@lang('people.attributes.father_name')</th>
        <th>@lang('people.attributes.grandfather_name')</th>
        <th>@lang('people.attributes.family_name')</th>
        <th>@lang('people.attributes.gender')</th>
        <th>@lang('people.attributes.phone')</th>
        <th>@lang('people.attributes.dob')</th>
        <th>@lang('people.attributes.social_status')</th>
        <th>@lang('people.attributes.city')</th>
        <th>@lang('people.attributes.current_city')</th>
        <th>@lang('people.attributes.neighborhood')</th>
        <th>@lang('people.attributes.landmark')</th>
        <th>@lang('people.attributes.housing_type')</th>
        <th>@lang('people.attributes.housing_damage_status')</th>
        <th>@lang('people.attributes.employment_status')</th>
        <th>@lang('people.attributes.person_status')</th>
        <th>@lang('people.attributes.relatives_count')</th>
        <th>@lang('people.attributes.has_condition')</th>
        <th>@lang('people.attributes.condition_description')</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($persons as $index => $person)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $person->id_num }}</td>
            <td>{{ $person->first_name }}</td>
            <td>{{ $person->father_name }}</td>
            <td>{{ $person->grandfather_name }}</td>
            <td>{{ $person->family_name }}</td>
            <td>{{ $person->gender }}</td>
            <td>{{ $person->phone }}</td>
            <td>{{ $person->dob }}</td>
            <td>{{ $person->social_status }}</td>
            <td>{{ $person->city }}</td>
            <td>{{ $person->current_city }}</td>
            <td>{{ $person->neighborhood }}</td>
            <td>{{ $person->landmark }}</td>
            <td>{{ $person->housing_type }}</td>
            <td>{{ $person->housing_damage_status }}</td>
            <td>{{ $person->employment_status }}</td>
            <td>{{ $person->person_status }}</td>
            <td>{{ $person->relatives_count }}</td>
            <td>{{ $person->has_condition }}</td>
            <td>{{ $person->condition_description }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
