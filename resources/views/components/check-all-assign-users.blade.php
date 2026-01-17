<!-- الزر -->
<button type="button" class="btn btn-sm"
        data-checkbox=".item-checkbox"
        data-form="assign-areaResponsible-block-form"
        data-toggle="modal"
        data-target="#assign-areaResponsible-block-modal"
        style="color:#f57c00; border:1px solid #f57c00;margin-right: 10px;">
    <i class="fas fa-user-tie"></i>
    @lang('check-all.actions.assignResponsibleAndBlock')
</button>

<!-- المودال -->
<div class="modal fade" id="assign-areaResponsible-block-modal" tabindex="-1" role="dialog"
     aria-labelledby="assign-areaResponsible-block-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assign-areaResponsible-block-title">
                    @lang('check-all.dialogs.assignResponsibleAndBlock.title')
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
                        <label for="area_responsible_id">
                            @lang('check-all.dialogs.assignResponsibleAndBlock.area_responsible_label')
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="area_responsible_id" id="area_responsible_id" required>
                            <option value="">@lang('check-all.dialogs.assignResponsibleAndBlock.select_area_responsible')</option>
                            @foreach(\App\Models\AreaResponsible::orderBy('name')->get(['id', 'name']) as $responsible)
                                <option value="{{ $responsible->id }}">{{ $responsible->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- اختيار المندوب -->
                    <div class="form-group">
                        <label for="block_id">
                            @lang('check-all.dialogs.assignResponsibleAndBlock.block_label')
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="block_id" id="block_id" required disabled>
                            <option value="">@lang('check-all.dialogs.assignResponsibleAndBlock.select_block')</option>
                        </select>
                        <small class="text-muted">@lang('check-all.dialogs.assignResponsibleAndBlock.select_responsible_first')</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    @lang('check-all.dialogs.assignResponsibleAndBlock.cancel')
                </button>
                <button type="submit" class="btn btn-success btn-sm" form="assign-areaResponsible-block-form">
                    @lang('check-all.dialogs.assignResponsibleAndBlock.confirm')
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
                    alert('{{ __("check-all.messages.no_people_selected") }}');
                    e.preventDefault();
                    return false;
                }

                console.log('الأشخاص المحددون:', selectedPeopleIds);
            });

            // عند تغيير مسؤول المنطقة
            $('#area_responsible_id').on('change', function() {
                const responsibleId = $(this).val();
                const blockSelect = $('#block_id');

                blockSelect.html('<option value="">{{ __("check-all.messages.loading") }}</option>').prop('disabled', true);

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
                            blockSelect.html('<option value="">{{ __("check-all.dialogs.assignResponsibleAndBlock.select_block") }}</option>');

                            if (response.blocks && response.blocks.length > 0) {
                                $.each(response.blocks, function(index, block) {
                                    blockSelect.append(`<option value="${block.id}">${block.name}</option>`);
                                });
                                blockSelect.prop('disabled', false);
                            } else {
                                blockSelect.html('<option value="">{{ __("check-all.messages.no_blocks_for_responsible") }}</option>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('خطأ في تحميل المندوبين:', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                responseText: xhr.responseText,
                                error: error
                            });
                            blockSelect.html('<option value="">{{ __("check-all.messages.loading_error") }}</option>');
                        }
                    });
                } else {
                    blockSelect.html('<option value="">{{ __("check-all.dialogs.assignResponsibleAndBlock.select_block") }}</option>').prop('disabled', true);
                }
            });

            // عند إرسال النموذج
            $('#assign-areaResponsible-block-form').on('submit', function(e) {
                e.preventDefault();

                console.log('بدء إرسال النموذج...');

                if (selectedPeopleIds.length === 0) {
                    alert('{{ __("check-all.messages.no_people_selected") }}');
                    return false;
                }

                const areaResponsibleId = $('#area_responsible_id').val();
                const blockId = $('#block_id').val();

                if (!areaResponsibleId || !blockId) {
                    alert('{{ __("check-all.messages.select_responsible_and_block") }}');
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
                const updatingText = '{{ __("check-all.messages.updating") }}';
                const updateText = '{{ __("check-all.dialogs.assignResponsibleAndBlock.confirm") }}';

                submitBtn.prop('disabled', true).html(`<i class="fa fa-spinner fa-spin"></i> ${updatingText}`);

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
                            alert('{{ __("check-all.messages.assigned_successfully") }}');
                            window.location.reload();
                        } else {
                            alert(response.message || '{{ __("check-all.messages.unexpected_error") }}');
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

                        let errorMessage = '{{ __("check-all.messages.assignment_error") }}';

                        if (xhr.status === 422) {
                            // خطأ في validation
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                            }
                        } else if (xhr.status === 419) {
                            // خطأ في CSRF token
                            errorMessage = '{{ __("check-all.messages.session_expired") }}';
                        } else if (xhr.status === 404) {
                            errorMessage = '{{ __("check-all.messages.not_found") }}';
                        } else if (xhr.status === 500) {
                            errorMessage = '{{ __("check-all.messages.server_error") }}';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage += ': ' + xhr.responseJSON.message;
                            }
                        } else if (xhr.status === 0) {
                            errorMessage = '{{ __("check-all.messages.connection_failed") }}';
                        }

                        alert(errorMessage);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(updateText);
                    }
                });
            });

            // إعادة تعيين المودال عند إغلاقه
            $('#assign-areaResponsible-block-modal').on('hidden.bs.modal', function() {
                $('#assign-areaResponsible-block-form')[0].reset();
                $('#block_id').html('<option value="">{{ __("check-all.dialogs.assignResponsibleAndBlock.select_block") }}</option>').prop('disabled', true);
                selectedPeopleIds = [];
            });
        });
    </script>
@endpush
