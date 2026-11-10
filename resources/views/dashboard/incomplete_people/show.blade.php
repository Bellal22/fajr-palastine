<x-layout title="بروفايل السجل المؤقت" :breadcrumbs="['dashboard.incomplete_people.index']">
    <div class="row">
        {{-- Sidebar Profile Card --}}
        <div class="col-md-3">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="avatar-circle mx-auto bg-primary shadow-sm" style="width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; color: white;">
                            {{ mb_substr($person->first_name, 0, 1) }}
                        </div>
                    </div>
                    <h3 class="profile-username text-center font-weight-bold mt-3">{{ $person->getFullName() }}</h3>
                    <p class="text-muted text-center small"><span class="badge badge-warning">سجل مؤقت - بيانات غير مكتملة</span></p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item py-2">
                            <b>رقم الهوية</b>
                            <div class="float-right text-right">
                                <span class="text-dark font-weight-bold">{{ $person->id_num }}</span>
                                @if($person->isValidId())
                                    <span class="badge badge-success ml-1"><i class="fas fa-check-circle"></i> لونا صحيح</span>
                                @else
                                    <span class="badge badge-danger ml-1" title="غير مطابق لمعادلة لونا"><i class="fas fa-times-circle"></i> لونا غير صالح</span>
                                @endif
                            </div>
                        </li>
                        <li class="list-group-item py-2">
                            <b>رقم الجوال</b>
                            <div class="float-right text-right">
                                @if($person->phone)
                                    <a href="tel:{{ $person->phone }}" class="text-primary font-weight-bold">{{ $person->phone }}</a>
                                    @if($person->isValidPhone())
                                        <span class="badge badge-success ml-1"><i class="fas fa-check-circle"></i> معتمد (059/056)</span>
                                    @else
                                        <span class="badge badge-warning text-dark ml-1" title="يجب أن يبدأ بـ 059 أو 056 ومتبوعاً بـ 7 أرقام"><i class="fas fa-exclamation-triangle"></i> غير مطابق</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </li>
                        <li class="list-group-item py-2">
                            <b>المنطقة</b> <a class="float-right text-dark">{{ $person->area ?? 'غزة' }}</a>
                        </li>
                        @if($person->isBanned())
                            <li class="list-group-item py-2 bg-light">
                                <span class="badge badge-danger w-100 p-2"><i class="fas fa-ban mr-1"></i> مدرج في قائمة الحظر (البلوكات)</span>
                            </li>
                        @endif
                        @if($person->isEmployee())
                            <li class="list-group-item py-2 bg-light">
                                <span class="badge badge-info w-100 p-2"><i class="fas fa-user-tie mr-1"></i> مسجل كـ موظف في الجمعية</span>
                            </li>
                        @endif
                        @if($person->isBeneficiary())
                            <li class="list-group-item py-2 bg-light">
                                <span class="badge badge-primary w-100 p-2"><i class="fas fa-user-check mr-1"></i> مسجل كمستفيد معتمد مسبقاً</span>
                            </li>
                        @endif
                    </ul>

                    <div class="d-flex flex-column gap-2">
                        <button type="button" class="btn btn-success btn-block mb-2 font-weight-bold" onclick="transferSingle({{ $person->id }})">
                            <i class="fas fa-rocket mr-1"></i> نقل للسجل المعتمد
                        </button>
                        <a href="{{ route('dashboard.incomplete_people.edit', $person->id) }}" class="btn btn-primary btn-block mb-2 font-weight-bold">
                            <i class="fas fa-edit mr-1"></i> تعديل البيانات
                        </a>
                        <form action="{{ route('dashboard.incomplete_people.destroy', $person->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد نهائياً من الحذف؟ لا يمكن التراجع.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-block btn-sm">
                                <i class="fas fa-trash-alt mr-1"></i> حذف السجل نهائياً
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
                                <i class="fas fa-ticket-alt mr-1"></i> سجل الكوبونات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="family-tab" data-toggle="pill" href="#tab-family" role="tab">
                                <i class="fas fa-users mr-1"></i> أفراد العائلة
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
                                        <span class="info-box-icon text-danger"><i class="fas fa-heart"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted small uppercase">الحالة الاجتماعية</span>
                                            <span class="info-box-number">{{ $person->social_status ?? 'غير محدد' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-light border shadow-none">
                                        <span class="info-box-icon text-primary"><i class="fas fa-user-shield"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted small uppercase">مسؤول المنطقة</span>
                                            <span class="info-box-number @empty($person->area_responsible_id) text-muted italic @endempty">
                                                {{ $person->areaResponsible->name ?? 'غير معتمد لغاية الآن' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-light border shadow-none">
                                        <span class="info-box-icon text-success"><i class="fas fa-map-marked-alt"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted small uppercase">المربع / المندوب</span>
                                            <span class="info-box-number">{{ $person->block->name ?? 'غير محدد' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h5 class="border-bottom pb-2 font-weight-bold text-dark"><i class="far fa-sticky-note mr-1 text-warning"></i> ملاحظات إدارية</h5>
                                @if($person->notes)
                                    <div class="callout callout-warning bg-light">
                                        <p class="mb-0 lead text-dark" style="font-size: 1rem;">{{ $person->notes }}</p>
                                    </div>
                                @else
                                    <div class="alert alert-light border border-dashed text-center">
                                        <i class="fas fa-info-circle mr-1"></i> لا توجد ملاحظات مسجلة لهذا الشخص.
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
                                    <h5 class="text-muted font-weight-bold">لا توجد كوبونات مسجلة لهذا الشخص</h5>
                                </div>
                            @endif
                        </div>

                        {{-- Tab: Family --}}
                        <div class="tab-pane fade" id="tab-family" role="tabpanel">
                            {{-- Spouse Section --}}
                            <h5 class="border-bottom pb-2 font-weight-bold text-dark"><i class="fas fa-heart mr-1 text-danger"></i> بيانات الزوج / الزوجة</h5>
                            
                            @if($person->wife)
                                <div class="media p-3 border rounded bg-white shadow-sm mb-4">
                                    <div class="bg-gray-light p-3 rounded mr-3 text-danger"><i class="fas fa-female fa-2x"></i></div>
                                    <div class="media-body">
                                        <h6 class="font-weight-bold mb-1">{{ $person->wife->getFullName() }} <span class="badge badge-danger ml-2">الزوجة</span></h6>
                                        <div class="text-muted small">
                                            <i class="fas fa-id-card mr-1"></i> الهوية: <strong>{{ $person->wife->id_num }}</strong>
                                            @if($person->wife->isValidId())
                                                <span class="badge badge-success ml-1"><i class="fas fa-check-circle"></i> لونا صحيح</span>
                                            @else
                                                <span class="badge badge-danger ml-1" title="غير مطابق لمعادلة لونا"><i class="fas fa-times-circle"></i> لونا غير صالح</span>
                                            @endif
                                            @if($person->wife->isBanned())
                                                <span class="badge badge-danger ml-1"><i class="fas fa-ban"></i> محظورة (بلوك)</span>
                                            @endif
                                            @if($person->wife->isEmployee())
                                                <span class="badge badge-info ml-1"><i class="fas fa-user-tie"></i> موظفة</span>
                                            @endif
                                            @if($person->wife->isBeneficiary())
                                                <span class="badge badge-primary ml-1"><i class="fas fa-user-check"></i> مستفيدة معتمدة</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @elseif($person->relative_id && $person->relationship == 'زوجة')
                                @php $head = \App\Models\IncompletePerson::where('id_num', $person->relative_id)->first(); @endphp
                                @if($head)
                                    <div class="media p-3 border rounded bg-white shadow-sm mb-4">
                                        <div class="bg-gray-light p-3 rounded mr-3 text-primary"><i class="fas fa-male fa-2x"></i></div>
                                        <div class="media-body">
                                            <h6 class="font-weight-bold mb-1">{{ $head->getFullName() }} <span class="badge badge-primary ml-2">رب العائلة</span></h6>
                                            <div class="text-muted small">
                                                <i class="fas fa-id-card mr-1"></i> الهوية: <strong>{{ $head->id_num }}</strong>
                                                @if($head->isValidId())
                                                    <span class="badge badge-success ml-1"><i class="fas fa-check-circle"></i> لونا صحيح</span>
                                                @else
                                                    <span class="badge badge-danger ml-1" title="غير مطابق لمعادلة لونا"><i class="fas fa-times-circle"></i> لونا غير صالح</span>
                                                @endif
                                                @if($head->isBanned())
                                                    <span class="badge badge-danger ml-1"><i class="fas fa-ban"></i> محظور (بلوك)</span>
                                                @endif
                                                @if($head->isEmployee())
                                                    <span class="badge badge-info ml-1"><i class="fas fa-user-tie"></i> موظف</span>
                                                @endif
                                                @if($head->isBeneficiary())
                                                    <span class="badge badge-primary ml-1"><i class="fas fa-user-check"></i> مستفيد معتمد</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-light border border-dashed text-center mb-4">لا توجد بيانات زوجة مسجلة.</div>
                            @endif

                            {{-- Children Section --}}
                            <h5 class="border-bottom pb-2 font-weight-bold text-dark mt-4"><i class="fas fa-child mr-1 text-info"></i> الأبناء والتابعين للسجل</h5>
                            
                            @php $children = $person->children()->get(); @endphp
                            <div class="row mt-3">
                                @forelse($children as $child)
                                    <div class="col-md-6 mb-3">
                                        <div class="media p-3 border rounded bg-white shadow-sm h-100">
                                            <div class="bg-gray-light px-3 py-2 rounded mr-3 text-info"><i class="fas fa-user"></i></div>
                                            <div class="media-body">
                                                <h6 class="font-weight-bold mb-1">{{ $child->getFullName() }} <span class="badge badge-info ml-2">{{ $child->relationship }}</span></h6>
                                                <div class="text-muted small"><i class="fas fa-id-card mr-1"></i> {{ $child->id_num }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-light border border-dashed text-center">لا يوجد تابعين مسجلين لهذا السجل.</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted small py-3">
                    <i class="far fa-calendar-alt mr-1"></i> تاريخ الإضافة: {{ $person->created_at->format('Y-m-d H:i') }} | 
                    <i class="far fa-clock mr-1"></i> آخر تحديث: {{ $person->updated_at->format('Y-m-d H:i') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden form for single transfer --}}
    <form id="form-single-transfer" action="{{ route('dashboard.incomplete_people.bulk-action') }}" method="GET" style="display: none;">
        <input type="hidden" name="action" value="transfer">
        <input type="hidden" name="ids[]" id="single-transfer-id">
    </form>

    @push('scripts')
    <script>
        function transferSingle(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم نقل هذا السجل بالكامل إلى قاعدة بيانات الأفراد المعتمدة، وسيمكنك بعد ذلك استكمال باقي بياناته التقنية.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، انقل السجل',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('single-transfer-id').value = id;
                    document.getElementById('form-single-transfer').submit();
                }
            })
        }
    </script>
    @endpush
</x-layout>
