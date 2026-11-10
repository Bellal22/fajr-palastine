<x-layout title="تعديل بيانات الموظف" :breadcrumbs="['dashboard.employees.index', 'dashboard.employees.edit']">

    <div class="row justify-content-center">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-user-edit text-primary mr-1"></i> تعديل بيانات الموظف: {{ $employee->first_name }} {{ $employee->family_name }}
                @endslot

                <form action="{{ route('dashboard.employees.update', $employee->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">الاسم الأول <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control form-control-sm @error('first_name') is-invalid @enderror" value="{{ old('first_name', $employee->first_name) }}" required>
                            @error('first_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">اسم الأب</label>
                            <input type="text" name="father_name" class="form-control form-control-sm @error('father_name') is-invalid @enderror" value="{{ old('father_name', $employee->father_name) }}">
                            @error('father_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">اسم الجد</label>
                            <input type="text" name="grandfather_name" class="form-control form-control-sm @error('grandfather_name') is-invalid @enderror" value="{{ old('grandfather_name', $employee->grandfather_name) }}">
                            @error('grandfather_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">اسم العائلة <span class="text-danger">*</span></label>
                            <input type="text" name="family_name" class="form-control form-control-sm @error('family_name') is-invalid @enderror" value="{{ old('family_name', $employee->family_name) }}" required>
                            @error('family_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">رقم الهوية <span class="text-danger">*</span></label>
                            <input type="text" name="id_num" class="form-control form-control-sm @error('id_num') is-invalid @enderror" value="{{ old('id_num', $employee->id_num) }}" minlength="9" maxlength="9" placeholder="9 أرقام" required>
                            @error('id_num')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">رقم الجوال</label>
                            <input type="text" name="phone" class="form-control form-control-sm @error('phone') is-invalid @enderror" value="{{ old('phone', $employee->phone) }}" placeholder="مثال: 599123456">
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">الجنس</label>
                            <select name="gender" class="form-control form-control-sm @error('gender') is-invalid @enderror">
                                <option value="ذكر" {{ old('gender', $employee->gender) == 'ذكر' ? 'selected' : '' }}>ذكر</option>
                                <option value="أنثى" {{ old('gender', $employee->gender) == 'أنثى' ? 'selected' : '' }}>أنثى</option>
                            </select>
                            @error('gender')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">تاريخ الميلاد</label>
                            <input type="date" name="dob" class="form-control form-control-sm @error('dob') is-invalid @enderror" value="{{ old('dob', $employee->dob ? $employee->dob->format('Y-m-d') : '') }}">
                            @error('dob')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold small">المسمى الوظيفي</label>
                            <input type="text" name="job_title" class="form-control form-control-sm @error('job_title') is-invalid @enderror" value="{{ old('job_title', $employee->job_title) }}">
                            @error('job_title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold small">ملاحظات</label>
                            <textarea name="notes" class="form-control form-control-sm @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $employee->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @slot('footer')
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save mr-1"></i> تحديث البيانات
                        </button>
                        <a href="{{ route('dashboard.employees.show', $employee->id) }}" class="btn btn-secondary btn-sm ml-2">
                            إلغاء
                        </a>
                    @endslot
                </form>
            @endcomponent
        </div>
    </div>

</x-layout>
