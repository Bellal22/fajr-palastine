<x-layout :title="'404 - الصفحة غير موجودة'">
    <div class="container text-center py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="error-container py-5">
                    <h1 class="display-1 text-danger font-weight-bold">404</h1>
                    <h2 class="mb-4">المورد المطلوب غير موجود</h2>
                    <p class="lead text-muted mb-5">
                        نعتذر، ولكن الصفحة التي تبحث عنها غير موجودة أو تم نقلها.
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
