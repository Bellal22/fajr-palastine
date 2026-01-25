<x-layout title="بحث عام عن الأفراد" :breadcrumbs="['dashboard.people.index']">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Search Bar --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('dashboard.people.general_search') }}" method="GET" class="form-inline justify-content-center">
                        <div class="input-group input-group-lg w-100">
                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   placeholder="أدخل رقم الهوية للبحث..."
                                   value="{{ request('q') }}"
                                   required
                                   autofocus>
                            <div class="input-group-append">
                                <button class="btn btn-primary px-4" type="submit">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                @if(request()->filled('q'))
                                    <a href="{{ route('dashboard.people.general_search') }}" class="btn btn-outline-secondary px-3 ml-2" title="مسح الفلتر">
                                        <i class="fas fa-times"></i> مسح
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($person)
                {{-- Result Card --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user"></i> نتيجة البحث</h5>
                        <a href="{{ route('dashboard.people.show', $person) }}" class="btn btn-primary btn-sm text-light">
                            <i class="fas fa-external-link-alt"></i> عرض الملف الكامل
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">الاسم الكامل</label>
                                <h5 class="font-weight-bold">{{ $person->first_name }} {{ $person->father_name }} {{ $person->grandfather_name }} {{ $person->family_name }}</h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">رقم الهوية</label>
                                <h5 class="font-weight-bold">{{ $person->id_num }}</h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">رقم الجوال</label>
                                <div>
                                    @if($person->phone)
                                        <a href="tel:{{ $person->phone }}" class="text-success font-weight-bold">
                                            <i class="fas fa-phone"></i> {{ $person->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">الزوجة/الزوج</label>
                                <div>
                                    @php
                                        $spouseName = null;
                                        // إذا كان رباً للأسرة، نبحث عن الزوجة/الزوج في أتباعه
                                        if ($person->wife || $person->spouse) {
                                            $spouseName = $person->getWifeName();
                                        } 
                                        // إذا كانت هي الزوجة أو هو الزوج، نظهر اسم رب الأسرة
                                        elseif ($person->familyHead && in_array($person->relationship, ['زوجة', 'زوج', 'wife', 'husband'])) {
                                            $spouseName = $person->familyHead->first_name . ' ' . $person->familyHead->family_name;
                                        }
                                    @endphp
                                    @if($spouseName)
                                        <span class="font-weight-bold">{{ $spouseName }}</span>
                                    @else
                                        <span class="text-muted">غير مخصص</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="media">
                                    <i class="fas fa-user-tie fa-2x text-success mr-3"></i>
                                    <div class="media-body">
                                        <label class="text-muted small mb-0">مسؤول المنطقة</label>
                                        <div class="font-weight-bold text-dark">
                                            {{ $person->areaResponsible->name ?? 'غير مخصص' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="media">
                                    <i class="fas fa-walking fa-2x text-info mr-3"></i>
                                    <div class="media-body">
                                        <label class="text-muted small mb-0">المندوب</label>
                                        <div class="font-weight-bold text-dark">
                                            {{ $person->block->name ?? 'غير مخصص' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Coupon History --}}
                        <div class="mt-3">
                            <h6 class="font-weight-bold mb-3"><i class="fas fa-ticket-alt text-danger"></i> سجل الكوبونات (للعائلة)</h6>
                            @php
                                $coupons = $person->familyCoupons ?? collect();
                            @endphp

                            @if($coupons->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped border">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>المشروع</th>
                                                <th class="text-center">الكمية</th>
                                                <th class="text-center">الحالة</th>
                                                <th class="text-center">المستلم</th>
                                                <th>التاريخ</th>
                                                <th class="text-center">تفاصيل</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($coupons as $coupon)
                                                <tr>
                                                    <td>{{ $coupon->name }}</td>
                                                    <td class="text-center"><span class="badge badge-primary">{{ $coupon->quantity }}</span></td>
                                                    <td class="text-center">
                                                        @if($coupon->status == 'مستلم')
                                                            <span class="badge badge-success">مستلم</span>
                                                        @else
                                                            <span class="badge badge-secondary">غير مستلم</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center small">
                                                        @if($coupon->person_id == $person->id)
                                                            <span class="text-primary font-weight-bold">نفس الشخص</span>
                                                        @else
                                                            {{ $coupon->recipient_name ?? '-' }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $coupon->delivery_date ? \Carbon\Carbon::parse($coupon->delivery_date)->format('Y-m-d') : '-' }}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-info view-coupon-details"
                                                                data-name="{{ $coupon->name }}"
                                                                data-description="{{ $coupon->project_description ?? 'لا يوجد وصف' }}"
                                                                data-quantity="{{ $coupon->quantity }}"
                                                                data-status="{{ $coupon->status }}"
                                                                data-warehouse="{{ $coupon->warehouse_name ?? '-' }}"
                                                                data-notes="{{ $coupon->notes ?? '-' }}"
                                                                data-date="{{ $coupon->delivery_date ? \Carbon\Carbon::parse($coupon->delivery_date)->format('Y-m-d') : '-' }}"
                                                                data-types="{{ $coupon->coupon_types->toJson() }}"
                                                                data-recipient="{{ $coupon->person_id == $person->id ? 'نفس الشخص' : ($coupon->recipient_name ?? '-') }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-light border text-center py-3">
                                    <i class="fas fa-info-circle"></i> لا يوجد سجل كوبونات لهذه العائلة.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Coupon Details Modal --}}
    <div class="modal fade" id="couponDetailsModal" tabindex="-1" role="dialog" aria-labelledby="couponDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="couponDetailsModalLabel"><i class="fas fa-ticket-alt"></i> تفاصيل الكوبون</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3">
                    {{-- Header: Project Name & Description --}}
                    <div class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-project-diagram text-info mr-2"></i>
                            <h6 class="font-weight-bold mb-0" id="modal-coupon-name"></h6>
                        </div>
                        <p class="text-muted small mb-0 ml-4" id="modal-coupon-description"></p>
                    </div>

                    {{-- Data Grid: 2 Columns for better arrangement --}}
                    <div class="row no-gutters mb-3">
                        <div class="col-6 pr-2 mb-2">
                            <div class="small text-muted mb-1"><i class="fas fa-layer-group fa-fw mr-1 text-secondary"></i> الكمية:</div>
                            <div class="font-weight-bold text-primary ml-4" id="modal-coupon-quantity"></div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="small text-muted mb-1"><i class="fas fa-info-circle fa-fw mr-1 text-secondary"></i> الحالة:</div>
                            <div class="ml-4" id="modal-coupon-status"></div>
                        </div>
                        <div class="col-6 pr-2 mb-2">
                            <div class="small text-muted mb-1"><i class="fas fa-user-check fa-fw mr-1 text-secondary"></i> المستلم:</div>
                            <div class="font-weight-bold text-dark ml-4" id="modal-coupon-recipient"></div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="small text-muted mb-1"><i class="fas fa-calendar-day fa-fw mr-1 text-secondary"></i> التاريخ:</div>
                            <div class="font-weight-bold text-dark ml-4" id="modal-coupon-date"></div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="small text-muted mb-1"><i class="fas fa-map-marker-alt fa-fw mr-1 text-secondary"></i> النقطة / المستودع:</div>
                            <div class="font-weight-bold text-dark ml-4" id="modal-coupon-warehouse"></div>
                        </div>
                        <div class="col-12">
                            <div class="small text-muted mb-1"><i class="fas fa-comment-dots fa-fw mr-1 text-secondary"></i> ملاحظات:</div>
                            <div class="text-secondary ml-4 small" id="modal-coupon-notes"></div>
                        </div>
                    </div>
 house

                    {{-- Coupon Types Table --}}
                    <div id="modal-types-container" style="display: none;">
                        <h6 class="font-weight-bold text-info mb-3">
                            <i class="fas fa-boxes mr-2"></i> تفصيل محتويات الكوبون
                        </h6>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0 shadow-xs">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="15%" class="text-center py-2">#</th>
                                        <th width="55%" class="py-2">نوع الكوبون</th>
                                        <th width="30%" class="text-center py-2">الكمية</th>
                                    </tr>
                                </thead>
                                <tbody id="modal-coupon-types-body" class="bg-white">
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr class="font-weight-bold">
                                        <td colspan="2" class="text-right py-2">الإجمالي الفني للأصناف:</td>
                                        <td class="text-center py-2">
                                            <span class="badge badge-success badge-pill px-3" id="modal-coupon-types-total">0</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <div id="modal-no-types-alert" class="alert alert-warning border-0 bg-light text-center py-4 mb-0" style="display: none; border-radius: 12px;">
                        <i class="fas fa-info-circle fa-2x mb-2 text-warning d-block"></i>
                        <div class="font-weight-bold">لا توجد أنواع محددة</div>
                        <div class="small text-muted">هذا الكوبون لا يحتوي على أصناف فرعية مسجلة</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('.view-coupon-details').on('click', function() {
                var btn = $(this);

                // Fill Modal
                $('#modal-coupon-name').text(btn.data('name'));
                $('#modal-coupon-quantity').text(btn.data('quantity'));
                $('#modal-coupon-recipient').text(btn.data('recipient'));
                $('#modal-coupon-date').text(btn.data('date'));
                $('#modal-coupon-warehouse').text(btn.data('warehouse'));
                $('#modal-coupon-notes').text(btn.data('notes'));
                $('#modal-coupon-description').text(btn.data('description'));

                // Populate Types Table
                var types = btn.data('types');
                var typesBody = $('#modal-coupon-types-body');
                var typesTotal = $('#modal-coupon-types-total');
                typesBody.empty();

                if (types && types.length > 0) {
                    var total = 0;
                    $.each(types, function(index, type) {
                        var row = '<tr>' +
                            '<td class="text-center small">' + (index + 1) + '</td>' +
                            '<td class="small"><i class="fas fa-ticket-alt text-info mr-1"></i> <strong>' + type.name + '</strong></td>' +
                            '<td class="text-center"><span class="badge badge-primary badge-pill">' + type.quantity + '</span></td>' +
                            '</tr>';
                        typesBody.append(row);
                        total += parseInt(type.quantity) || 0;
                    });
                    typesTotal.text(total);
                    $('#modal-types-container').show();
                    $('#modal-no-types-alert').hide();
                } else {
                    $('#modal-types-container').hide();
                    $('#modal-no-types-alert').show();
                }

                // Status Badge
                var status = btn.data('status');
                var statusHtml = '';
                if(status === 'مستلم') {
                    statusHtml = '<span class="badge badge-success px-3 py-2">مستلم</span>';
                } else {
                    statusHtml = '<span class="badge badge-secondary px-3 py-2">غير مستلم</span>';
                }
                $('#modal-coupon-status').html(statusHtml);
                
                // Show Modal
                $('#couponDetailsModal').modal('show');
            });
        });
    </script>
    @endpush
</x-layout>
