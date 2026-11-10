<x-layout title="البيانات غير المكتملة" :breadcrumbs="['dashboard.incomplete_people.index']">

    {{-- Import Section --}}
    <div class="mb-3">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0">
                    <i class="fas fa-file-upload text-primary"></i> استيراد بيانات من إكسل
                </h6>
                @if(session('last_incomplete_skipped_report') && count(session('last_incomplete_skipped_report')) > 0)
                    <a href="{{ route('dashboard.incomplete_people.export-skipped') }}" class="btn btn-warning btn-xs font-weight-bold shadow-sm">
                        <i class="fas fa-file-excel mr-1"></i> تحميل كشف المستبعدين الأخير ({{ count(session('last_incomplete_skipped_report')) }} سجل)
                    </a>
                @endif
            </div>
            <div class="card-body py-3">
                <form action="{{ route('dashboard.incomplete_people.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="text-muted small mb-1">اختر ملف الإكسل <small class="text-danger">(الهوية بالعمود 1 وتخضع لمعادلة لونا، والجوال يبدأ بـ 059 أو 056 ويليه 7 أرقام، ويُمنع المحظورون والموظفون والمستفيدون)</small></label>
                                <input type="file" name="excel_file" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="col-md-3 mt-2 mt-md-0">
                            <button type="submit" class="btn btn-primary btn-block btn-sm">
                                <i class="fas fa-upload"></i> بدء الاستيراد
                            </button>
                        </div>
                        <div class="col-md-3 mt-2 mt-md-0">
                            <a href="{{ route('dashboard.incomplete_people.download-template') }}"
                               class="btn btn-outline-success btn-block btn-sm">
                                <i class="fas fa-file-excel"></i> تحميل ملف النموذج
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- تنبيه كشف السجلات المستبعدة إن وجد --}}
    @if(session('last_incomplete_skipped_report') && count(session('last_incomplete_skipped_report')) > 0)
        <div class="alert alert-warning border-warning shadow-sm mb-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="mb-2 mb-md-0">
                    <h6 class="alert-heading font-weight-bold mb-1 text-dark">
                        <i class="fas fa-exclamation-triangle text-danger mr-1"></i>
                        تم استبعاد وتخطي ({{ count(session('last_incomplete_skipped_report')) }}) سجل خلال عملية الاستيراد الأخيرة
                    </h6>
                    <p class="mb-0 text-muted small">
                        تم تخطي هذه السجلات لعدم استيفائها الشروط (أخطاء لونا برقم الهوية، رقم جوال غير صالح، أو لوجود السجل في قائمة البلوكات/الموظفين/المستفيدين/مكرر). تم تجهيز إفادة تفصيلية بكافة أسباب الاستبعاد لكل سطر.
                    </p>
                </div>
                <div>
                    <a href="{{ route('dashboard.incomplete_people.export-skipped') }}" class="btn btn-danger font-weight-bold shadow-sm">
                        <i class="fas fa-file-download mr-1"></i> تحميل كشف الإفادة بالمستبعدين (Excel)
                    </a>
                </div>
            </div>
        </div>
    @endif


    {{-- Filters Section --}}
    <div class="mb-3">
        <form action="{{ route('dashboard.incomplete_people.index') }}" method="GET" id="filter-form">

            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-filter"></i> فلترة البيانات غير المكتملة
                @endslot

                <div class="row">

                    {{-- ===== بحث بالهويات ===== --}}
                    <div class="col-12 mb-3">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0"><i class="fas fa-id-card"></i> البحث برقم الهوية</h6>
                            </div>
                            <div class="card-body">
                                <label class="font-weight-bold small">
                                    <i class="fas fa-keyboard"></i> أدخل أرقام الهويات (رقم واحد أو أكثر، كل رقم في سطر منفصل)
                                </label>
                                <textarea name="id_nums"
                                          id="id_nums_input"
                                          class="form-control"
                                          style="height: 80px; min-height: 80px; max-height: 200px; resize: vertical;"
                                          placeholder="مثال:&#10;123456789&#10;987654321">{{ request('id_nums') }}</textarea>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    يمكنك إدخال أرقام متعددة، سيتم البحث عن كل منها (الرقم يُكمَّل تلقائياً لـ 9 أرقام)
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- ===== بحث بالاسم ===== --}}
                    <div class="col-12 mb-3">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0"><i class="fas fa-user-circle"></i> البحث بالاسم</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label class="font-weight-bold small"><i class="fas fa-user"></i> الاسم الأول</label>
                                        <input type="text"
                                               name="first_name"
                                               class="form-control form-control-sm"
                                               placeholder="الاسم الأول..."
                                               value="{{ request('first_name') }}">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="font-weight-bold small"><i class="fas fa-user"></i> اسم الأب</label>
                                        <input type="text"
                                               name="father_name"
                                               class="form-control form-control-sm"
                                               placeholder="اسم الأب..."
                                               value="{{ request('father_name') }}">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="font-weight-bold small"><i class="fas fa-user"></i> اسم الجد</label>
                                        <input type="text"
                                               name="grandfather_name"
                                               class="form-control form-control-sm"
                                               placeholder="اسم الجد..."
                                               value="{{ request('grandfather_name') }}">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="font-weight-bold small"><i class="fas fa-users"></i> اسم العائلة</label>
                                        <input type="text"
                                               name="family_name"
                                               class="form-control form-control-sm"
                                               placeholder="اسم العائلة..."
                                               value="{{ request('family_name') }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="font-weight-bold small"><i class="fas fa-phone"></i> رقم الجوال</label>
                                        <input type="text"
                                               name="phone"
                                               class="form-control form-control-sm"
                                               placeholder="رقم الجوال..."
                                               value="{{ request('phone') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== فحص وتدقيق البيانات ===== --}}
                    <div class="col-12 mb-3">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0"><i class="fas fa-check-double"></i> تدقيق البيانات (معادلة لونا والجوال)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="font-weight-bold small"><i class="fas fa-id-card"></i> فحص صحة رقم الهوية (معادلة لونا)</label>
                                        <select name="id_status" class="form-control form-control-sm">
                                            <option value="">-- عرض الكل --</option>
                                            <option value="valid" {{ request('id_status') == 'valid' ? 'selected' : '' }}>هويات صحيحة فقط (مطابقة لمعادلة لونا)</option>
                                            <option value="invalid" {{ request('id_status') == 'invalid' ? 'selected' : '' }}>هويات غير صحيحة (تحتاج تصحيح)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="font-weight-bold small"><i class="fas fa-mobile-alt"></i> فحص رقم الجوال (يبدأ بـ 059 أو 056)</label>
                                        <select name="phone_status" class="form-control form-control-sm">
                                            <option value="">-- عرض الكل --</option>
                                            <option value="valid" {{ request('phone_status') == 'valid' ? 'selected' : '' }}>أرقام جوال صحيحة (059 / 056 + 7 أرقام)</option>
                                            <option value="invalid" {{ request('phone_status') == 'invalid' ? 'selected' : '' }}>أرقام جوال غير صحيحة أو مفقودة</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== إعدادات العرض ===== --}}
                    <div class="col-12 mb-3">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0"><i class="fas fa-cog"></i> إعدادات العرض</h6>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label class="font-weight-bold small">
                                             <i class="fas fa-list-ol"></i> عدد السجلات في الصفحة
                                        </label>
                                        <input type="number"
                                               name="perPage"
                                               class="form-control form-control-sm"
                                               value="{{ request('perPage', 500) }}"
                                               min="1"
                                               max="1000">
                                        <small class="text-muted">الحد الأقصى: 1000 سجل</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                @slot('footer')
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> بحث وتصفية
                    </button>
                    <button type="button" class="btn btn-secondary ml-2" id="resetFilters">
                        <i class="fas fa-eraser"></i> مسح الفلاتر
                    </button>
                    @if(request()->hasAny(['id_nums','first_name','father_name','grandfather_name','family_name','phone','id_status','phone_status']))
                        <span class="badge badge-warning ml-3 p-2">
                            <i class="fas fa-filter"></i> يوجد فلاتر مفعّلة
                        </span>
                    @endif
                @endslot
            @endcomponent

        </form>
    </div>

    {{-- Main Table --}}
    <form id="bulk-action-form" action="{{ route('dashboard.incomplete_people.bulk-action') }}" method="GET">

        @component('dashboard::components.table-box')
            @slot('title')
                <i class="fas fa-user-clock"></i> قائمة البيانات غير المكتملة
                <span class="badge badge-warning badge-pill">{{ count_formatted($people->total()) }}</span>
            @endslot

            <thead>
            <tr>
                <th colspan="100">
                    <div class="d-flex align-items-center flex-wrap justify-content-between">
                        <div class="d-flex align-items-center flex-wrap">

                            {{-- Select All Checkbox (manual since no x-component for incomplete) --}}
                            <div class="mr-3 d-flex align-items-center">
                                <input type="checkbox" id="select-all" class="mr-1" title="تحديد الكل">
                                <label for="select-all" class="mb-0 text-muted small">تحديد الكل</label>
                            </div>

                            {{-- Transfer to Persons --}}
                            <div class="mr-2">
                                <button type="submit"
                                        name="action"
                                        value="transfer"
                                        class="btn btn-success btn-sm bulk-btn"
                                        id="transfer-btn"
                                        disabled
                                        onclick="return confirm('هل أنت متأكد من نقل المحددين إلى جدول الأفراد؟')">
                                    <i class="fas fa-exchange-alt mr-1"></i> نقل للأفراد
                                </button>
                            </div>

                            {{-- Delete Selected --}}
                            <div class="mr-2">
                                <button type="submit"
                                        name="action"
                                        value="delete"
                                        class="btn btn-danger btn-sm bulk-btn"
                                        id="delete-btn"
                                        disabled
                                        onclick="return confirm('هل أنت متأكد من حذف المحددين نهائياً؟')">
                                    <i class="fas fa-trash-alt mr-1"></i> حذف المحددين
                                </button>
                            </div>

                        </div>

                        <div class="d-flex align-items-center">
                            <span class="text-muted small">
                                <i class="fas fa-info-circle"></i>
                                إجمالي السجلات: <strong>{{ $people->total() }}</strong>
                            </span>
                        </div>
                    </div>
                </th>
            </tr>

            {{-- Column Headers --}}
            <tr class="bg-light">
                <th style="width: 50px">
                    <input type="checkbox" id="select-all-header">
                </th>
                <th><i class="fas fa-id-card"></i> هوية رب الأسرة</th>
                <th><i class="fas fa-user"></i> اسم رب الأسرة</th>
                <th class="d-none d-lg-table-cell"><i class="fas fa-phone"></i> الجوال</th>
                <th class="d-none d-md-table-cell"><i class="fas fa-id-card text-pink"></i> هوية الزوجة</th>
                <th class="d-none d-md-table-cell"><i class="fas fa-female"></i> اسم الزوجة</th>
                <th class="d-none d-xl-table-cell"><i class="fas fa-calendar-alt"></i> تاريخ الإضافة</th>
                <th class="text-center" style="width: 100px"><i class="fas fa-cog"></i> الإجراءات</th>
            </tr>
            </thead>

            <tbody>
            @forelse($people as $person)
                <tr class="align-middle">
                    <td>
                        <input type="checkbox" name="ids[]" value="{{ $person->id }}" class="item-checkbox">
                    </td>

                    {{-- ID --}}
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('dashboard.incomplete_people.show', $person->id) }}" class="font-weight-bold text-primary mr-1">
                                <i class="fas fa-id-card text-muted"></i>
                                {{ $person->id_num }}
                            </a>
                            @if($person->isValidId())
                                <span class="badge badge-light text-success border border-success" title="رقم الهوية مطابق لمعادلة لونا">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            @else
                                <span class="badge badge-danger" title="رقم الهوية غير مطابق لمعادلة لونا">
                                    <i class="fas fa-exclamation-triangle"></i> لونا خطأ
                                </span>
                            @endif
                        </div>
                    </td>

                    {{-- Full Name --}}
                    <td>
                        <div class="font-weight-bold">
                            {{ $person->first_name }} {{ $person->father_name }}
                            {{ $person->grandfather_name }} {{ $person->family_name }}
                        </div>
                        <small class="text-muted d-lg-none">
                            <i class="fas fa-phone"></i> {{ $person->phone ?? '-' }}
                            @if($person->phone && !$person->isValidPhone())
                                <span class="badge badge-warning text-dark ml-1">جوال غير مطابق</span>
                            @endif
                        </small>
                    </td>

                    {{-- Phone --}}
                    <td class="d-none d-lg-table-cell">
                        @if($person->phone)
                            <div class="d-flex align-items-center">
                                <a href="tel:{{ $person->phone }}" class="text-decoration-none text-success mr-1">
                                    <i class="fas fa-phone"></i> {{ $person->phone }}
                                </a>
                                @if($person->isValidPhone())
                                    <span class="badge badge-light text-success border border-success" title="رقم الجوال صحيح (يبدأ بـ 059 أو 056 ومتبوعاً بـ 7 أرقام)">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                @else
                                    <span class="badge badge-warning text-dark" title="رقم الجوال غير صالح (يجب أن يبدأ بـ 059 أو 056 ومتبوعاً بـ 7 أرقام)">
                                        <i class="fas fa-exclamation-circle"></i> غير مطابق
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Wife ID --}}
                    <td class="d-none d-md-table-cell">
                        @if($person->wife)
                            <div class="d-flex align-items-center">
                                <span class="text-info mr-1">
                                    <i class="fas fa-id-card"></i>
                                    {{ $person->wife->id_num }}
                                </span>
                                @if($person->wife->isValidId())
                                    <span class="badge badge-light text-success border border-success" title="هوية الزوجة مطابقة لمعادلة لونا">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                @else
                                    <span class="badge badge-danger" title="هوية الزوجة غير مطابقة لمعادلة لونا">
                                        <i class="fas fa-exclamation-triangle"></i> خطأ
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Wife Name --}}
                    <td class="d-none d-md-table-cell">
                        @if($person->wife)
                            <span>{{ $person->wife->getFullName() }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Created At --}}
                    <td class="d-none d-xl-table-cell">
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $person->created_at->format('Y-m-d') }}
                        </small>
                    </td>

                    {{-- Actions Dropdown --}}
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                    type="button"
                                    id="dropdownMenuButton{{ $person->id }}"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right"
                                 aria-labelledby="dropdownMenuButton{{ $person->id }}">
                                {{-- Show single --}}
                                <a class="dropdown-item text-primary" href="{{ route('dashboard.incomplete_people.show', $person->id) }}">
                                    <i class="fas fa-eye text-primary"></i>
                                    <span class="mr-2">عرض التفاصيل</span>
                                </a>


                                {{-- Transfer single --}}
                                <button type="button"
                                        class="dropdown-item text-success"
                                        onclick="transferSingle({{ $person->id }})">
                                    <i class="fas fa-exchange-alt text-success"></i>
                                    <span class="mr-2">نقل للأفراد</span>
                                </button>

                                <div class="dropdown-divider"></div>

                                {{-- Delete --}}
                                <form action="{{ route('dashboard.incomplete_people.destroy', $person->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('هل أنت متأكد؟ سيتم حذف رب الأسرة والزوجة المرتبطة به')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-trash text-danger"></i>
                                        <span class="mr-2">حذف العائلة</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="100" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-user-clock fa-3x mb-3 d-block"></i>
                            <h5>لا توجد بيانات غير مكتملة</h5>
                            <p class="mb-0">يمكنك استيراد بيانات من ملف إكسل أعلاه</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>

            {{-- Pagination --}}
            @if($people->hasPages())
                @slot('footer')
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            عرض {{ $people->firstItem() ?? 0 }} - {{ $people->lastItem() ?? 0 }}
                            من إجمالي {{ $people->total() }} سجل
                        </div>
                        {{ $people->appends(request()->query())->links() }}
                    </div>
                @endslot
            @endif
        @endcomponent

    </form>

    {{-- Hidden form for single transfer --}}
    <form id="form-single-transfer"
          action="{{ route('dashboard.incomplete_people.bulk-action') }}"
          method="GET"
          style="display: none;">
        <input type="hidden" name="action" value="transfer">
        <input type="hidden" name="ids[]" id="single-transfer-id">
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            const transferBtn = document.getElementById('transfer-btn');
            const deleteBtn = document.getElementById('delete-btn');

            function updateButtons() {
                const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                if (transferBtn) transferBtn.disabled = checkedCount === 0;
                if (deleteBtn) deleteBtn.disabled = checkedCount === 0;
            }

            // Select All (header checkbox)
            ['select-all', 'select-all-header'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', function () {
                        checkboxes.forEach(cb => cb.checked = this.checked);
                        // Sync both checkboxes
                        ['select-all', 'select-all-header'].forEach(otherId => {
                            const other = document.getElementById(otherId);
                            if (other && other !== this) other.checked = this.checked;
                        });
                        updateButtons();
                    });
                }
            });

            checkboxes.forEach(cb => cb.addEventListener('change', updateButtons));

            // Reset filters
            document.getElementById('resetFilters')?.addEventListener('click', function () {
                document.getElementById('filter-form').reset();
                history.pushState({}, document.title, window.location.pathname);
                location.reload();
            });

            // Auto-resize ID textarea
            const idTextarea = document.getElementById('id_nums_input');
            if (idTextarea) {
                idTextarea.addEventListener('input', function () {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 300) + 'px';
                });
            }
        });

        function transferSingle(id) {
            if (!confirm('هل أنت متأكد من نقل هذا السجل إلى جدول الأفراد؟')) return;
            document.getElementById('single-transfer-id').value = id;
            document.getElementById('form-single-transfer').submit();
        }
    </script>
    @endpush

</x-layout>
