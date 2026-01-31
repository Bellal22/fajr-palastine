@php
    $notFound = $needRequestNotFound ?? session('need_request_notFound', []);
    $unavailable = $needRequestUnavailable ?? session('need_request_unavailable', []);
    $processed = $needRequestProcessed ?? session('need_request_processed', []);

    $allSkipped = array_merge($notFound, $unavailable, $processed);
    
    // Debugging: Log to laravel log
    if (!empty($allSkipped)) {
        \Log::info('Modal showing with items:', $allSkipped);
    }
@endphp

@if(!empty($allSkipped))
<div class="modal fade" id="skipped-items-modal" tabindex="-1" role="dialog" aria-labelledby="skippedItemsModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="skippedItemsModalTitle">
                    <i class="fas fa-exclamation-triangle"></i>
                    تنبيه: هويات مستبعدة
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border-info">
                    <i class="fas fa-info-circle"></i>
                    تم إنشاء الطلب للأشخاص الصالحين فقط، وتم استبعاد التالي:
                </div>
                <ul class="list-group list-group-flush border">
                    @foreach($allSkipped as $error)
                        <li class="list-group-item">
                            <i class="fas fa-times-circle text-danger mr-2"></i>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="clear-skipped-session-button">
                    @lang('people.modal.confirm')
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Skipped items modal script loaded');
    // Ensure the modal is shown
    $('#skipped-items-modal').modal('show');

    // Handle session clearing
    $('#clear-skipped-session-button').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري المسح...');
        
        $.ajax({
            url: '{{ route("dashboard.need_requests.clear") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#skipped-items-modal').modal('hide');
                location.reload(); // Optional: reload to clear variables
            },
            error: function(xhr) {
                alert('حدث خطأ أثناء مسح التنبيهات');
                $btn.prop('disabled', false).html('@lang("people.modal.confirm")');
            }
        });
    });
});
</script>
@endpush
@endif
