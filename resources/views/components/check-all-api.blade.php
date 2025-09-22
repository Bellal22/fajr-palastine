<!-- الزر -->
<button type="button" class="btn btn-sm"
        data-checkbox=".item-checkbox"
        data-form="bulk-api-sync-form"
        data-toggle="modal"
        data-target="#bulk-api-sync-modal"
        style="color:#28a745; border:1px solid #28a745;margin-right: 10px;">
    <i class="fas fa-sync-alt"></i>
    مزامنة API جماعية
</button>

<!-- المودال -->
<div class="modal fade" id="bulk-api-sync-modal" tabindex="-1" role="dialog"
     aria-labelledby="bulk-api-sync-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulk-api-sync-title">
                    مزامنة API جماعية
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    سيتم مزامنة الأشخاص المحددين مع API الخارجي. هذه العملية قد تستغرق بعض الوقت.
                </div>

                <div id="selected-count" class="mb-3">
                    <strong>عدد الأشخاص المحددين: <span id="count-number">0</span></strong>
                </div>

                <div id="sync-progress" style="display: none;">
                    <div class="progress mb-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar" style="width: 0%" id="progress-bar">
                            <span id="progress-text">0%</span>
                        </div>
                    </div>
                    <div id="sync-status">
                        <small class="text-muted">جاري البدء...</small>
                    </div>
                </div>

                <div id="sync-results" style="display: none;">
                    <div class="alert alert-success" id="success-results" style="display: none;">
                        <strong>تمت المزامنة بنجاح!</strong>
                        <ul id="success-list"></ul>
                    </div>
                    <div class="alert alert-danger" id="error-results" style="display: none;">
                        <strong>فشلت المزامنة للأشخاص التالية:</strong>
                        <ul id="error-list"></ul>
                    </div>
                </div>

                <form id="bulk-api-sync-form" style="display: none;">
                    @csrf
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" id="cancel-btn">
                    إلغاء
                </button>
                <button type="button" class="btn btn-success btn-sm" id="start-sync-btn">
                    <i class="fas fa-sync-alt"></i>
                    بدء المزامنة
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="close-results-btn" style="display: none;">
                    إغلاق
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            let selectedPeopleIds = [];
            let syncInProgress = false;

            // عند النقر على الزر لفتح المودال
            $('button[data-target="#bulk-api-sync-modal"]').on('click', function(e) {
                selectedPeopleIds = [];
                $('.item-checkbox:checked').each(function() {
                    selectedPeopleIds.push($(this).val());
                });

                if (selectedPeopleIds.length === 0) {
                    alert('يرجى اختيار أشخاص للمزامنة أولاً');
                    e.preventDefault();
                    return false;
                }

                $('#count-number').text(selectedPeopleIds.length);
                console.log('الأشخاص المحددون للمزامنة:', selectedPeopleIds);
            });

            // عند النقر على بدء المزامنة
            $('#start-sync-btn').on('click', function() {
                if (selectedPeopleIds.length === 0) {
                    alert('يرجى اختيار أشخاص للمزامنة أولاً');
                    return;
                }

                if (syncInProgress) {
                    return;
                }

                if (!confirm(`هل تريد مزامنة ${selectedPeopleIds.length} شخص مع API الخارجي؟`)) {
                    return;
                }

                startBulkSync();
            });

            function startBulkSync() {
                syncInProgress = true;

                // إخفاء الأزرار وإظهار التقدم
                $('#start-sync-btn, #cancel-btn').hide();
                $('#sync-progress').show();
                $('#sync-results').hide();

                let successCount = 0;
                let errorCount = 0;
                let processedCount = 0;
                let successList = [];
                let errorList = [];

                // معالجة كل شخص على حدة
                processPerson(0);

                function processPerson(index) {
                    if (index >= selectedPeopleIds.length) {
                        // انتهاء المعالجة
                        finishSync(successCount, errorCount, successList, errorList);
                        return;
                    }

                    const personId = selectedPeopleIds[index];
                    const progress = Math.round((index / selectedPeopleIds.length) * 100);

                    updateProgress(progress, `جاري مزامنة الشخص ${index + 1} من ${selectedPeopleIds.length}`);

                    // استدعاء API للشخص المحدد
                    $.ajax({
                        url: `{{ route('dashboard.people.aid.api', '') }}/${personId}`,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            processedCount++;
                            if (response.status && (response.status >= 200 && response.status < 300)) {
                                successCount++;
                                successList.push({
                                    person: response.person || `شخص ${personId}`,
                                    status: response.status
                                });
                            } else {
                                errorCount++;
                                errorList.push({
                                    person: response.person || `شخص ${personId}`,
                                    error: `HTTP ${response.status}: ${response.response || 'خطأ غير محدد'}`
                                });
                            }

                            // الانتقال للشخص التالي بعد تأخير قصير
                            setTimeout(() => {
                                processPerson(index + 1);
                            }, 500);
                        },
                        error: function(xhr, status, error) {
                            processedCount++;
                            errorCount++;

                            let errorMessage = 'خطأ غير محدد';
                            if (xhr.status === 404) {
                                errorMessage = 'الشخص غير موجود';
                            } else if (xhr.status === 500) {
                                errorMessage = 'خطأ في الخادم';
                            } else if (xhr.status === 0) {
                                errorMessage = 'فشل الاتصال';
                            } else {
                                errorMessage = `HTTP ${xhr.status}: ${error}`;
                            }

                            errorList.push({
                                person: `شخص ${personId}`,
                                error: errorMessage
                            });

                            console.error(`خطأ في مزامنة الشخص ${personId}:`, xhr);

                            // الانتقال للشخص التالي بعد تأخير قصير
                            setTimeout(() => {
                                processPerson(index + 1);
                            }, 500);
                        }
                    });
                }
            }

            function updateProgress(percentage, statusText) {
                $('#progress-bar').css('width', percentage + '%');
                $('#progress-text').text(percentage + '%');
                $('#sync-status small').text(statusText);
            }

            function finishSync(successCount, errorCount, successList, errorList) {
                syncInProgress = false;

                // إخفاء شريط التقدم
                $('#sync-progress').hide();

                // إظهار النتائج
                $('#sync-results').show();

                if (successCount > 0) {
                    $('#success-results').show();
                    let successHtml = `<li>تمت مزامنة ${successCount} شخص بنجاح</li>`;
                    successList.forEach(item => {
                        successHtml += `<li>${item.person} - حالة: ${item.status}</li>`;
                    });
                    $('#success-list').html(successHtml);
                }

                if (errorCount > 0) {
                    $('#error-results').show();
                    let errorHtml = `<li>فشلت مزامنة ${errorCount} شخص</li>`;
                    errorList.forEach(item => {
                        errorHtml += `<li>${item.person} - ${item.error}</li>`;
                    });
                    $('#error-list').html(errorHtml);
                }

                // إظهار زر الإغلاق
                $('#close-results-btn').show();

                console.log(`انتهاء المزامنة - نجح: ${successCount}, فشل: ${errorCount}`);
            }

            // إغلاق النتائج
            $('#close-results-btn').on('click', function() {
                $('#bulk-api-sync-modal').modal('hide');
                // إعادة تحميل الصفحة لعرض التحديثات
                location.reload();
            });

            // إعادة تعيين المودال عند إغلاقه
            $('#bulk-api-sync-modal').on('hidden.bs.modal', function() {
                if (!syncInProgress) {
                    resetModal();
                }
            });

            function resetModal() {
                selectedPeopleIds = [];
                $('#sync-progress, #sync-results').hide();
                $('#success-results, #error-results').hide();
                $('#start-sync-btn, #cancel-btn').show();
                $('#close-results-btn').hide();
                $('#count-number').text('0');
                $('#progress-bar').css('width', '0%');
                $('#progress-text').text('0%');
            }

            // منع إغلاق المودال أثناء المزامنة
            $('#bulk-api-sync-modal').on('hide.bs.modal', function(e) {
                if (syncInProgress) {
                    if (!confirm('المزامنة قيد التشغيل. هل تريد إيقافها والخروج؟')) {
                        e.preventDefault();
                        return false;
                    }
                    syncInProgress = false;
                }
            });
        });
    </script>
@endpush
