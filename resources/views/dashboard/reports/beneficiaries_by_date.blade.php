<x-layout :title="'تقرير المستلمين حسب الفترة الزمنية'" :breadcrumbs="['dashboard.reports.projects']">
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
            <i class="fas fa-file-excel text-success ml-2"></i>
            تقرير المستلمين حسب الفترة الزمنية
        </h3>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card report-card">
                <div class="card-header bg-white">
                    <h5 class="mb-0 font-weight-bold">حدد معايير التقرير</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.reports.projects.beneficiaries_by_date.export') }}" method="POST">
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
                            <label for="project_id">المشروع / الكوبون (اختياري)</label>
                            <select name="project_id" id="project_id" class="form-control select2">
                                <option value="">-- جميع المشاريع --</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->start_date ? $project->start_date->format('Y-m-d') : 'بدون تاريخ' }})</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">إذا لم يتم تحديد مشروع، سيتم تصدير كل المشاريع التي تم التسليم فيها خلال هذه الفترة.</small>
                            @error('project_id')
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
