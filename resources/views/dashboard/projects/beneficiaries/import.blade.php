<x-layout :title="'استيراد المستفيدين - ' . $project->name">

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
    </div>
</x-layout>
