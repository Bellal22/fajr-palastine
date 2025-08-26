<!-- زر حذف المسؤول للمجموعة -->
<button type="button" class="btn btn-outline-dark btn-sm"
        data-checkbox=".item-checkbox"
        data-form="bulk-delete-area-responsible-form"
        data-toggle="modal"
        data-target="#bulk-delete-area-responsible-modal">
    <i class="fas fa-user-times"></i>
    حذف المسؤول من المجموعة
</button>

<!-- Modal حذف المسؤول للمجموعة -->
<div class="modal fade" id="bulk-delete-area-responsible-modal" tabindex="-1" role="dialog"
     aria-labelledby="bulk-delete-area-responsible-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulk-delete-area-responsible-title">
                    تأكيد حذف المسؤول
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    هل أنت متأكد من إلغاء ربط المسؤول والمندوب من الأشخاص المحددين؟
                    <br>
                    <small class="text-muted">سيتم إزالة area_responsible_id و block_id من جميع الأشخاص المحددين</small>
                </div>

                <form action="{{ route('dashboard.people.areaResponsible.bulkDelete') }}" method="POST" id="bulk-delete-area-responsible-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="items" id="selected-people-delete" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    إلغاء
                </button>
                <button type="submit" class="btn btn-danger btn-sm" form="bulk-delete-area-responsible-form">
                    <i class="fas fa-user-times"></i>
                    تأكيد حذف المسؤول
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // معالج زر حذف المسؤول للمجموعة
    document.addEventListener('DOMContentLoaded', function() {
        // زر فتح modal حذف المسؤول
        const bulkDeleteBtn = document.querySelector('[data-target="#bulk-delete-area-responsible-modal"]');

        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                const selectedIds = [];
                document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
                    selectedIds.push(checkbox.value);
                });

                if (selectedIds.length === 0) {
                    alert('يرجى تحديد أشخاص أولاً');
                    return false;
                }

                document.getElementById('selected-people-delete').value = selectedIds.join(',');

                // تحديث النص في Modal ليعرض العدد
                const modalTitle = document.getElementById('bulk-delete-area-responsible-title');
                modalTitle.textContent = `تأكيد حذف المسؤول من ${selectedIds.length} شخص`;
            });
        }

        // التأكد من وجود أشخاص محددين قبل الإرسال
        const deleteForm = document.getElementById('bulk-delete-area-responsible-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                const selectedIds = document.getElementById('selected-people-delete').value;
                if (!selectedIds || selectedIds.trim() === '') {
                    e.preventDefault();
                    alert('يرجى تحديد أشخاص أولاً');
                    return false;
                }
            });
        }
    });
</script>
@endpush
