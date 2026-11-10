<x-layout title="فحص وتدقيق أرقام الهويات" :breadcrumbs="['dashboard.people.index']">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-dark">
                    <h5 class="mb-0"><i class="fas fa-check-double mr-2"></i> فحص وتدقيق أرقام الهويات</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        أدخل قائمة بأرقام الهويات للتحقق من صحتها برمجياً بناءً على (معادلة لونا - Luhn Algorithm).
                    </p>

                    <form action="{{ route('dashboard.people.export_verify_ids') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="font-weight-bold">قائمة أرقام الهويات <span class="text-danger">*</span></label>
                            <textarea name="id_nums" class="form-control" rows="12" placeholder="أدخل أرقام الهويات هنا..." required>{{ old('id_nums') }}</textarea>
                        </div>

                        <div class="mt-4 p-3 border rounded bg-light">
                            <h6 class="font-weight-bold text-primary"><i class="fas fa-info-circle"></i> تعليمات الاستخدام:</h6>
                            <ul class="mb-0 small text-dark" style="line-height: 1.8;">
                                <li>يتم التحقق من صحة الرقم بناءً على **معادلة لونا (Luhn Algorithm)** المستخدمة في الهويات الفلسطينية والإسرائيلية.</li>
                                <li>الملف الناتج يحتوي على عمودين: رقم الهوية، وحالة الفحص (صحيح / خطأ).</li>
                                <li>النظام يتعامل مع الهويات التي تحتوي على أرقام فقط (9 أرقام).</li>
                                <li>إذا كان الرقم أقل من 9 أرقام، يقوم النظام بإضافة أصفار جهة اليسار تلقائياً قبل الفحص.</li>
                            </ul>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-file-excel mr-2"></i> بدء الفحص وتحميل النتائج
                            </button>
                            <a href="{{ route('dashboard.people.general_search') }}" class="btn btn-outline-secondary btn-lg px-5">
                                <i class="fas fa-arrow-right mr-2"></i> العودة للبحث
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
