<x-layout title="بروفايل الموظف" :breadcrumbs="['dashboard.employees.index']">
    <div class="row">
        {{-- Sidebar Profile Card --}}
        <div class="col-md-3">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="avatar-circle mx-auto bg-primary shadow-sm" style="width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; color: white;">
                            {{ mb_substr($employee->first_name, 0, 1) }}
                        </div>
                    </div>
                    <h3 class="profile-username text-center font-weight-bold mt-3">{{ $employee->first_name }} {{ $employee->family_name }}</h3>
                    <p class="text-muted text-center small"><span class="badge badge-primary">موظف</span></p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item py-2">
                            <b>رقم الهوية</b> <a class="float-right text-dark font-weight-bold">{{ $employee->id_num }}</a>
                        </li>
                        <li class="list-group-item py-2">
                            <b>رقم الجوال</b> <a href="tel:{{ $employee->phone }}" class="float-right text-primary font-weight-bold">{{ $employee->phone ?? '—' }}</a>
                        </li>
                        <li class="list-group-item py-2">
                            <b>الوظيفة</b> <a class="float-right text-dark font-weight-bold">{{ $employee->job_title ?? '—' }}</a>
                        </li>
                    </ul>

                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('dashboard.employees.edit', $employee->id) }}" class="btn btn-primary btn-block mb-2 font-weight-bold">
                            <i class="fas fa-edit mr-1"></i> تعديل البيانات
                        </a>
                        <form action="{{ route('dashboard.employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد نهائياً من حذف الموظف؟ لا يمكن التراجع.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-block btn-sm">
                                <i class="fas fa-trash-alt mr-1"></i> حذف الموظف
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Tabs --}}
        <div class="col-md-9">
            <div class="card card-primary card-outline card-tabs shadow-sm">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="overview-tab" data-toggle="pill" href="#tab-overview" role="tab">
                                <i class="fas fa-info-circle mr-1"></i> البيانات الأساسية
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="coupons-tab" data-toggle="pill" href="#tab-coupons" role="tab">
                                <i class="fas fa-ticket-alt mr-1"></i> سجل الترشيحات والطرود
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        {{-- Tab: Overview --}}
                        <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box bg-light border shadow-none">
                                        <span class="info-box-icon text-info"><i class="fas fa-user"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted small">الاسم رباعي</span>
                                            <span class="info-box-number" style="font-size: 0.95rem;">
                                                {{ $employee->first_name }} {{ $employee->father_name }} {{ $employee->grandfather_name }} {{ $employee->family_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-light border shadow-none">
                                        <span class="info-box-icon text-primary"><i class="fas fa-venus-mars"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted small">الجنس</span>
                                            <span class="info-box-number">{{ $employee->gender ?? 'غير محدد' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-light border shadow-none">
                                        <span class="info-box-icon text-success"><i class="fas fa-calendar"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted small">تاريخ الميلاد</span>
                                            <span class="info-box-number">{{ $employee->dob ? $employee->dob->format('Y-m-d') : 'غير محدد' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h5 class="border-bottom pb-2 font-weight-bold text-dark"><i class="far fa-sticky-note mr-1 text-warning"></i> ملاحظات</h5>
                                @if($employee->notes)
                                    <div class="callout callout-warning bg-light">
                                        <p class="mb-0 lead text-dark" style="font-size: 1rem;">{{ $employee->notes }}</p>
                                    </div>
                                @else
                                    <div class="alert alert-light border border-dashed text-center">
                                        <i class="fas fa-info-circle mr-1"></i> لا توجد ملاحظات مسجلة لهذا الموظف.
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Tab: Coupons --}}
                        <div class="tab-pane fade" id="tab-coupons" role="tabpanel">
                            @if($coupons->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped table-valign-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>المشروع الإغاثي</th>
                                                <th class="text-center">الكمية</th>
                                                <th class="text-center">حالة الاستلام</th>
                                                <th>نقطة التوزيع</th>
                                                <th>تاريخ التنفيذ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($coupons as $coupon)
                                                <tr>
                                                    <td>
                                                        <div class="font-weight-bold text-dark">{{ $coupon->project_name }}</div>
                                                        <small class="text-muted">{{ Str::limit($coupon->project_description, 60) }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-primary px-3 py-2" style="font-size: 1rem;">{{ $coupon->quantity }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($coupon->status == 'مستلم')
                                                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> تم الاستلام</span>
                                                        @else
                                                            <span class="badge badge-danger"><i class="fas fa-clock mr-1"></i> بانتظار التسليم</span>
                                                        @endif
                                                    </td>
                                                    <td><i class="fas fa-warehouse small text-muted mr-1"></i> {{ $coupon->warehouse_name ?? '—' }}</td>
                                                    <td><span class="text-muted font-weight-bold">{{ $coupon->delivery_date ?? '—' }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-ticket-alt fa-3x text-muted mb-3 opacity-25"></i>
                                    <h5 class="text-muted font-weight-bold">لا توجد طرود أو كوبونات مسجلة لهذا الموظف</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted small py-3">
                    <i class="far fa-calendar-alt mr-1"></i> تاريخ الإضافة: {{ $employee->created_at->format('Y-m-d H:i') }} | 
                    <i class="far fa-clock mr-1"></i> آخر تحديث: {{ $employee->updated_at->format('Y-m-d H:i') }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
