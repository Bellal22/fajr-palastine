<x-layout :title="'403 - غير مصرح'">
    <div class="container text-center py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="error-container py-5">
                    <h1 class="display-1 text-warning font-weight-bold">403</h1>
                    <h2 class="mb-4">غير مصرح لك بالوصول</h2>
                    <p class="lead text-muted mb-5">
                        عذراً، ليس لديك الصلاحيات الكافية للوصول إلى هذه الصفحة.
                    </p>
                    <div class="actions">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-lg {{ app()->isLocale('ar') ? 'ml-3' : 'mr-3' }}">
                            <i class="fas fa-arrow-right"></i> العودة للخلف
                        </a>
                        <a href="{{ route('dashboard.home') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-home"></i> الصفحة الرئيسية
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
