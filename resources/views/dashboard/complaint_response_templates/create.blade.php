<x-layout title="إضافة رد جاهز جديد" :breadcrumbs="['dashboard.complaint_response_templates.create']">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-plus-circle"></i> إضافة رد جاهز جديد
                @endslot

                <form action="{{ route('dashboard.complaint_response_templates.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="title" class="font-weight-bold">
                            <i class="fas fa-heading text-primary"></i> عنوان الرد الجاهز <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="مثال: تم قبول الشكوى وتوجيهها للمستودع" required>
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="status" class="font-weight-bold">
                            <i class="fas fa-flag text-primary"></i> الحالة المقترحة للشكوى (اختياري)
                        </label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                            <option value="">-- بدون تغيير الحالة --</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="resolved" {{ old('status') == 'resolved' ? 'selected' : '' }}>تم الحل / مقبول</option>
                            <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                        <small class="form-text text-muted">عند اختيار هذا الرد الجاهز، سيتم تغيير حالة الشكوى تلقائياً لهذه الحالة.</small>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="content" class="font-weight-bold">
                            <i class="fas fa-comment-alt text-primary"></i> نص الرد التلقائي <span class="text-danger">*</span>
                        </label>
                        <textarea name="content" id="content" rows="6" class="form-control @error('content') is-invalid @enderror" placeholder="اكتب نص الرد التلقائي التفصيلي هنا..." required>{{ old('content') }}</textarea>
                        @error('content')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save"></i> حفظ الرد الجاهز
                        </button>
                        <a href="{{ route('dashboard.complaint_response_templates.index') }}" class="btn btn-secondary">
                            إلغاء
                        </a>
                    </div>
                </form>
            @endcomponent
        </div>
    </div>
</x-layout>
