@include('dashboard.errors')

<div class="row">
    {{-- Basic Information Section --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-shield"></i> @lang('admins.sections.basic_info')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::text('name')
                    ->label(trans('admins.attributes.name'))
                    ->placeholder(trans('admins.placeholders.name'))
                    ->required() }}

                {{ BsForm::email('email')
                    ->label(trans('admins.attributes.email'))
                    ->placeholder(trans('admins.placeholders.email'))
                    ->required() }}

                {{ BsForm::text('phone')
                    ->label(trans('admins.attributes.phone'))
                    ->placeholder(trans('admins.placeholders.phone'))
                    ->required() }}

                {{-- Status Field --}}
                {{ BsForm::select('person_status')
                    ->label(trans('admins.attributes.person_status'))
                    ->options([
                        'فعال' => trans('admins.status.active'),
                        'غير فعال' => trans('admins.status.inactive'),
                    ])
                    ->required() }}
            </div>
        </div>
    </div>

    {{-- Security Section --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-gray text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-lock"></i> @lang('admins.sections.security')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::password('password')
                    ->label(trans('admins.attributes.password'))
                    ->placeholder(trans('admins.placeholders.password')) }}

                {{ BsForm::password('password_confirmation')
                    ->label(trans('admins.attributes.password_confirmation'))
                    ->placeholder(trans('admins.placeholders.password_confirmation')) }}

                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i>
                    @lang('admins.hints.password')
                </small>
            </div>
        </div>
    </div>
</div>

{{-- Avatar Section --}}
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-image"></i> @lang('admins.attributes.avatar')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                @isset($admin)
                    {{ BsForm::image('avatar')
                        ->label(trans('admins.attributes.avatar'))
                        ->collection('avatars')
                        ->files($admin->getMediaResource('avatars')) }}
                @else
                    {{ BsForm::image('avatar')
                        ->label(trans('admins.attributes.avatar'))
                        ->collection('avatars') }}
                @endisset
            </div>
            <div class="col-md-6">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-lightbulb"></i>
                    <strong>@lang('admins.hints.avatar_title')</strong>
                    <ul class="mb-0 mt-2">
                        <li>@lang('admins.hints.avatar_size')</li>
                        <li>@lang('admins.hints.avatar_format')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
