<x-layout title="إضافة موظف جديد" :breadcrumbs="['dashboard.employees.index', 'dashboard.employees.create']">

    <div class="row justify-content-center">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-user-plus text-primary mr-1"></i> إضافة موظف جديد
                @endslot

                <form action="{{ route('dashboard.employees.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">الاسم الأول <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control form-control-sm @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">اسم الأب</label>
                            <input type="text" name="father_name" class="form-control form-control-sm @error('father_name') is-invalid @enderror" value="{{ old('father_name') }}">
                            @error('father_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">اسم الجد</label>
                            <input type="text" name="grandfather_name" class="form-control form-control-sm @error('grandfather_name') is-invalid @enderror" value="{{ old('grandfather_name') }}">
                            @error('grandfather_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">اسم العائلة <span class="text-danger">*</span></label>
                            <input type="text" name="family_name" class="form-control form-control-sm @error('family_name') is-invalid @enderror" value="{{ old('family_name') }}" required>
                            @error('family_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">رقم الهوية <span class="text-danger">*</span></label>
                            <input type="text" name="id_num" class="form-control form-control-sm @error('id_num') is-invalid @enderror" value="{{ old('id_num') }}" minlength="9" maxlength="9" placeholder="9 أرقام" required>
                            @error('id_num')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">رقم الجوال</label>
                            <input type="text" name="phone" class="form-control form-control-sm @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="مثال: 599123456">
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">الجنس</label>
                            <select name="gender" class="form-control form-control-sm @error('gender') is-invalid @enderror">
                                <option value="ذكر" {{ old('gender') == 'ذكر' ? 'selected' : '' }}>ذكر</option>
                                <option value="أنثى" {{ old('gender') == 'أنثى' ? 'selected' : '' }}>أنثى</option>
                            </select>
                            @error('gender')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">تاريخ الميلاد</label>
                            <input type="date" name="dob" class="form-control form-control-sm @error('dob') is-invalid @enderror" value="{{ old('dob') }}">
                            @error('dob')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold small">المسمى الوظيفي</label>
                            <input type="text" name="job_title" class="form-control form-control-sm @error('job_title') is-invalid @enderror" value="{{ old('job_title') }}">
                            @error('job_title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold small">ملاحظات</label>
                            <textarea name="notes" class="form-control form-control-sm @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @slot('footer')
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save mr-1"></i> حفظ الموظف
                        </button>
                        <a href="{{ route('dashboard.employees.index') }}" class="btn btn-secondary btn-sm ml-2">
                            إلغاء
                        </a>
                    @endslot
                </form>
            @endcomponent
        </div>
    </div>

</x-layout>
