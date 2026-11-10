<x-layout title="تصدير سجل الكوبونات الجماعي" :breadcrumbs="['dashboard.people.index']">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-file-excel mr-2"></i> استخراج سجل الكوبونات بأرقام الهوية</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        أدخل أرقام الهويات في المربع أدناه (رقم واحد في كل سطر أو مفصولة بمسافات). 
                        سيقوم النظام بالبحث عن كل مستفيد، وإذا كان الشخص تابعاً (ليس رب أسرة)، سيتم جلب بيانات رب الأسرة وسجل الكوبونات الخاص بالعائلة ككل.
                    </p>

                    <form action="{{ route('dashboard.people.export_bulk_history') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="font-weight-bold">قائمة أرقام الهويات <span class="text-danger">*</span></label>
                            <textarea name="id_nums" class="form-control" rows="12" placeholder="أدخل أرقام الهويات هنا..." required>{{ old('id_nums') }}</textarea>
                        </div>

                        <div class="mt-4 p-3 border rounded bg-light">
                            <h6 class="font-weight-bold text-primary"><i class="fas fa-info-circle"></i> تعليمات الاستخدام:</h6>
                            <ul class="mb-0 small text-dark" style="line-height: 1.8;">
                                <li>يتم البحث في السجلات الأساسية (Persons) والسجلات المؤقتة (Temporary).</li>
                                <li>الملف الناتج يحتوي على: الاسم الرباعي، رقم الجوال، اسم المشروع، الحالة، تاريخ الاستلام، والمخزن.</li>
                                <li>إذا أدخلت رقم هوية (ابن/زوجة)، سيظهر اسم (الأب/رب الأسرة) في الملف مع سجل كوبونات العائلة.</li>
                                <li>يمكنك نسخ قائمة كبيرة من الهويات ولصقها مباشرة في المربع أعلاه.</li>
                            </ul>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-download mr-2"></i> تحميل ملف الاكسل
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
