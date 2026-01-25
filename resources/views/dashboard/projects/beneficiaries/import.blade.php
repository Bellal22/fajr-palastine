<x-layout :title="'استيراد المستفيدين - ' . $project->name" :breadcrumbs="['dashboard.projects.beneficiaries.import', $project]">

    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title', 'رفع ملف Excel')

                <form action="{{ route('dashboard.projects.beneficiaries.import.store', $project) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="alert alert-info">
                        <strong>تنسيق الملف المطلوب (بالترتيب):</strong>
                        <ul class="mb-0">
                            <li>العمود الأول: رقم الهوية (أو مسلسل ثم رقم الهوية)</li>
                            <li>العمود الثاني: الاسم الرباعي</li>
                            <li>العمود الخامس: الكمية</li>
                            <li>العمود السادس: حالة الاستلام (مستلم / غير مستلم)</li>
                            <li>العمود السابع: تاريخ التسليم</li>
                            <li>العمود الثامن: الملاحظات</li>
                        </ul>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sub_warehouse_id">المخزن الفرعي <span class="text-danger">*</span></label>
                                <select name="sub_warehouse_id"
                                        id="sub_warehouse_id"
                                        class="form-control @error('sub_warehouse_id') is-invalid @enderror"
                                        required>
                                    <option value="">-- اختر المخزن الفرعي --</option>
                                    @foreach(\App\Models\SubWarehouse::all() as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('sub_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sub_warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ignore_conflicts">تجاوز تعارض المشاريع <span class="text-danger">*</span></label>
                                <select name="ignore_conflicts" id="ignore_conflicts" class="form-control">
                                    <option value="0">لا (منع إضافة المستفيدين في حال وجود تعارض)</option>
                                    <option value="1">نعم (إسقاط التعارض وإضافة المستفيدين)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- القسم الأول: استيرا ملف Excel -->
                        <div class="col-md-6">
                            <div class="card card-outline card-primary h-100">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-file-excel mr-1"></i>
                                        الخيار 1: استيراد ملف Excel
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="file">ملف Excel</label>
                                        <input type="file"
                                               name="file"
                                               id="file"
                                               class="form-control @error('file') is-invalid @enderror"
                                               accept=".xlsx,.xls,.csv">
                                        @error('file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">الحقول المطلوبة في الملف: رقم الهوية.</small>
                                    </div>

                                    <div class="alert alert-light border">
                                        <small>
                                            <i class="fas fa-info-circle"></i>
                                            عند استخدام ملف Excel، سيتم قراءة الكمية، الحالة، وتاريخ التسليم من الملف.
                                            <br>
                                            يمكنك تحديد "تاريخ تسليم موحد" في الأسفل لتجاوز تاريخ الملف.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- القسم الثاني: الإدخال اليدوي -->
                        <div class="col-md-6">
                            <div class="card card-outline card-success h-100">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-keyboard mr-1"></i>
                                        الخيار 2: الإدخال اليدوي
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="id_nums">أرقام الهويات</label>
                                        <textarea name="id_nums"
                                                  id="id_nums"
                                                  class="form-control @error('id_nums') is-invalid @enderror"
                                                  rows="4"
                                                  placeholder="أدخل أرقام الهويات هنا، كل رقم في سطر أو مفصولة بفواصل.">{{ old('id_nums') }}</textarea>
                                        @error('id_nums')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="status">حالة الاستلام</label>
                                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                            <option value="">-- اختر الحالة --</option>
                                            <option value="مستلم" {{ old('status') == 'مستلم' ? 'selected' : '' }}>مستلم</option>
                                            <option value="غير مستلم" {{ old('status') == 'غير مستلم' ? 'selected' : '' }}>غير مستلم</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="manual_notes">ملاحظات (اختياري)</label>
                                        <textarea name="manual_notes"
                                                  id="manual_notes"
                                                  class="form-control @error('manual_notes') is-invalid @enderror"
                                                  rows="2"
                                                  placeholder="ملاحظات تضاف للمستفيدين المدخلين يدوياً">{{ old('manual_notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body bg-light">
                                    <div class="form-group mb-0">
                                        <label for="delivery_date">تاريخ التسليم (موحد / مطلوب لليدوي)</label>
                                        <input type="date"
                                               name="delivery_date"
                                               id="delivery_date"
                                               class="form-control @error('delivery_date') is-invalid @enderror"
                                               value="{{ old('delivery_date') }}">
                                        @error('delivery_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-exclamation-circle"></i>
                                            هذا التاريخ <strong>مطلوب</strong> في حالة الإدخال اليدوي.
                                            وفي حالة رفع ملف Excel، سيتم اعتماده كبديل للتاريخ الموجود في الملف (إن وجد).
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload"></i> رفع واستيراد
                        </button>
                        <a href="{{ route('dashboard.projects.beneficiaries', $project) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </form>
            @endcomponent
        </div>

        <div class="col-md-4">
            @component('dashboard::components.box')
                @slot('title', 'ملاحظات هامة')

                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>تنبيه:</strong> يجب اختيار المخزن الفرعي قبل رفع الملف
                </div>

                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        الملف يجب أن يكون بصيغة Excel (.xlsx, .xls, .csv)
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        الحد الأقصى لحجم الملف: 10 ميجابايت
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        يجب أن يحتوي الملف على رقم الهوية لكل مستفيد
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        سيتم ربط جميع المستفيدين بالمخزن المحدد
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-info-circle text-info"></i>
                        المستفيدون الموجودون مسبقاً سيتم تحديث بياناتهم
                    </li>
                </ul>
            @endcomponent
        </div>
    </div>
</x-layout>
