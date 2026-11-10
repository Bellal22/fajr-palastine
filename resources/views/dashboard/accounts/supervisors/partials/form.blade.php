@include('dashboard.errors')

<div class="row">
    {{-- Basic Information Section --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-circle"></i> @lang('supervisors.sections.basic_info')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::text('name')->required() }}
                {{ BsForm::text('email')->required() }}
                {{ BsForm::text('phone') }}

                {{-- Status Field --}}
                {{ BsForm::select('person_status')
                    ->label(trans('supervisors.attributes.person_status'))
                    ->options([
                        'فعال' => trans('supervisors.status.active'),
                        'غير فعال' => trans('supervisors.status.inactive'),
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
                    <i class="fas fa-lock"></i> @lang('supervisors.sections.security')
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::password('password') }}
                {{ BsForm::password('password_confirmation') }}

                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i>
                    @lang('supervisors.hints.password')
                </small>
            </div>
        </div>
    </div>
</div>

{{-- Permissions Section --}}
@if(auth()->user()->isAdmin())
    @php
        $dynamicCustomProjects = collect();
        try {
            \App\Models\CustomProject::ensureTableExists();
            $dynamicCustomProjects = \App\Models\CustomProject::where('slug', '!=', 'rrm')->orderBy('id', 'asc')->get();
        } catch (\Throwable $e) {
            $dynamicCustomProjects = collect();
        }
    @endphp
    <div class="card mb-4" id="permissions-card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-shield-alt"></i> @lang('permissions.plural')
            </h5>
            <button type="button" class="btn btn-sm btn-light text-dark font-weight-bold" id="toggle-all-permissions-btn">
                <i class="fas fa-check-double ml-1" id="toggle-all-permissions-icon"></i>
                <span id="toggle-all-permissions-text">@lang('permissions.select_all')</span>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach(config('permission.supported') as $permission)
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="custom-control custom-checkbox">
                            {{ BsForm::checkbox('permissions[]')
                                    ->value($permission)
                                    ->label(trans(str_replace('manage.', '', $permission.'.permission')))
                                    ->checked(isset($supervisor) && $supervisor->hasPermission($permission)) }}
                        </div>
                    </div>
                @endforeach

                @foreach($dynamicCustomProjects as $cp)
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="custom-control custom-checkbox">
                            {{ BsForm::checkbox('permissions[]')
                                    ->value($cp->permission_name)
                                    ->label('إدارة ' . $cp->name . ' (' . $cp->code_prefix . ')')
                                    ->checked(isset($supervisor) && $supervisor->hasPermission($cp->permission_name)) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var card = document.getElementById('permissions-card');
            var btn = document.getElementById('toggle-all-permissions-btn');
            var btnText = document.getElementById('toggle-all-permissions-text');
            var btnIcon = document.getElementById('toggle-all-permissions-icon');
            if (!card || !btn) return;

            var selectAllLabel = @json(trans('permissions.select_all'));
            var deselectAllLabel = @json(trans('permissions.deselect_all'));

            function getCheckboxes() {
                return Array.from(card.querySelectorAll('input[type="checkbox"][name="permissions[]"]'));
            }

            function updateButtonState() {
                var checkboxes = getCheckboxes();
                var allChecked = checkboxes.length > 0 && checkboxes.every(function (cb) { return cb.checked; });
                if (allChecked) {
                    btnText.textContent = deselectAllLabel;
                    btnIcon.className = 'fas fa-times-circle ml-1';
                } else {
                    btnText.textContent = selectAllLabel;
                    btnIcon.className = 'fas fa-check-double ml-1';
                }
            }

            btn.addEventListener('click', function () {
                var checkboxes = getCheckboxes();
                var allChecked = checkboxes.length > 0 && checkboxes.every(function (cb) { return cb.checked; });
                checkboxes.forEach(function (cb) {
                    cb.checked = !allChecked;
                    cb.dispatchEvent(new Event('change'));
                });
                updateButtonState();
            });

            getCheckboxes().forEach(function (cb) {
                cb.addEventListener('change', updateButtonState);
            });

            updateButtonState();
        });
    </script>
    @endpush
@endif

{{-- Avatar Section --}}
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-image"></i> @lang('supervisors.attributes.avatar')
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                @isset($supervisor)
                    {{ BsForm::image('avatar')->collection('avatars')->files($supervisor->getMediaResource('avatars')) }}
                @else
                    {{ BsForm::image('avatar')->collection('avatars') }}
                @endisset
            </div>
            <div class="col-md-6">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-lightbulb"></i>
                    <strong>@lang('supervisors.hints.avatar_title')</strong>
                    <ul class="mb-0 mt-2">
                        <li>@lang('supervisors.hints.avatar_size')</li>
                        <li>@lang('supervisors.hints.avatar_format')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
