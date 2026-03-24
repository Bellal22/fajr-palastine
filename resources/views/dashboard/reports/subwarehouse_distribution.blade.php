<x-layout :title="'تقرير توزيع المخازن الفرعية'" :breadcrumbs="['dashboard.reports.projects']">
    @push('style')
        <style>
            .report-card {
                border-radius: 10px;
                border: none;
                box-shadow: 0 2px 6px rgba(0,0,0,0.06);
                transition: all 0.3s ease;
                background: white;
            }
        </style>
    @endpush

    <!-- Page Header -->
    <div class="mb-4">
        <h3 class="mb-0 font-weight-bold">
            <i class="fas fa-warehouse text-primary ml-2"></i>
            تقرير توزيع المخازن الفرعية
        </h3>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card report-card">
                <div class="card-header bg-white">
                    <h5 class="mb-0 font-weight-bold">حدد معايير التقرير</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.reports.sub_warehouses.distribution.export') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="date_from">من تاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="date_from" id="date_from" class="form-control" required value="{{ old('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
                                @error('date_from')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="date_to">إلى تاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="date_to" id="date_to" class="form-control" required value="{{ old('date_to', now()->format('Y-m-d')) }}">
                                @error('date_to')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sub_warehouse_id">المخزن الفرعي / نقطة التوزيع (اختياري)</label>
                            <select name="sub_warehouse_id" id="sub_warehouse_id" class="form-control select2">
                                <option value="">-- جميع المخازن --</option>
                                @foreach($subWarehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">إذا لم يتم تحديد مخزن، سيتم تصدير تقرير مجمع لكل النقاط مع صفحات منفصلة لكل نقطة.</small>
                            @error('sub_warehouse_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success px-5">
                                <i class="fas fa-file-excel mr-2"></i> تصدير إكسل
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    dir: "rtl"
                });
            });
        </script>
    @endpush
</x-layout>
