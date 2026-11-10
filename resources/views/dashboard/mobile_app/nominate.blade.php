<x-layout :title="'تكليف الموزعين'" :breadcrumbs="['dashboard.mobile_app.nominate']">
    <div class="row">
        <div class="col-md-4">
            <form action="{{ route('dashboard.mobile_app.nominate.store') }}" method="POST">
                @csrf
                @component('dashboard::components.box')
                    @slot('title', 'إضافة تكليفات جديدة')

                    @if(session('import_errors'))
                        <div class="alert alert-danger p-2 mb-3">
                            <ul class="mb-0 small">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="fw-bold">المشروع المستهدف <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-control select2" required>
                            <option value="">-- اختر المشروع --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ ($selectedProjectId == $project->id) ? 'selected' : '' }}>{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="fw-bold">الموزع المكلف <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-control select2" required>
                            <option value="">-- اختر الموزع --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ ($selectedUserId == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="fw-bold">نقطة التوزيع (المخزن الفرعي) <span class="text-danger">*</span></label>
                        <select name="sub_warehouse_id" class="form-control select2" required>
                            <option value="">-- اختر المخزن --</option>
                            @foreach($subWarehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('sub_warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="fw-bold">أرقام الهويات <span class="text-danger">*</span></label>
                        <textarea name="id_nums" rows="6" class="form-control" placeholder="ضع أرقام الهويات هنا، كل رقم في سطر منفصل..." required>{{ old('id_nums') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="fw-bold">تاريخ فحص التعارض (من تاريخ الاستلام)</label>
                        <input type="date" name="conflict_date" class="form-control" value="{{ old('conflict_date') }}">
                        <small class="d-block text-muted mt-1">
                            <i class="fas fa-info-circle me-1"></i> عند تحديد تاريخ، لن يتم إضافة أي شخص استلم مساعدات بدءاً من هذا التاريخ فصاعداً (إلا إذا تم تفعيل خيار التجاوز أدناه).
                        </small>
                    </div>

                    <div class="form-group mb-0 mt-2">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="skip_conflict" class="custom-control-input" id="skip_conflict" value="1">
                            <label class="custom-control-label fw-bold text-danger" for="skip_conflict">
                                <i class="fas fa-exclamation-triangle me-1"></i> تجاوز فحص التعارض وطلب إعادة الاستحقاق لهذا المشروع
                            </label>
                            <small class="d-block text-muted mt-1">
                                <i class="fas fa-info-circle me-1"></i> عند التفعيل، سيتم إضافة المستفيد حتى لو كان مضافاً مسبقاً في هذا المشروع أو مشاريع متعارضة، مما يتيح له الاستلام مرة أخرى.
                            </small>
                        </div>
                    </div>

                    @slot('footer')
                        <button type="submit" class="btn btn-primary btn-block py-2 fw-bold">
                            <i class="fas fa-check-circle me-1"></i> حفظ وتكليف الموزع
                        </button>
                    @endslot
                @endcomponent
            </form>
        </div>

        <div class="col-md-8">
            <div class="mb-3 bg-white p-3 border rounded shadow-sm">
                <form action="{{ route('dashboard.mobile_app.nominate') }}" method="GET">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <select name="project_id" class="form-control select2" onchange="this.form.submit()">
                                <option value="">-- عرض حسب المشروع --</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ $selectedProjectId == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="user_id" class="form-control select2" onchange="this.form.submit()">
                                <option value="">-- عرض حسب الموزع --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                                <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" onchange="this.form.submit()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('dashboard.mobile_app.nominate') }}" class="btn btn-default btn-block">تصفير</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <textarea name="search_id_nums" class="form-control" rows="2" placeholder="بحث بمجموعة أرقام هويات (مفصولة بمسافة أو سطر جديد)...">{{ request('search_id_nums') }}</textarea>
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <button type="submit" class="btn btn-primary btn-block h-100"><i class="fas fa-search mb-1 d-block"></i> بحث</button>
                        </div>
                    </div>
                </form>
            </div>

            <form action="{{ route('dashboard.mobile_app.nominate.bulk_action') }}" method="POST" id="bulk-action-form">
                @csrf
                <input type="hidden" name="action_type" id="bulk-action-type" value="">
                
                <!-- Modal التحويل الجماعي -->
                <div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content" style="direction: rtl; text-align: right;">
                            <div class="modal-header" style="border-bottom: 1px solid #e9ecef;">
                                <h5 class="modal-title fw-bold" style="float: right;"><i class="fas fa-exchange-alt text-primary ml-2"></i> نقل التكليفات المحددة</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float: left; margin: -1rem auto -1rem -1rem;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <div class="clearfix"></div>
                            </div>
                            <div class="modal-body pr-4 pl-4">
                                <div class="form-group mb-3">
                                    <label class="fw-bold w-100">نقل إلى مشروع:</label>
                                    <select name="transfer_project_id" class="form-control select2" style="width: 100%">
                                        <option value="">-- اختر المشروع --</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="fw-bold w-100">نقل إلى موزع:</label>
                                    <select name="transfer_user_id" class="form-control select2" style="width: 100%">
                                        <option value="">-- اختر الموزع --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="fw-bold w-100">إلى مخزن:</label>
                                    <select name="transfer_sub_warehouse_id" class="form-control select2" style="width: 100%">
                                        <option value="">-- اختر المخزن --</option>
                                        @foreach($subWarehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer" style="justify-content: flex-end; border-top: 1px solid #e9ecef;">
                                <button type="button" class="btn btn-secondary text-dark" data-dismiss="modal">إلغاء</button>
                                <button type="button" class="btn btn-primary" onclick="submitBulkAction('transfer')">تأكيد النقل</button>
                            </div>
                        </div>
                    </div>
                </div>

                @component('dashboard::components.table-box')
                    @slot('title')
                        <i class="fas fa-list-ul"></i> التكليفات الحالية
                        <span class="badge badge-success badge-pill ml-2">{{ $currentAssignments->total() }}</span>
                    @endslot

                    @slot('tools')
                        <div class="d-flex flex-wrap align-items-center">
                            @if($currentAssignments->total() > 0)
                                <div class="btn-group mr-3">
                                    <button type="button" class="btn btn-sm btn-primary bulk-action-btn font-weight-bold" disabled data-toggle="modal" data-target="#transferModal">
                                        <i class="fas fa-exchange-alt mr-1"></i> تحويل المحدد
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger bulk-action-btn font-weight-bold" disabled onclick="submitBulkAction('delete')">
                                        <i class="fas fa-trash-alt mr-1"></i> حذف المحدد
                                    </button>
                                </div>
                            @endif

                            @if($selectedProjectId && $selectedUserId && $currentAssignments->total() > 0)
                                <button formnovalidate formaction="{{ route('dashboard.mobile_app.projects.refresh', $selectedProjectId) }}" formmethod="POST" onclick="return confirm('هل تريد إرسال إشارة تحديث لتطبيق الجوال لهذا المشروع؟')" class="btn btn-sm btn-outline-primary mr-2" title="تحديث بيانات المشروع للتطبيق">
                                    <i class="fas fa-sync"></i> تحديث للمشروع
                                </button>
                                <a href="{{ route('dashboard.mobile_app.nominate.export', ['project_id' => $selectedProjectId, 'user_id' => $selectedUserId]) }}" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-file-excel"></i> تصدير Excel
                                </a>
                            @endif
                        </div>
                    @endslot

                    <thead dir="rtl">
                    <tr class="bg-light">
                        <th style="width: 40px" class="text-center">
                            <div class="custom-control custom-checkbox mx-auto">
                                <input type="checkbox" class="custom-control-input" id="check-all" onchange="checkAllItems(this)">
                                <label class="custom-control-label" for="check-all"></label>
                            </div>
                        </th>
                        <th class="text-right" style="text-align: right !important">اسم المستفيد</th>
                        <th class="text-right" style="text-align: right !important">رقم الهوية</th>
                        <th class="text-right" style="text-align: right !important">المشروع / الموزع</th>
                        <th class="text-center" style="width: 130px">تاريخ التكليف</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center" style="width: 60px">إبطال</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($currentAssignments as $assignment)
                        <tr class="align-middle">
                            <td class="text-center">
                                <div class="custom-control custom-checkbox mx-auto">
                                    <input type="checkbox" name="selected_ids[]" value="{{ $assignment->id }}" class="custom-control-input item-checkbox" id="check-{{ $assignment->id }}">
                                    <label class="custom-control-label" for="check-{{ $assignment->id }}"></label>
                                </div>
                            </td>
                            <td class="text-right" style="text-align: right !important" dir="rtl">{{ $assignment->first_name }} {{ $assignment->family_name }}</td>
                            <td class="text-right" style="text-align: right !important" dir="ltr"><code class="text-dark font-weight-bold" style="font-size: 1.1em">{{ $assignment->id_num }}</code></td>
                            <td class="text-right" style="text-align: right !important" dir="rtl">
                                <div class="small fw-bold text-primary">{{ $assignment->project_name }}</div>
                                <div class="small text-muted"><i class="fas fa-user-tag ml-1"></i>{{ $assignment->user_name ?? 'بدون مكلف' }}</div>
                            </td>
                            <td class="text-center small">
                                <div class="font-weight-bold">{{ \Carbon\Carbon::parse($assignment->created_at)->format('Y-m-d') }}</div>
                                <div class="text-muted" style="font-size: 0.85em">{{ \Carbon\Carbon::parse($assignment->created_at)->format('H:i') }}</div>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusColor = $assignment->status == 'مستلم' ? 'success' : 'warning';
                                @endphp
                                <span class="badge badge-{{ $statusColor }} px-2 py-1">{{ $assignment->status }}</span>
                            </td>
                            <td class="text-center">
                                <button formnovalidate formaction="{{ route('dashboard.mobile_app.nominate.unassign', $assignment->id) }}" formmethod="POST" onclick="return confirm('هل أنت متأكد من إلغاء تكليف هذا المستفيد؟')" class="btn btn-xs btn-outline-danger" title="إلغاء التكليف فقط">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-search fa-2x text-muted mb-2 d-block"></i>
                                <div class="text-muted">اختر المشروع والموزع أو ابحث بأرقام الهويات لعرض التكليفات</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                @endcomponent

                <div class="d-flex justify-content-center mt-3">
                    {{ $currentAssignments->links() }}
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleBulkButtons() {
            var checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
            var buttons = document.querySelectorAll('.bulk-action-btn');
            buttons.forEach(function(btn) {
                btn.disabled = checkedCount === 0;
            });
        }

        function checkAllItems(source) {
            document.querySelectorAll('.item-checkbox').forEach(c => c.checked = source.checked);
            toggleBulkButtons();
        }

        document.querySelectorAll('.item-checkbox').forEach(c => c.addEventListener('change', toggleBulkButtons));

        function submitBulkAction(action) {
            if(document.querySelectorAll('.item-checkbox:checked').length === 0) {
                alert('يرجى تحديد عنصر واحد على الأقل ليتم تطبيق الإجراء عليه.');
                return;
            }
            if(action === 'delete' && !confirm('هل أنت متأكد من حذف عمليات التكليف المحددة؟ لا يمكن التراجع عن هذا الإجراء.')) {
                return;
            }
            if(action === 'transfer' && !confirm('هل أنت متأكد من تحويل التكليفات المحددة للوجهة الجديدة؟')) {
                return;
            }
            document.getElementById('bulk-action-type').value = action;
            document.getElementById('bulk-action-form').submit();
        }
    </script>
    @endpush
</x-layout>
