<x-layout :title="trans('need_requests.plural')" :breadcrumbs="['dashboard.need_requests.index']">
    @include('dashboard.need_requests.partials.filter')
    @push('styles')
    <style>
        .badge-light-primary {
            background-color: rgba(115, 103, 240, 0.12) !important;
            color: #7367f0 !important;
        }
        .timer-text {
            font-family: 'monospace', sans-serif;
            font-weight: 600;
        }
    </style>
    @endpush

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('need_requests.actions.list') ({{ $need_requests->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex flex-wrap align-items-center justify-content-between w-100">
                {{-- Left Side: Management & Creation --}}
                <div class="d-flex align-items-center mb-1">
                    <div class="d-flex align-items-center">
                        @include('dashboard.need_requests.partials.actions.create')
                        
                        @if(auth()->user()?->isAdmin())
                            <a href="{{ route('dashboard.need_requests.bulk_create') }}" class="btn btn-info btn-sm ml-2">
                                <i class="fas fa-layer-group"></i> تفعيل الترشيح
                            </a>
                        @endif

                        <a href="{{ route('dashboard.need_requests.available') }}" class="btn btn-primary btn-sm ml-2">
                            <i class="fas fa-bullhorn"></i> الفرص المتاحة
                        </a>

                        @if(auth()->user()?->isAdmin())
                            <a href="{{ route('dashboard.need_requests.available') }}" class="btn btn-dark btn-sm ml-2">
                                <i class="fas fa-history"></i> سجل التفعيلات
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Right Side: Export & Bulk Actions --}}
                <div class="d-flex align-items-center mb-1">
                    <div class="btn-group mr-2">
                        <a href="{{ route('dashboard.need_requests.export_all') }}?{{ http_build_query(request()->all()) }}" class="btn btn-success btn-sm">
                             <i class="fas fa-file-excel"></i> تصدير الكل
                        </a>
                        <button class="btn btn-outline-success btn-sm" 
                                id="export-selected-btn"
                                data-toggle="modal" 
                                data-target="#export-selected-modal"
                                disabled>
                            <i class="fas fa-check-double"></i> تصدير المحدد
                        </button>
                    </div>

                    <button class="btn btn-outline-warning btn-sm mr-2" 
                            id="bulk-status-btn"
                            data-toggle="modal" 
                            data-target="#bulk-status-modal"
                            disabled>
                        <i class="fas fa-tasks"></i> تغيير الحالة
                    </button>

                    <x-check-all-delete
                            type="{{ \App\Models\NeedRequest::class }}"
                            :resource="trans('need_requests.plural')"></x-check-all-delete>

                    <div class="ml-2">
                        @include('dashboard.need_requests.partials.actions.trashed')
                    </div>
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('need_requests.attributes.project_id')</th>
            <th>@lang('need_requests.attributes.supervisor_id')</th>
            <th>@lang('need_requests.attributes.status')</th>
            <th>المدة المتبقية</th>
            <th>@lang('need_requests.count')</th>
            <th>@lang('need_requests.attributes.created_at')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($need_requests as $need_request)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$need_request"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.need_requests.show', $need_request) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $need_request->project->name ?? '---' }}
                    </a>
                </td>
                <td>{{ $need_request->supervisor->name ?? '---' }}</td>
                <td>
                    @php
                        $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                        $color = $statusColors[$need_request->status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $color }}">
                        {{ trans('need_requests.statuses.'.$need_request->status) }}
                    </span>
                </td>
                <td>
                    @if($need_request->isPending() && optional($need_request->project->needRequestProject)->deadline)
                        @php
                            $deadline = $need_request->project->needRequestProject->deadline;
                        @endphp
                        @if($deadline->isPast())
                            <span class="text-danger font-weight-bold">منتهي</span>
                        @else
                            <div class="row-countdown" data-deadline="{{ $deadline->toIso8601String() }}">
                                <span class="badge badge-pill badge-primary timer-text">...</span>
                            </div>
                        @endif
                    @else
                        ---
                    @endif
                </td>
                <td>{{ $need_request->items_count }}</td>
                <td>{{ $need_request->created_at->format('Y-m-d') }}</td>

                <td style="width: 160px">
                    @include('dashboard.need_requests.partials.actions.show')
                    @include('dashboard.need_requests.partials.actions.edit')
                    @include('dashboard.need_requests.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('need_requests.empty')</td>
            </tr>
        @endforelse

        @if($need_requests->hasPages())
            @slot('footer')
                {{ $need_requests->links() }}
            @endslot
        @endif
    @endcomponent

    {{-- Modal for Export Selected --}}
    <div class="modal fade" id="export-selected-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-file-excel"></i> تصدير الطلبات المحددة</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('dashboard.need_requests.export_selected') }}" method="GET" id="export-selected-form">
                    <div class="modal-body">
                        <p>هل أنت متأكد من تصدير طلبات الاحتياج التي قمت بتحديدها؟</p>
                        <input type="hidden" name="selected_ids" id="selected_ids_input">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تأكيد التصدير</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal for Bulk Status Update --}}
    <div class="modal fade" id="bulk-status-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fas fa-tasks"></i> تغيير حالة الطلبات المحددة</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('dashboard.need_requests.bulk_update_status') }}" method="POST" id="bulk-status-form">
                    @csrf
                    <div class="modal-body">
                        <p>يرجى اختيار الحالة الجديدة للطلبات المحددة:</p>
                        <div class="form-group">
                            <select name="status" class="form-control" required>
                                <option value="approved">موافق عليه</option>
                                <option value="rejected">مرفوض</option>
                            </select>
                        </div>
                        <input type="hidden" name="selected_ids" id="bulk_status_ids_input">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">تحديث الحالات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            function updateRowTimers() {
                $('.row-countdown').each(function() {
                    const deadline = new Date($(this).data('deadline')).getTime();
                    const now = new Date().getTime();
                    const diff = deadline - now;

                    if (diff <= 0) {
                        $(this).html('<span class="text-danger font-weight-bold">منتهي</span>');
                        return;
                    }

                    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((diff % (1000 * 60)) / 1000);

                    let text = "";
                    if (d > 0) text += d + "ي ";
                    text += String(h).padStart(2, '0') + ":" + String(m).padStart(2, '0') + ":" + String(s).padStart(2, '0');
                    
                    $(this).find('.timer-text').text(text);
                });
            }

            setInterval(updateRowTimers, 1000);
            updateRowTimers();

            // Handle Checkboxes and Buttons
            function toggleBulkButtons() {
                var selectedCount = $('.item-checkbox:checked').length;
                if (selectedCount > 0) {
                    $('#export-selected-btn, #bulk-status-btn').prop('disabled', false);
                } else {
                    $('#export-selected-btn, #bulk-status-btn').prop('disabled', true);
                }
            }

            // Listen for changes
            $(document).on('change', '.item-checkbox, #check-all, .check-all-checkbox', toggleBulkButtons);
            
            // Listen for clicks specifically which often trigger programmatic changes
            $(document).on('click', '#check-all, .check-all-checkbox, .item-checkbox', function() {
                setTimeout(toggleBulkButtons, 100);
            });

            // Periodic check as a fallback
            setInterval(toggleBulkButtons, 1000);

            // Populate IDs for modals
            $('#export-selected-modal, #bulk-status-modal').on('show.bs.modal', function () {
                var selectedIds = [];
                $('.item-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });
                
                var inputId = $(this).attr('id') === 'export-selected-modal' ? '#selected_ids_input' : '#bulk_status_ids_input';
                $(inputId).val(selectedIds.join(','));
            });
        });
    </script>
    @endpush
    @include('dashboard.need_requests.partials.skipped_modal')
</x-layout>
