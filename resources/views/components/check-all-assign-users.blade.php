<!-- الزر -->
<button type="button" class="btn btn-outline-success btn-sm"
        data-checkbox=".item-checkbox"
        data-form="assign-areaResponsible-block-form"
        data-toggle="modal"
        data-target="#assign-areaResponsible-block-modal"
        style="margin-right: 10px;">
    <i class="fas fa-user-tie"></i>
    تخصيص مسؤول ومندوب
</button>

<!-- المودال -->
<div class="modal fade" id="assign-areaResponsible-block-modal" tabindex="-1" role="dialog"
     aria-labelledby="assign-areaResponsible-block-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assign-areaResponsible-block-title">
                    تخصيص مسؤول المنطقة والمندوب
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('dashboard.people.assignToUsers') }}" method="POST" id="assign-areaResponsible-block-form">
                    @csrf

                    <!-- اختيار مسؤول المنطقة -->
                    <div class="form-group">
                        <label for="area_responsible_id">مسؤول المنطقة <span class="text-danger">*</span></label>
                        <select class="form-control" name="area_responsible_id" id="area_responsible_id" required>
                            <option value="">اختر مسؤول المنطقة</option>
                            @foreach(\App\Models\AreaResponsible::orderBy('name')->get(['id', 'name']) as $responsible)
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
                <button type="submit" class="btn btn-success btn-sm" form="assign-areaResponsible-block-form">
                    تحديث
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let selectedPeopleIds = [];

    // عند النقر على الزر لفتح المودال
    $('button[data-target="#assign-areaResponsible-block-modal"]').on('click', function(e) {
        selectedPeopleIds = [];
        $('.item-checkbox:checked').each(function() {
            selectedPeopleIds.push($(this).val());
        });

        if (selectedPeopleIds.length === 0) {
            alert('يرجى اختيار أشخاص للتخصيص أولاً');
            e.preventDefault();
            return false;
        }

        console.log('الأشخاص المحددون:', selectedPeopleIds);
    });

    // عند تغيير مسؤول المنطقة
    $('#area_responsible_id').on('change', function() {
        const responsibleId = $(this).val();
        const blockSelect = $('#block_id');

        blockSelect.html('<option value="">جارِ التحميل...</option>').prop('disabled', true);

        if (responsibleId) {
            $.ajax({
                url: "{{ route('dashboard.ajax.getBlocksByResponsible') }}",
                type: 'GET',
                data: {
                    responsible_id: responsibleId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('استجابة المندوبين:', response);
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
                    console.error('خطأ في تحميل المندوبين:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    blockSelect.html('<option value="">حدث خطأ في التحميل</option>');
                }
            });
        } else {
            blockSelect.html('<option value="">اختر المندوب</option>').prop('disabled', true);
        }
    });

    // عند إرسال النموذج
    $('#assign-areaResponsible-block-form').on('submit', function(e) {
        e.preventDefault();

        console.log('بدء إرسال النموذج...');

        if (selectedPeopleIds.length === 0) {
            alert('يرجى اختيار أشخاص للتخصيص أولاً');
            return false;
        }

        const areaResponsibleId = $('#area_responsible_id').val();
        const blockId = $('#block_id').val();

        if (!areaResponsibleId || !blockId) {
            alert('يرجى اختيار مسؤول المنطقة والمندوب');
            return false;
        }

        // تحضير البيانات
        const postData = {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'items': selectedPeopleIds.join(','),
            'area_responsible_id': areaResponsibleId,
            'block_id': blockId
        };

        console.log('البيانات المرسلة:', postData);

        const submitBtn = $('button[type="submit"][form="assign-areaResponsible-block-form"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> جاري التحديث...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: postData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            beforeSend: function(xhr) {
                console.log('إرسال الطلب...');
            },
            success: function(response) {
                console.log('استجابة ناجحة:', response);
                $('#assign-areaResponsible-block-modal').modal('hide');

                if (response.success) {
                    alert('تم التخصيص بنجاح');
                    window.location.reload();
                } else {
                    alert(response.message || 'حدث خطأ غير متوقع');
                }
            },
            error: function(xhr, status, error) {
                console.error('خطأ في الطلب:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    readyState: xhr.readyState,
                    error: error
                });

                let errorMessage = 'حدث خطأ في التخصيص';

                if (xhr.status === 422) {
                    // خطأ في validation
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                } else if (xhr.status === 419) {
                    // خطأ في CSRF token
                    errorMessage = 'انتهت صلاحية الجلسة. يرجى إعادة تحميل الصفحة';
                } else if (xhr.status === 404) {
                    errorMessage = 'الرابط غير موجود';
                } else if (xhr.status === 500) {
                    errorMessage = 'خطأ في الخادم';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += ': ' + xhr.responseJSON.message;
                    }
                } else if (xhr.status === 0) {
                    errorMessage = 'فشل الاتصال بالخادم';
                }

                alert(errorMessage);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('تحديث');
            }
        });
    });

    // إعادة تعيين المودال عند إغلاقه
    $('#assign-areaResponsible-block-modal').on('hidden.bs.modal', function() {
        $('#assign-areaResponsible-block-form')[0].reset();
        $('#block_id').html('<option value="">اختر المندوب</option>').prop('disabled', true);
        selectedPeopleIds = [];
    });
});
</script>
@endpush
