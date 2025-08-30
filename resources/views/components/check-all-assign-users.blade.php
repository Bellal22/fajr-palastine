<!-- الزر -->
<button type="button" class="btn btn-outline-success btn-sm"
        data-checkbox=".item-checkbox"
        data-form="assign-supervisor-block-form"
        data-toggle="modal"
        data-target="#assign-supervisor-block-modal"
        style="margin-right: 10px;">
    <i class="fas fa-user-tie"></i>
    تخصيص مسؤول ومندوب
</button>

<!-- المودال -->
<div class="modal fade" id="assign-supervisor-block-modal" tabindex="-1" role="dialog"
     aria-labelledby="assign-supervisor-block-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assign-supervisor-block-title">
                    تخصيص مسؤول المنطقة والمندوب
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('dashboard.people.assignToUsers') }}" method="POST" id="assign-supervisor-block-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="items" id="selected-people-supervisor" value="">

                    <!-- اختيار مسؤول المنطقة -->
                    <div class="form-group">
                        <label for="area_responsible_id">مسؤول المنطقة <span class="text-danger">*</span></label>
                        <select class="form-control" name="area_responsible_id" id="area_responsible_id" required>
                            <option value="">اختر مسؤول المنطقة</option>
                            @foreach($areaResponsibles as $responsible)
                                <option value="{{ $responsible->id }}">{{ $responsible->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- اختيار المندوب -->
                    <div class="form-group">
                        <label for="block_id">المندوب <span class="text-danger">*</span></label>
                        <select class="form-control" name="block_id" id="block_id" required disabled>
                            <option value="">اختر المندوب</option>
                        </select>
                        <small class="text-muted">يجب اختيار مسؤول المنطقة أولاً</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    إلغاء
                </button>
                <button type="submit" class="btn btn-success btn-sm" form="assign-supervisor-block-form">
                    تحديث
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // عند النقر على الزر لفتح المودال
    $('button[data-target="#assign-supervisor-block-modal"]').on('click', function() {
        // جمع الـ IDs المحددة
        const selectedIds = [];
        $('.item-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        $('#selected-people-supervisor').val(selectedIds.join(','));
    });

    // عند تغيير مسؤول المنطقة
    $('#area_responsible_id').on('change', function() {
        const responsibleId = $(this).val();
        const blockSelect = $('#block_id');

        // تنظيف وتعطيل select المندوبين
        blockSelect.html('<option value="">جارِ التحميل...</option>').prop('disabled', true);

        if (responsibleId) {
            // طلب AJAX لجلب المندوبين التابعين لمسؤول المنطقة
            $.ajax({
                url: "{{ route('dashboard.ajax.getblocksByResponsible') }}",
                type: 'GET',
                data: {
                    responsible_id: responsibleId
                },
                success: function(response) {
                    blockSelect.html('<option value="">اختر المندوب</option>');

                    if (response.blocks && response.blocks.length > 0) {
                        $.each(response.blocks, function(index, block) {
                            blockSelect.append(`<option value="${block.id}">${block.name}</option>`);
                        });
                        blockSelect.prop('disabled', false);
                    } else {
                        blockSelect.html('<option value="">لا توجد مندوبين لهذا المسؤول</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('خطأ في تحميل المندوبين:', error);
                    blockSelect.html('<option value="">حدث خطأ في التحميل</option>');
                }
            });
        } else {
            blockSelect.html('<option value="">اختر المندوب</option>').prop('disabled', true);
        }
    });

    // إعادة تعيين المودال عند إغلاقه
    $('#assign-supervisor-block-modal').on('hidden.bs.modal', function() {
        $('#assign-supervisor-block-form')[0].reset();
        $('#block_id').html('<option value="">اختر المندوب</option>').prop('disabled', true);
    });
});
</script>
@endpush
