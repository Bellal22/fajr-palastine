<x-layout :title="trans('complaints.plural')" :breadcrumbs="['dashboard.complaints.index']">

    {{-- Filters Section --}}
    <div class="mb-3">
        @include('dashboard.complaints.partials.filter')
    </div>

    <div class="card card-primary card-outline card-outline-tabs shadow-sm">
        {{-- Main Status Tabs Header --}}
        <div class="card-header p-0 border-bottom">
            <ul class="nav nav-tabs" id="complaints-status-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $statusTab === 'pending' ? 'active font-weight-bold text-primary' : '' }}"
                       href="{{ route('dashboard.complaints.index', array_merge(request()->query(), ['tab' => 'pending', 'complaint_type_id' => null])) }}">
                        <i class="fas fa-clock mr-1 text-warning"></i> غير مردود عليها (قيد الانتظار)
                        <span class="badge {{ $statusTab === 'pending' ? 'badge-warning text-dark' : 'badge-secondary' }} badge-pill ml-1">{{ count_formatted($pendingCount) }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusTab === 'resolved' ? 'active font-weight-bold text-success' : '' }}"
                       href="{{ route('dashboard.complaints.index', array_merge(request()->query(), ['tab' => 'resolved', 'complaint_type_id' => null])) }}">
                        <i class="fas fa-check-circle mr-1 text-success"></i> الشكاوى المحلولة / المردود عليها
                        <span class="badge {{ $statusTab === 'resolved' ? 'badge-success' : 'badge-secondary' }} badge-pill ml-1">{{ count_formatted($resolvedCount) }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusTab === 'all' ? 'active font-weight-bold text-info' : '' }}"
                       href="{{ route('dashboard.complaints.index', array_merge(request()->query(), ['tab' => 'all', 'complaint_type_id' => null])) }}">
                        <i class="fas fa-layer-group mr-1 text-info"></i> جميع الشكاوى
                        <span class="badge {{ $statusTab === 'all' ? 'badge-info' : 'badge-secondary' }} badge-pill ml-1">{{ count_formatted($allCount) }}</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Toolbar: Action Buttons & Sub-tabs --}}
        <div class="p-3 bg-light border-bottom">
            {{-- Action Buttons --}}
            <div class="d-flex align-items-center flex-wrap mb-2" style="gap: 8px;">
                <div>
                    <button type="button" 
                            class="btn btn-primary btn-sm font-weight-bold shadow-sm"
                            id="btn-bulk-respond"
                            data-toggle="modal" 
                            data-target="#bulkRespondModal">
                        <i class="fas fa-reply-all mr-1"></i> الرد على المحدد (<span id="selected-count">0</span>)
                    </button>
                </div>
                <div>
                    <a href="{{ route('dashboard.complaints.export_resolved', request()->query()) }}" class="btn btn-outline-success btn-sm font-weight-bold bg-white shadow-sm">
                        <i class="fas fa-file-excel text-success mr-1"></i> تصدير التقرير (Excel)
                    </a>
                </div>
                <div>
                    <x-check-all-delete
                        type="{{ \App\Models\Complaint::class }}"
                        :resource="trans('complaints.plural')">
                    </x-check-all-delete>
                </div>
                <div>
                    @include('dashboard.complaints.partials.actions.create')
                </div>
                <div>
                    @include('dashboard.complaints.partials.actions.trashed')
                </div>
            </div>

            {{-- Sub-tabs for Complaint Types (Filter by Type) --}}
            @if($complaintTypes->count() > 0)
                <div class="pt-2 border-top d-flex align-items-center flex-wrap" style="gap: 6px;">
                    <span class="font-weight-bold text-muted mr-2 small">
                        <i class="fas fa-tags text-info"></i> التصنيف حسب نوع الشكوى:
                    </span>
                    <a href="{{ route('dashboard.complaints.index', array_merge(request()->query(), ['tab' => $statusTab, 'complaint_type_id' => null])) }}"
                       class="badge p-2 px-3 my-1 {{ empty($typeId) ? 'badge-primary' : 'badge-white border text-dark' }}" style="font-size: 13px; text-decoration: none;">
                        الكل <span class="badge badge-light ml-1">{{ $statusTab === 'pending' ? $pendingCount : ($statusTab === 'resolved' ? $resolvedCount : $allCount) }}</span>
                    </a>
                    @foreach($complaintTypes as $cType)
                        <a href="{{ route('dashboard.complaints.index', array_merge(request()->query(), ['tab' => $statusTab, 'complaint_type_id' => $cType->id])) }}"
                           class="badge p-2 px-3 my-1 {{ $typeId == $cType->id ? 'badge-primary' : 'badge-white border text-dark' }}" style="font-size: 13px; text-decoration: none;">
                            {{ $cType->name }}
                            @if($statusTab === 'pending')
                                <span class="badge badge-warning ml-1 text-dark">{{ $cType->complaints_count }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr class="bg-light">
                            <th style="width: 50px">
                                <x-check-all></x-check-all>
                            </th>
                            <th><i class="fas fa-id-card"></i> @lang('complaints.attributes.id_num')</th>
                            <th><i class="fas fa-tag"></i> نوع الشكوى</th>
                            <th><i class="fas fa-heading"></i> @lang('complaints.attributes.complaint_title')</th>
                            <th class="d-none d-lg-table-cell"><i class="fas fa-align-left"></i> @lang('complaints.attributes.complaint_text')</th>
                            <th class="d-none d-md-table-cell"><i class="fas fa-calendar"></i> @lang('complaints.attributes.created_at')</th>
                            <th><i class="fas fa-info-circle"></i> الحالة / الرد</th>
                            <th class="text-center" style="width: 120px"><i class="fas fa-cog"></i> الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                            <tr class="align-middle">
                                <td>
                                    <x-check-all-item :model="$complaint"></x-check-all-item>
                                </td>
                                <td>
                                    <a href="{{ route('dashboard.complaints.show', $complaint) }}"
                                       class="text-decoration-none font-weight-bold text-primary">
                                        <i class="fas fa-id-card text-muted"></i>
                                        {{ $complaint->id_num }}
                                    </a>
                                </td>
                                <td>
                                    @if($complaint->complaintType)
                                        <span class="badge badge-info px-2 py-1">
                                            <i class="fas fa-tag"></i> {{ $complaint->complaintType->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">عام</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ Str::limit($complaint->complaint_title, 40) }}</div>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <span class="text-muted">{{ Str::limit($complaint->complaint_text, 50) }}</span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="text-muted">
                                        <i class="fas fa-clock"></i>
                                        {{ $complaint->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td>
                                    @if($complaint->response)
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-check-circle"></i> تم الرد
                                        </span>
                                        <div class="small text-muted mt-1" data-toggle="tooltip" title="{{ $complaint->response }}">
                                            {{ Str::limit($complaint->response, 30) }}
                                        </div>
                                    @else
                                        <span class="badge badge-warning text-dark px-2 py-1">
                                            <i class="fas fa-hourglass-half"></i> قيد الانتظار
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('dashboard.complaints.show', $complaint) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> عرض والرد
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-list fa-3x mb-3 d-block opacity-50"></i>
                                    <h5>لا توجد شكاوى في هذا التبويب حالياً</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    عرض الشكاوى {{ $complaints->firstItem() ?? 0 }} إلى {{ $complaints->lastItem() ?? 0 }} من أصل {{ $complaints->total() }}
                </div>
                @if($complaints->hasPages())
                    <div>
                        {{ $complaints->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bulk Respond Modal --}}
    <div class="modal fade" id="bulkRespondModal" tabindex="-1" role="dialog" aria-labelledby="bulkRespondModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius: 12px;">
                <div class="modal-header bg-primary text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title font-weight-bold" id="bulkRespondModalLabel">
                        <i class="fas fa-reply-all mr-2"></i> الرد الجماعي على الشكاوى المحددة
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('dashboard.complaints.bulk_respond') }}" method="POST" id="bulk-respond-form">
                    @csrf
                    <div id="bulk-respond-hidden-inputs"></div>

                    <div class="modal-body p-4">
                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="fas fa-info-circle fa-2x mr-3"></i>
                            <div>
                                <strong>تنبيه:</strong> سيتم تطبيق هذا الرد وتغيير الحالة لجميع الشكاوى المحددة وعددهم:
                                <span class="badge badge-primary badge-pill px-3 py-1 font-weight-bold" style="font-size: 14px;" id="modal-selected-count">0</span> شكوى.
                            </div>
                        </div>

                        @if(isset($responseTemplates) && $responseTemplates->count() > 0)
                            <div class="mb-3 p-3 border rounded bg-light">
                                <label class="text-primary font-weight-bold small d-block mb-1">
                                    <i class="fas fa-magic mr-1"></i> اختيار من الردود الجاهزة (تعبئة تلقائية):
                                </label>
                                <select id="bulk_response_template_select" class="form-control form-control-sm">
                                    <option value="">-- اختر رداً جاهزاً لتعبئة النموذج تلقائياً --</option>
                                    @foreach($responseTemplates as $template)
                                        <option value="{{ $template->id }}"
                                                data-content="{{ e($template->content) }}"
                                                data-status="{{ $template->status }}">
                                            {{ $template->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark small mb-1">
                                    <i class="fas fa-flag text-warning mr-1"></i> حالة الشكاوى بعد الرد <span class="text-danger">*</span>
                                </label>
                                <select name="status" class="form-control" required>
                                    <option value="resolved" selected>محلولة / تم الرد (resolved)</option>
                                    <option value="in_progress">قيد المعالجة (in_progress)</option>
                                    <option value="pending">قيد الانتظار (pending)</option>
                                    <option value="rejected">مرفوضة (rejected)</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark small mb-1">
                                    <i class="fas fa-calendar-alt text-info mr-1"></i> تاريخ ووقت الرد
                                </label>
                                <input type="datetime-local" 
                                       name="responded_at" 
                                       class="form-control" 
                                       value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="font-weight-bold text-dark small mb-1">
                                <i class="fas fa-heading text-primary mr-1"></i> عنوان أو ملخص الحل (اختياري)
                            </label>
                            <input type="text" 
                                   name="response_title" 
                                   class="form-control" 
                                   placeholder="مثال: تم المتابعة وتم تنفيذ الطلب...">
                        </div>

                        <div class="mb-3">
                            <label class="font-weight-bold text-dark small mb-1">
                                <i class="fas fa-comment-alt text-success mr-1"></i> نص الرد الموحد على الشكاوى المحددة <span class="text-danger">*</span>
                            </label>
                            <textarea name="response" 
                                      rows="5" 
                                      class="form-control" 
                                      placeholder="أدخل نص الرد الموحد الذي سيصل لجميع الشكاوى المحددة..." 
                                      required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary font-weight-bold" id="submit-bulk-respond">
                            <i class="fas fa-paper-plane mr-1"></i> إرسال وحفظ الرد الموحد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function updateSelectedCount() {
                const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
                const count = checkedBoxes.length;
                const countSpan = document.getElementById('selected-count');
                const modalCountSpan = document.getElementById('modal-selected-count');
                
                if (countSpan) countSpan.textContent = count;
                if (modalCountSpan) modalCountSpan.textContent = count;
            }

            document.addEventListener('change', function (e) {
                if (e.target && (e.target.classList.contains('item-checkbox') || e.target.classList.contains('check-all') || e.target.id === 'check-all')) {
                    setTimeout(updateSelectedCount, 50);
                }
            });

            $('#bulkRespondModal').on('show.bs.modal', function (e) {
                const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
                const hiddenInputsContainer = document.getElementById('bulk-respond-hidden-inputs');
                hiddenInputsContainer.innerHTML = '';

                if (checkedBoxes.length === 0) {
                    alert('يرجى تحديد شكوى واحدة على الأقل من الجدول أولاً للرد عليها جماعياً.');
                    e.preventDefault();
                    return false;
                }

                checkedBoxes.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'items[]';
                    input.value = cb.value;
                    hiddenInputsContainer.appendChild(input);
                });

                updateSelectedCount();
            });

            const templateSelect = document.getElementById('bulk_response_template_select');
            const responseTextarea = document.querySelector('#bulk-respond-form textarea[name="response"]');
            const statusSelect = document.querySelector('#bulk-respond-form select[name="status"]');

            if (templateSelect && responseTextarea) {
                templateSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        const content = selectedOption.getAttribute('data-content');
                        const status = selectedOption.getAttribute('data-status');

                        if (content) {
                            responseTextarea.value = content;
                        }
                        if (status && statusSelect) {
                            statusSelect.value = status;
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-layout>
