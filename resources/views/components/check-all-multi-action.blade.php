<!-- Multi-Action Button -->
<button type="button" class="btn btn-sm btn-outline-primary shadow-sm"
        id="multi-action-btn"
        data-toggle="modal"
        data-target="#multi-action-modal"
        disabled
        style="transition: all 0.3s ease; border-radius: 8px; font-weight: 600;">
    <i class="fas fa-tasks mr-1"></i> إجراءات متعددة
</button>

<style>
    #multi-action-modal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    #multi-action-modal .modal-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 1.5rem 2rem;
        border: none;
    }
    #multi-action-modal .modal-title {
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    #multi-action-modal .list-group-item {
        border: 1px solid #edf2f7;
        margin-bottom: 12px;
        border-radius: 12px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        padding: 1rem 1.25rem;
    }
    #multi-action-modal .list-group-item:hover {
        border-color: #4e73df;
        background-color: #f8faff;
        transform: translateX(5px);
    }
    #multi-action-modal .action-checkbox:checked + label {
        color: #4e73df;
        font-weight: 700;
    }
    #multi-action-modal .action-options {
        animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .selected-badge {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-right: 15px;
    }
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
</style>

<!-- Multi-Action Modal -->
<div class="modal fade" id="multi-action-modal" tabindex="-1" role="dialog" aria-labelledby="multi-action-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center" id="multi-action-title">
                    <i class="fas fa-tasks mr-2"></i> إجراءات متعددة
                    <span class="selected-badge modal-selected-count">0 أفراد مختارين</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="multi-action-selection-step">
                    <p class="mb-3 text-muted">اختر الإجراءات التي ترغب بتنفيذها بالترتيب:</p>
                    <ul class="list-group mb-4" id="multi-action-list">
                        <!-- التحميد (Freeze) -->
                        <li class="list-group-item d-flex align-items-center">
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" class="custom-control-input action-checkbox" id="action-freeze-1" data-action="freeze">
                                <label class="custom-control-label" for="action-freeze-1">
                                    <i class="fas fa-snowflake text-info mr-1"></i> التحميد (تجميد/فك تجميد)
                                </label>
                            </div>
                            <div class="action-options ml-auto" style="display: none;">
                                <select class="form-control form-control-sm freeze-type border-info" style="border-radius: 20px;">
                                    <option value="true">❄️ تجميد</option>
                                    <option value="false">☀️ فك تجميد</option>
                                </select>
                            </div>
                        </li>

                        <!-- حذف مسؤول المنطقة -->
                        <li class="list-group-item d-flex align-items-center">
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" class="custom-control-input action-checkbox" id="action-delete-responsible" data-action="deleteAreaResponsible">
                                <label class="custom-control-label" for="action-delete-responsible">
                                    <i class="fas fa-user-times text-danger mr-1"></i> حذف مسؤول المنطقة
                                </label>
                            </div>
                        </li>

                        <!-- تخصيص مسؤول المنطقة والمندوب -->
                        <li class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="custom-control custom-checkbox mr-3">
                                    <input type="checkbox" class="custom-control-input action-checkbox" id="action-assign-users" data-action="assignUsers">
                                    <label class="custom-control-label" for="action-assign-users">
                                        <i class="fas fa-user-tie text-warning mr-1"></i> تخصيص مسؤول المنطقة والمندوب
                                    </label>
                                </div>
                            </div>
                            <div class="action-options mt-3 border-top pt-3 bg-white p-3 rounded shadow-sm" style="display: none; border: 1px dashed #e2e8f0;">
                                <div class="form-row">
                                    <div class="col-md-6 mb-2">
                                        <label class="small font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">مسؤول المنطقة</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-user-shield text-primary"></i></span>
                                            </div>
                                            <select class="form-control form-control-sm multi-area-responsible-id border-left-0 pl-0">
                                                <option value="">اختر مسؤول...</option>
                                                @foreach(\App\Models\AreaResponsible::orderBy('name')->get(['id', 'name']) as $responsible)
                                                    <option value="{{ $responsible->id }}">{{ $responsible->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="small font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">المندوب</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-users text-success"></i></span>
                                            </div>
                                            <select class="form-control form-control-sm multi-block-id border-left-0 pl-0" disabled>
                                                <option value="">اختر مسؤول أولاً...</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <!-- مزامنة API -->
                        <li class="list-group-item d-flex align-items-center">
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" class="custom-control-input action-checkbox" id="action-sync" data-action="syncApi">
                                <label class="custom-control-label" for="action-sync">
                                    <i class="fas fa-sync-alt text-success mr-1"></i> مزامنة API
                                </label>
                            </div>
                        </li>

                        <!-- التجميد مرة ثانية -->
                        <li class="list-group-item d-flex align-items-center">
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" class="custom-control-input action-checkbox" id="action-freeze-2" data-action="freeze">
                                <label class="custom-control-label" for="action-freeze-2">
                                    <i class="fas fa-snowflake text-info mr-1"></i> التجميد مرة ثانية
                                </label>
                            </div>
                            <div class="action-options ml-auto" style="display: none;">
                                <select class="form-control form-control-sm freeze-type border-info" style="border-radius: 20px;">
                                    <option value="true">❄️ تجميد</option>
                                    <option value="false">☀️ فك تجميد</option>
                                </select>
                            </div>
                        </li>
                    </ul>
                </div>

                <div id="multi-action-execution-step" style="display: none;">
                    <div class="text-center mb-4" id="execution-header">
                        <div class="spinner-border text-primary execution-main-spinner" role="status">
                            <span class="sr-only">جاري التنفيذ...</span>
                        </div>
                        <h5 class="mt-3 execution-header-title">جاري تنفيذ الإجراءات...</h5>
                    </div>
                    <div id="execution-progress-list">
                        <!-- سيتم إضافة عناصر التقدم هنا برمجياً -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" id="multi-action-close-btn">إلغاء</button>
                <button type="button" class="btn btn-success btn-sm" id="multi-action-confirm-btn">موافق</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            let selectedActionQueue = [];
            
            // مراقبة اختيار الأفراد لتفعيل/تعطيل الزر وتحديث العداد
            function updateMainBtnState() {
                const checkedCount = $('.item-checkbox:checked').length;
                $('#multi-action-btn').prop('disabled', checkedCount === 0);
                $('.modal-selected-count').text(checkedCount + ' أفراد مختارين');
                
                if(checkedCount > 0) {
                    $('#multi-action-btn').removeClass('btn-outline-primary').addClass('btn-primary shadow');
                } else {
                    $('#multi-action-btn').removeClass('btn-primary shadow').addClass('btn-outline-primary');
                }
            }

            $(document).on('change', '.item-checkbox, #check-all', function() {
                setTimeout(updateMainBtnState, 50);
            });
            
            // التحقق الأولي
            updateMainBtnState();

            // إظهار/إخفاء الخيارات عند النقر على التشيكليست
            $('.action-checkbox').on('change', function() {
                const li = $(this).closest('li');
                const options = li.find('.action-options');
                if (this.checked) {
                    options.show();
                    // إضافة إلى الدور (بالترتيب)
                    selectedActionQueue.push(this.id);
                } else {
                    options.hide();
                    // إزالة من الدور
                    selectedActionQueue = selectedActionQueue.filter(id => id !== this.id);
                }
            });

            // التعامل مع تغيير مسؤول المنطقة في خيارات التخصيص
            $('.multi-area-responsible-id').on('change', function() {
                const responsibleId = $(this).val();
                const blockSelect = $('.multi-block-id');

                blockSelect.html('<option value="">جاري التحميل...</option>').prop('disabled', true);

                if (responsibleId) {
                    $.ajax({
                        url: "{{ route('dashboard.ajax.getBlocksByResponsible') }}",
                        type: 'GET',
                        data: { responsible_id: responsibleId },
                        success: function(response) {
                            blockSelect.html('<option value="">اختر المندوب...</option>');
                            if (response.blocks && response.blocks.length > 0) {
                                $.each(response.blocks, function(index, block) {
                                    blockSelect.append(`<option value="${block.id}">${block.name}</option>`);
                                });
                                blockSelect.prop('disabled', false);
                            } else {
                                blockSelect.html('<option value="">لا يوجد مناديب لهذا المسؤول</option>');
                            }
                        }
                    });
                } else {
                    blockSelect.html('<option value="">اختر المسؤول أولاً...</option>').prop('disabled', true);
                }
            });

            // تأكيد التنفيذ
            $('#multi-action-confirm-btn').on('click', function() {
                const selectedIds = [];
                $('.item-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى اختيار أشخاص أولاً' });
                    return;
                }

                if (selectedActionQueue.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى اختيار إجراء واحد على الأقل' });
                    return;
                }

                executeActions(selectedIds);
            });

            async function executeActions(peopleIds) {
                // تبديل واجهة المودال للبدء بالتنفيذ
                $('#multi-action-selection-step').hide();
                $('#multi-action-execution-step').show();
                $('#multi-action-confirm-btn').hide();
                $('#multi-action-close-btn').prop('disabled', true);

                const progressList = $('#execution-progress-list');
                progressList.empty();

                // إنشاء عناصر التقدم في القائمة
                selectedActionQueue.forEach(actionId => {
                    const label = $(`label[for="${actionId}"]`).text();
                    progressList.append(`
                        <div class="d-flex align-items-center mb-3 p-3 border rounded bg-light shadow-sm execution-row" id="progress-item-${actionId}" style="transition: all 0.3s ease;">
                            <div class="status-icon-container mr-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; border-radius: 50%; background: #eee;">
                                <i class="fas fa-clock text-muted status-icon"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold mb-0">${label}</div>
                                <small class="status-text text-muted">بانتظار الدور...</small>
                            </div>
                            <div class="execution-loader" style="display:none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            </div>
                        </div>
                    `);
                });

                // تنفيذ الإجراءات واحداً تلو الآخر
                for (const actionId of selectedActionQueue) {
                    const checkbox = $('#' + actionId);
                    const actionType = checkbox.data('action');
                    const progressItem = $(`#progress-item-${actionId}`);
                    
                    progressItem.removeClass('bg-light').addClass('border-primary').css('background', '#f8faff');
                    progressItem.find('.status-icon-container').css('background', '#e8f0fe');
                    progressItem.find('.status-icon').removeClass('fa-clock text-muted').addClass('fa-spinner fa-spin text-primary');
                    progressItem.find('.status-text').removeClass('text-muted').addClass('text-primary').text('جاري المعالجة...');
                    progressItem.find('.execution-loader').show();

                    try {
                        const result = await runSpecificAction(actionType, actionId, peopleIds);
                        
                        if (result.success) {
                            progressItem.removeClass('border-primary').addClass('border-success').css('background', '#f6fff9');
                            progressItem.find('.status-icon-container').css('background', '#d1e7dd');
                            progressItem.find('.status-icon').removeClass('fa-spinner fa-spin text-primary').addClass('fa-check-circle text-success');
                            progressItem.find('.status-text').removeClass('text-primary').addClass('text-success').text(result.message || 'تم الاكتمال بنجاح');
                            progressItem.find('.execution-loader').hide();
                        } else {
                            throw new Error(result.message || 'فشل الإجراء');
                        }
                    } catch (error) {
                        progressItem.removeClass('border-primary').addClass('border-danger').css('background', '#fff5f5');
                        progressItem.find('.status-icon-container').css('background', '#f8d7da');
                        progressItem.find('.status-icon').removeClass('fa-spinner fa-spin text-primary').addClass('fa-times-circle text-danger');
                        progressItem.find('.status-text').removeClass('text-primary').addClass('text-danger').text('خطأ: ' + error.message);
                        progressItem.find('.execution-loader').hide();
                        
                        if (!confirm('فشل الإجراء: ' + error.message + '. هل تريد المتابعة في الإجراءات التالية؟')) {
                            break;
                        }
                    }
                }

                // إنهاء التنفيذ - إخفاء السبينر الرئيسي وتغيير العنوان
                $('.execution-main-spinner').hide();
                $('.execution-header-title').text('تم الانتهاء من جميع الإجراءات المحددة').addClass('text-success pulse-animation');

                $('#multi-action-close-btn').prop('disabled', false).text('إغلاق وتحديث الصفحه').off('click').on('click', function() {
                    window.location.reload();
                });
                
                Swal.fire({
                    icon: 'success',
                    title: 'اكتملت العملية',
                    text: 'تم الانتهاء من جميع الإجراءات المحددة',
                    confirmButtonText: 'حسناً'
                }).then(() => {
                    window.location.reload();
                });
            }

            function runSpecificAction(type, elementId, peopleIds) {
                return new Promise((resolve, reject) => {
                    const li = $('#' + elementId).closest('li');
                    let url = '';
                    let data = {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        items: peopleIds.join(','),
                        _method: 'PUT'
                    };

                    switch (type) {
                        case 'freeze':
                            url = "{{ route('dashboard.people.bulkToggleFreeze') }}";
                            data.freeze = li.find('.freeze-type').val();
                            break;
                        case 'deleteAreaResponsible':
                            url = "{{ route('dashboard.people.areaResponsible.bulkDelete') }}";
                            data.action = 'both';
                            break;
                        case 'assignUsers':
                            url = "{{ route('dashboard.people.assignToUsers') }}";
                            data.area_responsible_id = li.find('.multi-area-responsible-id').val();
                            data.block_id = li.find('.multi-block-id').val();
                            data._method = 'POST'; // هذا المسار يستخدم POST
                            break;
                        case 'syncApi':
                            // المزامنة تحتاج معالجة خاصة لأنها في المكون الأصلي تنفذ فرادى
                            // سنقوم هنا بمزامنتهم في طلب واحد إذا كان الخادم يدعم ذلك، 
                            // أو سنقوم بحلقة برمجية هنا
                            executeSyncInLoop(peopleIds)
                                .then(res => resolve(res))
                                .catch(err => reject(err));
                            return; // الخروج من الوظيفة لأننا عالجنا الوعد يدوياً
                    }

                    if (!url) {
                        resolve({ success: true, message: 'تجاوز الإجراء' });
                        return;
                    }

                    $.ajax({
                        url: url,
                        type: data._method === 'PUT' ? 'POST' : data._method,
                        data: data,
                        success: function(response) {
                            resolve(response);
                        },
                        error: function(xhr) {
                            resolve({ success: false, message: 'خطأ في الخادم ' + xhr.status });
                        }
                    });
                });
            }

            async function executeSyncInLoop(peopleIds) {
                let successCount = 0;
                for (const personId of peopleIds) {
                    try {
                        await $.ajax({
                            url: `{{ route('dashboard.people.aid.api', '') }}/${personId}`,
                            type: 'GET'
                        });
                        successCount++;
                    } catch (e) {
                        console.error('فشل مزامنة الشخص: ' + personId);
                    }
                }
                return { success: true, message: `تمت مزامنة ${successCount} من ${peopleIds.length}` };
            }
        });
    </script>
@endpush
