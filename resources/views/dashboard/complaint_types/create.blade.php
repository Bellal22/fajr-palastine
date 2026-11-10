<x-layout title="إضافة نوع جديد" :breadcrumbs="['dashboard.complaint_types.create']">
    <div class="row justify-content-center">
        <div class="col-md-9">
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-plus-circle text-primary"></i> إضافة نوع شكوى جديد وتحديد المعايير
                @endslot

                <form action="{{ route('dashboard.complaint_types.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="name" class="font-weight-bold">
                            <i class="fas fa-heading text-primary"></i> اسم نوع الشكوى <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="مثال: شكوى إغاثية، شكوى سلة غذائية، استفسار عام" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="is_active" class="font-weight-bold">
                            <i class="fas fa-toggle-on text-primary"></i> حالة التفعيل <span class="text-danger">*</span>
                        </label>
                        <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>مفعّل (يستقبل الشكاوى من المواطنين)</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>مطفي (مغلق ومستثنى من التقديم)</option>
                        </select>
                        @error('is_active')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="cooldown_days" class="font-weight-bold">
                            <i class="fas fa-history text-primary"></i> فترة الانتظار الفاصلة للفرد (بالأيام) <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="cooldown_days" id="cooldown_days" class="form-control @error('cooldown_days') is-invalid @enderror" value="{{ old('cooldown_days', 30) }}" min="0" required>
                        <small class="form-text text-muted">عدد الأيام المسموح بها بين الشكوى والأخرى لنفس الشخص ومن نفس هذا النوع (أدخل 0 لإلغاء الانتظار).</small>
                        @error('cooldown_days')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="inactive_response" class="font-weight-bold">
                            <i class="fas fa-comment-dots text-primary"></i> الرد المخصص المسترجع للمواطن عند إيقاف هذا النوع
                        </label>
                        <textarea name="inactive_response" id="inactive_response" rows="4" class="form-control @error('inactive_response') is-invalid @enderror" placeholder="اكتب الرسالة المخصصة التي تظهر للمواطن عند اختياره لهذا النوع وهو مطفي (مثال: نعتذر، استقبال الشكاوى الإغاثية متوقف حالياً وسنفتح باب التقديم قريباً)...">{{ old('inactive_response') }}</textarea>
                        <small class="form-text text-muted">هذا النص سيظهر في مربع التنبيه للمواطن عند اختياره لهذا النوع إذا كان مفتاح الحالة "مطفي".</small>
                        @error('inactive_response')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save"></i> حفظ نوع الشكوى
                        </button>
                        <a href="{{ route('dashboard.complaint_types.index') }}" class="btn btn-secondary">
                            إلغاء
                        </a>
                    </div>
                </form>
            @endcomponent
        </div>
    </div>
</x-layout>
