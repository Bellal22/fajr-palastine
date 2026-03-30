<x-layout :title="'تجهيز التكليفات'" :breadcrumbs="['dashboard.mobile_app.assignments']">
    <div class="row g-5">
        <div class="col-md-6">
            <div class="card card-flush shadow-sm">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title fw-bold">ربط المربعات بالمسؤولين</h3>
                </div>
                <div class="card-body py-3">
                    <form action="{{ route('dashboard.mobile_app.assign_block') }}" method="POST">
                        @csrf
                        <div class="mb-5">
                            <label class="form-label fw-bold small text-muted">المربع / الحي</label>
                            <select name="block_id" class="form-select form-select-solid" required>
                                <option value="">اختر المربع...</option>
                                @foreach($blocks as $block)
                                    <option value="{{ $block->id }}">{{ $block->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold small text-muted">مسؤول المنطقة / المشرف</label>
                            <select name="area_responsible_id" class="form-select form-select-solid" required>
                                <option value="">اختر المسؤول...</option>
                                @foreach($responsibles as $resp)
                                    <option value="{{ $resp->id }}">{{ $resp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-sm">حفظ الربط</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-flush shadow-sm">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title fw-bold">التكليفات الحالية</h3>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-row-dashed">
                            <thead>
                                <tr class="text-muted fw-bold">
                                    <th>المسؤول</th>
                                    <th>المربعات المرتبطة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($responsibles as $resp)
                                <tr>
                                    <td class="fw-bold">{{ $resp->name }}</td>
                                    <td>
                                        @foreach($resp->blocks as $block)
                                            <span class="badge badge-light-info mb-1">{{ $block->title }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
