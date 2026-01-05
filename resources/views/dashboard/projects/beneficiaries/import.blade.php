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

                    <div class="row">
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
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> المخزن الذي سيستلم منه المستفيدون
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="file">اختر ملف Excel <span class="text-danger">*</span></label>
                                <input type="file"
                                       name="file"
                                       id="file"
                                       class="form-control @error('file') is-invalid @enderror"
                                       accept=".xlsx,.xls,.csv"
                                       required>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
