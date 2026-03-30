<x-layout :title="'إضافة موزع جديد'" :breadcrumbs="['dashboard.mobile_app.users']">
    <form action="{{ route('dashboard.mobile_app.users.store') }}" method="POST">
        @csrf
        @component('dashboard::components.box')
            @slot('title')
                <i class="fas fa-user-plus"></i> تسجيل موزع جديد
            @endslot

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="required fw-bold mb-2">اسم الموزع الرباعي</label>
                    <input type="text" name="name" class="form-control" placeholder="أدخل الاسم الرباعي هنا" required>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="required fw-bold mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" placeholder="example@domain.com" required>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="required fw-bold mb-2">كلمة المرور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="required fw-bold mb-2">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            @slot('footer')
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ بيانات الموزع
                    </button>
                    <a href="{{ route('dashboard.mobile_app.users') }}" class="btn btn-default">إلغاء وتراجع</a>
                </div>
            @endslot
        @endcomponent
    </form>
</x-layout>
