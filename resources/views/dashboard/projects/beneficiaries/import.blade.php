<x-layout :title="'استيراد المستفيدين - ' . $project->name" :breadcrumbs="['dashboard.projects.beneficiaries.import', $project]">

    @if($project->is_cash)
        <div class="alert alert-success d-flex align-items-center justify-content-between mb-4 shadow-sm" style="border-right: 5px solid #28a745;">
            <div>
                <h5 class="mb-1 font-weight-bold">
                    <i class="fas fa-money-bill-wave ml-1"></i> استيراد مستفيدي كوبون نقدي: {{ $project->name }}
                </h5>
                <span class="text-dark">
                    <strong>طبيعة الكوبون:</strong> كوبون نقدي 
                    | <strong>قيمة الكوبون:</strong> 
                    @if($project->cash_amount_type == 'custom')
                        <span class="badge badge-info">مبالغ منفصلة / متغيرة لكل مستفيد</span>
                    @else
                        <span class="badge badge-success font-weight-bold" style="font-size: 14px;">{{ $project->formatted_cash_value }}</span>
                    @endif
                    | <strong>العملة:</strong> {{ $project->currency_label }}
                </span>
            </div>
            <div>
                <span class="badge badge-light border p-2 font-weight-bold text-success" style="font-size: 18px;">
                    {{ $project->currency_symbol }}
                </span>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title', 'رفع ملف Excel / إدخال المستفيدين')

                <form action="{{ route('dashboard.projects.beneficiaries.import.store', $project) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if($project->is_cash)
                        <div class="alert alert-info">
                            <strong><i class="fas fa-file-excel ml-1"></i> تنسيق ملف Excel للكوبون النقدي (بالترتيب أو بعناوين الأعمدة):</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>العمود 1:</strong> رقم الهوية (مطلوب - أو مسلسل ثم الهوية)</li>
                                <li><strong>العمود 2:</strong> اسم المستفيد (اختياري)</li>
                                <li><strong>العمود 3 (أو عمود "المبلغ"):</strong> مبلغ الكوبون (اختياري، إن تُرك فارغاً يُعتمد المبلغ المحدد أدناه أو بالمشروع)</li>
                                <li><strong>عمود "حالة الاستلام":</strong> (مستلم / غير مستلم)</li>
                                <li><strong>عمود "تاريخ التسليم":</strong> تاريخ التسليم (مثال: 2026-11-03)</li>
                                <li><strong>عمود "طريقة التسليم":</strong> (كاش / محفظة / بنك)</li>
                                <li><strong>عمود "رقم المحفظة / الحساب":</strong> تفاصيل الحساب أو المحفظة (اختياري)</li>
                                <li><strong>عمود "الملاحظات":</strong> ملاحظات إضافية</li>
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <strong><i class="fas fa-file-excel ml-1"></i> تنسيق الملف المطلوب (بالترتيب):</strong>
                            <ul class="mb-0 mt-2">
                                <li>العمود الأول: رقم الهوية (أو مسلسل ثم رقم الهوية)</li>
                                <li>العمود الثاني: الاسم الرباعي</li>
                                <li>العمود الخامس: الكمية</li>
                                <li>العمود السادس: حالة الاستلام (مستلم / غير مستلم)</li>
                                <li>العمود السابع: تاريخ التسليم</li>
                                <li>العمود الثامن: الملاحظات</li>
                            </ul>
                        </div>
                    @endif

                    @if($project->is_cash)
                        <div class="card card-outline card-success mb-4">
                            <div class="card-header py-2">
                                <h6 class="card-title font-weight-bold mb-0 text-success">
                                    <i class="fas fa-hand-holding-usd ml-1"></i> إعدادات التسليم النقدي
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label for="delivery_method">طريقة التسليم <span class="text-danger">*</span></label>
                                            <select name="delivery_method" id="delivery_method" class="form-control @error('delivery_method') is-invalid @enderror" required>
                                                <option value="كاش" {{ old('delivery_method', $project->default_delivery_method ?? 'كاش') == 'كاش' ? 'selected' : '' }}>كاش (نقداً باليد)</option>
                                                <option value="محفظة" {{ old('delivery_method', $project->default_delivery_method) == 'محفظة' ? 'selected' : '' }}>محفظة إلكترونية (Jawwal Pay / PalPay / إلخ)</option>
                                                <option value="بنك" {{ old('delivery_method', $project->default_delivery_method) == 'بنك' ? 'selected' : '' }}>حساب بنكي / تحويل</option>
                                            </select>
                                            @error('delivery_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label for="cash_amount">المبلغ لكل مستفيد ({{ $project->currency_symbol }})</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="0" name="cash_amount" id="cash_amount"
                                                       class="form-control @error('cash_amount') is-invalid @enderror"
                                                       value="{{ old('cash_amount', $project->cash_amount) }}"
                                                       placeholder="{{ $project->cash_amount ? $project->cash_amount : 'المبلغ' }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text font-weight-bold">{{ $project->currency_symbol }}</span>
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                @if($project->cash_amount_type == 'custom')
                                                    المشروع مخصص بمبالغ متغيرة (يمكن تحديده لكل مستفيد بالملف)
                                                @else
                                                    المبلغ الموحد للمشروع: {{ $project->formatted_cash_value }}
                                                @endif
                                            </small>
                                            @error('cash_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label for="ignore_conflicts">تجاوز تعارض المشاريع <span class="text-danger">*</span></label>
                                            <select name="ignore_conflicts" id="ignore_conflicts" class="form-control">
                                                <option value="0">لا (منع إضافة المستفيدين في حال وجود تعارض)</option>
                                                <option value="1">نعم (إسقاط التعارض وإضافة المستفيدين)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-2">
                                        <div class="form-group mb-0">
                                            <label for="delivery_details">تفاصيل الاستلام (اختياري)</label>
                                            <input type="text" name="delivery_details" id="delivery_details" class="form-control"
                                                   value="{{ old('delivery_details') }}"
                                                   placeholder="مثال: محفظة جوال باي أو اسم البنك ورقم الحساب">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-2">
                                        <div class="form-group mb-0">
                                            <label for="sub_warehouse_id">المخزن الفرعي (اختياري - غير مطلوب للنقدي)</label>
                                            <select name="sub_warehouse_id" id="sub_warehouse_id" class="form-control @error('sub_warehouse_id') is-invalid @enderror">
                                                <option value="">-- بدون مخزن (تسليم نقدي مباشر) --</option>
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
                                </div>
                            </div>
                        </div>
                    @else
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
                    @endif

                    <div class="row">
                        <!-- القسم الأول: استيراد ملف Excel -->
                        <div class="col-md-6">
                            <div class="card card-outline card-primary h-100">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-file-excel ml-1"></i>
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
                                            @if($project->is_cash)
                                                عند استخدام ملف Excel، سيتم قراءة المبلغ، الحالة، تاريخ وطريقة التسليم من الملف (إن وجدت أعمدتها).
                                            @else
                                                عند استخدام ملف Excel، سيتم قراءة الكمية، الحالة، وتاريخ التسليم من الملف.
                                            @endif
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
                                        <i class="fas fa-keyboard ml-1"></i>
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
                            <i class="fas fa-upload ml-1"></i> رفع واستيراد
                        </button>
                        <a href="{{ route('dashboard.projects.beneficiaries', $project) }}" class="btn btn-secondary">
                            <i class="fas fa-times ml-1"></i> إلغاء
                        </a>
                    </div>
                </form>
            @endcomponent
        </div>

        <div class="col-md-4">
            @component('dashboard::components.box')
                @slot('title', 'ملاحظات هامة')

                @if($project->is_cash)
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-hand-holding-usd ml-1"></i>
                        <strong>تسليم نقدي:</strong> لا يُشترط تحديد مخزن فرعي. التسليم يتم نقداً أو عبر محفظة أو حساب بنكي.
                    </div>
                @else
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle ml-1"></i>
                        <strong>تنبيه:</strong> يجب اختيار المخزن الفرعي قبل رفع الملف
                    </div>
                @endif

                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success ml-1"></i>
                        الملف يجب أن يكون بصيغة Excel (.xlsx, .xls, .csv)
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success ml-1"></i>
                        الحد الأقصى لحجم الملف: 10 ميجابايت
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success ml-1"></i>
                        يجب أن يحتوي الملف على رقم الهوية لكل مستفيد
                    </li>
                    @if($project->is_cash)
                        <li class="mb-2">
                            <i class="fas fa-check text-success ml-1"></i>
                            يمكن تحديد مبالغ متغيرة لكل مستفيد من خلال عمود المبلغ في الملف
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success ml-1"></i>
                            طرق التسليم المتاحة: كاش (نقداً)، محفظة إلكترونية، أو بنك
                        </li>
                    @else
                        <li class="mb-2">
                            <i class="fas fa-check text-success ml-1"></i>
                            سيتم ربط جميع المستفيدين بالمخزن المحدد
                        </li>
                    @endif
                    <li class="mb-2">
                        <i class="fas fa-info-circle text-info ml-1"></i>
                        المستفيدون الموجودون مسبقاً سيتم تحديث بياناتهم
                    </li>
                </ul>
            @endcomponent
        </div>
    </div>
</x-layout>
