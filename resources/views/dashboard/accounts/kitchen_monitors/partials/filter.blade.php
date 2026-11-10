{{ BsForm::resource('kitchen_monitors')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title')
        <i class="fas fa-search"></i> تصفية البحث
    @endslot

    <div class="row">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-user"></i> الاسم
                </label>
                {{ BsForm::text('name')
                    ->placeholder('ابحث بالاسم...')
                    ->value(request('name'))
                    ->label(false) }}
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-envelope"></i> البريد الإلكتروني
                </label>
                {{ BsForm::email('email')
                    ->placeholder('ابحث بالبريد الإلكتروني...')
                    ->value(request('email'))
                    ->label(false) }}
            </div>
        </div>

        <div class="col-lg-2 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-phone"></i> الهاتف
                </label>
                {{ BsForm::text('phone')
                    ->placeholder('ابحث بالهاتف...')
                    ->value(request('phone'))
                    ->label(false) }}
            </div>
        </div>

        <div class="col-lg-2 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                {{ BsForm::select('person_status')
                    ->placeholder('اختر الحالة')
                    ->options([
                        '' => 'الكل',
                        'فعال' => 'فعال',
                        'غير فعال' => 'غير فعال',
                    ])
                    ->value(request('person_status'))
                    ->label(false) }}
            </div>
        </div>

        <div class="col-lg-2 col-md-6 mb-3">
            <div class="form-group">
                <label>
                    <i class="fas fa-list-ol"></i> عدد النتائج
                </label>
                {{ BsForm::number('perPage')
                    ->value(request('perPage', 15))
                    ->min(1)
                    ->max(100)
                    ->label(false) }}
            </div>
        </div>
    </div>

    @slot('footer')
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    تصفية
                </button>

                <a href="{{ url()->current() }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-eraser"></i>
                    إعادة تعيين
                </a>
            </div>
        </div>
    @endslot
@endcomponent
{{ BsForm::close() }}
