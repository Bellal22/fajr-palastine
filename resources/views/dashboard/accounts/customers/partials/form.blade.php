@include('dashboard.errors')
{{ BsForm::number('id_num') }}

<div class="row">
    <div class="col-3">
        {{ BsForm::text('first_name') }}
    </div>
    <div class="col-3">
        {{ BsForm::text('father_name') }}
    </div>
    <div class="col-3">
        {{ BsForm::text('grandfather_name') }}
    </div>
    <div class="col-3">
        {{ BsForm::text('family_name') }}
    </div>
</div>

<div class="row">
    <div class="col-4">
        {{ BsForm::text('email') }}
    </div>
    <div class="col-4">
        {{ BsForm::text('phone') }}
    </div>
    <div class="col-4">
        {{ BsForm::text('dob') }}
    </div>
</div>

<div class="row">
    <div class="col-4">
        {{ BsForm::text('social_status') }}
    </div>
    <div class="col-4">
        {{ BsForm::text('city') }}
    </div>
    <div class="col-4">
        {{ BsForm::text('current_city') }}
    </div>
    <div class="col-4">
        {{ BsForm::text('neighborhood') }}
    </div>
    <div class="col-8">
        {{ BsForm::text('landmark') }}
    </div>
</div>

<div class="row">
    <div class="col-6">
        {{ BsForm::text('housing_type') }}
    </div>
    <div class="col-6">
        {{ BsForm::text('housing_damage_status') }}
    </div>
</div>

<div class="row">
    <div class="col-6">
        {{ BsForm::text('has_condition') }}
    </div>
    <div class="col-6">
        {{ BsForm::text('condition_description') }}
    </div>
</div>


{{ BsForm::password('password') }}
{{ BsForm::password('password_confirmation') }}

@isset($customer)
    {{ BsForm::image('avatar')->collection('avatars')->files($customer->getMediaResource('avatars')) }}
@else
    {{ BsForm::image('avatar')->collection('avatars') }}
@endisset
