{{ BsForm::resource('complaint_types')->get(url()->current()) }}

@component('dashboard::components.box')
    @slot('title')
        <i class="fas fa-filter text-primary"></i> الفلترة والبحث
    @endslot

    <div class="row">
        <div class="col-12 mb-1 mt-1">
            <h6 class="text-primary mb-0">
                <i class="fas fa-search"></i> خيارات البحث والتصفية
            </h6>
            <hr class="mt-1 mb-2">
        </div>

        <div class="col-md-5 mb-2">
            <label class="mb-1 font-weight-bold">
                <i class="fas fa-heading text-info"></i> اسم نوع الشكوى
            </label>
            {{ BsForm::text('name')
                ->value(request('name'))
                ->placeholder('ادخل اسم نوع الشكوى للبحث...')
                ->label(false) }}
        </div>

        <div class="col-md-4 mb-2">
            <label class="mb-1 font-weight-bold">
                <i class="fas fa-toggle-on text-info"></i> الحالة
            </label>
            {{ BsForm::select('is_active', [
                '' => 'جميع الحالات',
                '1' => 'مفعّل فقط',
                '0' => 'مطفي فقط',
            ])
                ->value(request('is_active'))
                ->label(false) }}
        </div>

        <div class="col-md-3 mb-2">
            <label class="mb-1 font-weight-bold">
                <i class="fas fa-list-ol text-info"></i> عدد العناصر
            </label>
            {{ BsForm::number('perPage')
                ->value(request('perPage', 10))
                ->min(1)
                ->label(false) }}
        </div>
    </div>

    @slot('footer')
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> تصفية النتائج
        </button>

        <button type="button" class="btn btn-secondary ml-2" id="resetFilters">
            <i class="fas fa-eraser"></i> إعادة ضبط
        </button>
    @endslot
@endcomponent

@push('scripts')
<script>
$(document).ready(function() {
    $('#resetFilters').on('click', function() {
        window.location.href = "{{ route('dashboard.complaint_types.index') }}";
    });
});
</script>
@endpush

{{ BsForm::close() }}
