@include('dashboard.errors')

<div class="row">
    {{-- Basic Information Section --}}
    <div class="col-md-6">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-id-card"></i> @lang('distributors.sections.basic_info')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::text('name')->required() }}
                {{ BsForm::text('email')->required() }}
                {{ BsForm::text('phone') }}
            </div>
        </div>
    </div>

    {{-- Security Section --}}
    <div class="col-md-6">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-key"></i> @lang('distributors.sections.security')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::password('password') }}
                {{ BsForm::password('password_confirmation') }}

                <small class="form-text text-muted mt-2">
                    <i class="fas fa-info-circle"></i>
                    @lang('distributors.hints.password')
                </small>
            </div>
        </div>
    </div>
</div>

{{-- Avatar Section --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-camera"></i> @lang('distributors.attributes.avatar')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 text-center border-right">
                @isset($distributor)
                    {{ BsForm::image('avatar')->collection('avatars')->files($distributor->getMediaResource('avatars')) }}
                @else
                    {{ BsForm::image('avatar')->collection('avatars') }}
                @endisset
            </div>
            <div class="col-md-6">
                <div class="p-3 border rounded bg-light">
                    <h6 class="text-info mb-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>@lang('distributors.hints.avatar_title')</strong>
                    </h6>
                    <ul class="mb-0 list-unstyled text-muted">
                        <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> @lang('distributors.hints.avatar_size')</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i> @lang('distributors.hints.avatar_format')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
