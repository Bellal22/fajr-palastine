@extends('dashboard.layouts.app')

@section('title', 'تعديل بيانات السجل المؤقت')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg border-0" style="border-radius: 20px;">
            <div class="card-header bg-primary text-white p-4" style="border-radius: 20px 20px 0 0;">
                <h4 class="mb-0 font-weight-bold"><i class="fas fa-edit mr-2"></i> تعديل بيانات: {{ $person->getFullName() }}</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('dashboard.incomplete_people.update', $person->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">الاسم الأول <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $person->first_name) }}" required>
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">اسم الأب</label>
                            <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" value="{{ old('father_name', $person->father_name) }}">
                            @error('father_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">اسم الجد</label>
                            <input type="text" name="grandfather_name" class="form-control @error('grandfather_name') is-invalid @enderror" value="{{ old('grandfather_name', $person->grandfather_name) }}">
                            @error('grandfather_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">اسم العائلة <span class="text-danger">*</span></label>
                            <input type="text" name="family_name" class="form-control @error('family_name') is-invalid @enderror" value="{{ old('family_name', $person->family_name) }}" required>
                            @error('family_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">رقم الهوية <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" 
                                       id="id_num" 
                                       name="id_num" 
                                       class="form-control @error('id_num') is-invalid @enderror" 
                                       value="{{ old('id_num', $person->id_num) }}" 
                                       required 
                                       maxlength="9"
                                       pattern="\d{9}"
                                       placeholder="مثال: 405242876">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-white" id="id_status_icon">
                                        <i class="fas fa-id-card text-muted"></i>
                                    </span>
                                </div>
                            </div>
                            <div id="id_feedback" class="mt-1"></div>
                            @error('id_num') <div class="text-danger small font-weight-bold mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">رقم الجوال <small class="text-muted">(يبدأ بـ 059 أو 056 ويليه 7 أرقام)</small></label>
                            <div class="input-group">
                                <input type="text" 
                                       id="phone" 
                                       name="phone" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $person->phone) }}"
                                       maxlength="10"
                                       placeholder="مثال: 0599123456 أو 0569123456">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-white" id="phone_status_icon">
                                        <i class="fas fa-phone text-muted"></i>
                                    </span>
                                </div>
                            </div>
                            <div id="phone_feedback" class="mt-1"></div>
                            @error('phone') <div class="text-danger small font-weight-bold mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">الحالة الاجتماعية</label>
                            <select name="social_status" class="form-control select2">
                                <option value="">اختر الحالة...</option>
                                <option value="متزوج" {{ old('social_status', $person->social_status) == 'متزوج' ? 'selected' : '' }}>متزوج</option>
                                <option value="أعزب" {{ old('social_status', $person->social_status) == 'أعزب' ? 'selected' : '' }}>أعزب</option>
                                <option value="أرمل" {{ old('social_status', $person->social_status) == 'أرمل' ? 'selected' : '' }}>أرمل</option>
                                <option value="مطلق" {{ old('social_status', $person->social_status) == 'مطلق' ? 'selected' : '' }}>مطلق</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">المنطقة</label>
                            <input type="text" name="area" class="form-control" value="{{ old('area', $person->area) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">مسؤول المنطقة</label>
                            <select name="area_responsible_id" class="form-control select2">
                                <option value="">اختر المسؤول...</option>
                                @foreach($areaResponsibles as $id => $name)
                                    <option value="{{ $id }}" {{ old('area_responsible_id', $person->area_responsible_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">المربع / المندوب</label>
                            <select name="block_id" class="form-control select2">
                                <option value="">اختر المربع...</option>
                                @foreach($blocks as $id => $name)
                                    <option value="{{ $id }}" {{ old('block_id', $person->block_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $person->notes) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('dashboard.incomplete_people.show', $person->id) }}" class="btn btn-light px-4 rounded-pill font-weight-bold"> إلغاء</a>
                        <button type="submit" id="submitBtn" class="btn btn-primary px-5 rounded-pill font-weight-bold">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function luhnCheck(id) {
        if (!id) return false;
        id = id.toString().replace(/\D/g, '');
        if (id.length === 0 || id.length > 9) return false;
        id = id.padStart(9, '0');
        let sum = 0;
        for (let i = 0; i < 9; i++) {
            let digit = parseInt(id[i], 10);
            let step = digit * ((i % 2) + 1);
            if (step > 9) step -= 9;
            sum += step;
        }
        return (sum % 10 === 0);
    }

    function isPhoneValid(phone) {
        if (!phone || phone.trim() === '') return true; // nullable
        let cleaned = phone.replace(/\D/g, '');
        if (cleaned.startsWith('970') || cleaned.startsWith('972')) {
            cleaned = cleaned.substring(3);
        }
        if (cleaned.startsWith('0')) {
            cleaned = cleaned.substring(1);
        }
        if (cleaned.length === 9 && (cleaned.startsWith('59') || cleaned.startsWith('56'))) {
            cleaned = '0' + cleaned;
        }
        return /^(059|056)\d{7}$/.test(cleaned);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const idInput = document.getElementById('id_num');
        const idFeedback = document.getElementById('id_feedback');
        const idIcon = document.getElementById('id_status_icon');

        const phoneInput = document.getElementById('phone');
        const phoneFeedback = document.getElementById('phone_feedback');
        const phoneIcon = document.getElementById('phone_status_icon');

        function validateId() {
            const val = idInput.value.trim();
            if (!val) {
                idFeedback.innerHTML = '';
                idIcon.innerHTML = '<i class="fas fa-id-card text-muted"></i>';
                return false;
            }

            if (!/^\d+$/.test(val)) {
                idFeedback.innerHTML = '<span class="text-danger small font-weight-bold"><i class="fas fa-times-circle"></i> رقم الهوية يجب أن يحتوي على أرقام فقط</span>';
                idIcon.innerHTML = '<i class="fas fa-times text-danger"></i>';
                return false;
            }

            if (val.length < 9) {
                idFeedback.innerHTML = '<span class="text-muted small font-weight-bold"><i class="fas fa-info-circle"></i> متبقي ' + (9 - val.length) + ' أرقام لاكتمال رقم الهوية</span>';
                idIcon.innerHTML = '<i class="fas fa-hourglass-half text-warning"></i>';
                return false;
            }

            if (val.length === 9) {
                if (luhnCheck(val)) {
                    idFeedback.innerHTML = '<span class="text-success small font-weight-bold"><i class="fas fa-check-circle"></i> رقم الهوية صحيح وفقاً لمعادلة لونا</span>';
                    idIcon.innerHTML = '<i class="fas fa-check text-success"></i>';
                    return true;
                } else {
                    idFeedback.innerHTML = '<span class="text-danger small font-weight-bold"><i class="fas fa-times-circle"></i> رقم الهوية غير صحيح (غير مطابق لمعادلة لونا)</span>';
                    idIcon.innerHTML = '<i class="fas fa-times text-danger"></i>';
                    return false;
                }
            }

            return false;
        }

        function validatePhone() {
            const val = phoneInput.value.trim();
            if (!val) {
                phoneFeedback.innerHTML = '';
                phoneIcon.innerHTML = '<i class="fas fa-phone text-muted"></i>';
                return true;
            }

            if (isPhoneValid(val)) {
                phoneFeedback.innerHTML = '<span class="text-success small font-weight-bold"><i class="fas fa-check-circle"></i> رقم جوال صحيح معتمد (يبدأ بـ 059 أو 056 ومتبوعاً بـ 7 أرقام)</span>';
                phoneIcon.innerHTML = '<i class="fas fa-check text-success"></i>';
                return true;
            } else {
                phoneFeedback.innerHTML = '<span class="text-danger small font-weight-bold"><i class="fas fa-times-circle"></i> رقم الجوال يجب أن يبدأ بـ 059 أو 056 ومتبوعاً بـ 7 أرقام (10 أرقام إجمالاً)</span>';
                phoneIcon.innerHTML = '<i class="fas fa-times text-danger"></i>';
                return false;
            }
        }

        idInput.addEventListener('input', validateId);
        phoneInput.addEventListener('input', validatePhone);

        // Initial validation on load
        validateId();
        validatePhone();
    });
</script>
@endpush
@endsection
