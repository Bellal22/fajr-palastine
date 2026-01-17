<!-- زر حذف المسؤول للمجموعة -->
<button type="button" class="btn btn-outline-dark btn-sm bulk-delete-btn"
        id="bulk-delete-area-responsible-btn"
        data-checkbox=".item-checkbox"
        style="margin-right: 10px;">
    <i class="fas fa-user-times"></i>
    @lang('check-all.actions.deleteAreaResponsibles')
</button>

<!-- Modal حذف المسؤول للمجموعة -->
<div class="modal fade" id="bulk-delete-area-responsible-modal" tabindex="-1" role="dialog"
     aria-labelledby="bulk-delete-area-responsible-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulk-delete-area-responsible-title">
                    @lang('check-all.dialogs.bulkDeleteResponsible.title')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <i class="fas fa-exclamation-triangle"></i>
                @lang('check-all.dialogs.bulkDeleteResponsible.info')
                <br>
                <small class="text-muted">@lang('check-all.dialogs.bulkDeleteResponsible.warning')</small>

                <form action="{{ route('dashboard.people.areaResponsible.bulkDelete') }}" method="POST" id="bulk-delete-area-responsible-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="items" id="selected-people-delete" value="">
                    <input type="hidden" name="action" value="both">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    @lang('check-all.dialogs.bulkDeleteResponsible.cancel')
                </button>
                <button type="submit" class="btn btn-danger btn-sm" form="bulk-delete-area-responsible-form" id="confirm-delete-btn">
                    <i class="fas fa-user-times"></i>
                    @lang('check-all.dialogs.bulkDeleteResponsible.confirm')
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulkDeleteBtn = document.getElementById('bulk-delete-area-responsible-btn');
    const modal = document.getElementById('bulk-delete-area-responsible-modal');
    const deleteForm = document.getElementById('bulk-delete-area-responsible-form');
    const selectedInput = document.getElementById('selected-people-delete');
    const confirmBtn = document.getElementById('confirm-delete-btn');

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // جمع الـ IDs المحددة
            const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value).filter(id => id);

            if (selectedIds.length === 0) {
                alert('{{ __("check-all.messages.no_people_selected") }}');
                return;
            }

            // تحديث قيمة الـ input
            if (selectedInput) {
                selectedInput.value = selectedIds.join(',');
            }

            // تحديث عنوان الـ modal
            const modalTitle = document.getElementById('bulk-delete-area-responsible-title');
            if (modalTitle) {
                modalTitle.textContent = '{{ __("check-all.dialogs.bulkDeleteResponsible.title_with_count", ["count" => ""]) }}'.replace(':count', selectedIds.length);
            }

            // فتح الـ modal
            $('#bulk-delete-area-responsible-modal').modal('show');
        });
    }

    // التحقق قبل الإرسال
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            const selectedIds = selectedInput ? selectedInput.value : '';

            if (!selectedIds || selectedIds.trim() === '') {
                e.preventDefault();
                alert('{{ __("check-all.messages.no_people_selected") }}');
                return false;
            }

            // إظهار مؤشر تحميل
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("check-all.messages.processing") }}';
            }
        });
    }

    // إضافة listener لإغلاق الـ modal
    const closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
    closeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            $('#bulk-delete-area-responsible-modal').modal('hide');
        });
    });
});
</script>
@endpush
