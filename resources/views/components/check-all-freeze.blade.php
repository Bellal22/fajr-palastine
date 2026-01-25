<!-- الزر -->
<div class="dropdown d-inline-block mr-2">
    <button type="button" class="btn btn-sm dropdown-toggle" 
            id="bulk-freeze-dropdown"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            data-checkbox=".item-checkbox"
            style="color:#607d8b; border:1px solid #607d8b;">
        <i class="fas fa-snowflake"></i> إجراءات التجميد
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        <button type="button" class="dropdown-item bulk-freeze-action" data-freeze="true">
            <i class="fas fa-snowflake text-danger"></i> تجميد المختار
        </button>
        <button type="button" class="dropdown-item bulk-freeze-action" data-freeze="false">
            <i class="fas fa-snowflake text-success"></i> فك تجميد المختار
        </button>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // الاستماع للنقر على خيارات التجميد
            $(document).on('click', '.bulk-freeze-action', function() {
                const freeze = $(this).data('freeze');
                const selectedIds = [];
                
                $('.item-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    return;
                }

                const confirmTitle = freeze ? 'تجميد البيانات' : 'فك تجميد البيانات';
                const confirmText = freeze ? 'هل أنت متأكد من تجميد البيانات المختارة؟' : 'هل أنت متأكد من فك تجميد البيانات المختارة؟';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: confirmTitle,
                        text: confirmText,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'نعم، تابع',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            executeBulkFreeze(selectedIds, freeze);
                        }
                    });
                } else {
                    if (confirm(confirmText)) {
                        executeBulkFreeze(selectedIds, freeze);
                    }
                }
            });

            function executeBulkFreeze(ids, freeze) {
                $.ajax({
                    url: "{{ route('dashboard.people.bulkToggleFreeze') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'PUT',
                        items: ids.join(','),
                        freeze: freeze
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم بنجاح',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                alert(response.message);
                                window.location.reload();
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: response.message
                                });
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function() {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: 'حدث خطأ غير متوقع أثناء المعالجة'
                            });
                        } else {
                            alert('حدث خطأ غير متوقع أثناء المعالجة');
                        }
                    }
                });
            }
        });
    </script>
@endpush
