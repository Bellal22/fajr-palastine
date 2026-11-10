@include('dashboard.errors')

<div class="row">
    {{-- Basic Information Section --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-circle"></i> المعلومات الأساسية
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::text('name')->required()->label('الاسم') }}
                {{ BsForm::text('email')->required()->label('البريد الإلكتروني') }}
                {{ BsForm::text('phone')->label('الهاتف') }}

                {{-- Status Field --}}
                {{ BsForm::select('person_status')
                    ->label('الحالة')
                    ->options([
                        'فعال' => 'فعال',
                        'غير فعال' => 'غير فعال',
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
                    <i class="fas fa-lock"></i> الحماية وكلمة المرور
                </h5>
            </div>
            <div class="card-body">
                {{ BsForm::password('password')->label('كلمة المرور') }}
                {{ BsForm::password('password_confirmation')->label('تأكيد كلمة المرور') }}

                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i>
                    اتركه فارغاً إذا كنت لا تريد تغيير كلمة المرور.
                </small>
            </div>
        </div>
    </div>
</div>

{{-- Permissions Section (Read Only for Kitchen Monitor) --}}
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-shield-alt"></i> الصلاحيات الممنوحة (تلقائية)
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 col-sm-6 mb-2">
                <span class="badge badge-pill badge-primary p-2 w-100">
                    <i class="fas fa-id-card"></i> إدارة البطاقات (البطاقات)
                </span>
            </div>
            <div class="col-md-4 col-sm-6 mb-2">
                <span class="badge badge-pill badge-primary p-2 w-100">
                    <i class="fas fa-campground"></i> إدارة المشاريع (المخيمات)
                </span>
            </div>
            <div class="col-md-4 col-sm-6 mb-2">
                <span class="badge badge-pill badge-primary p-2 w-100">
                    <i class="fas fa-utensils"></i> متابعة شكاوى المطبخ
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Avatar Section --}}
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-image"></i> الصورة الشخصية
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
                    <strong>نصيحة للصورة الشخصية</strong>
                    <ul class="mb-0 mt-2">
                        <li>يفضل أن تكون الصورة مربعة ونظيفة.</li>
                        <li>الصيغ المدعومة: png, jpg, jpeg.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
