@include('dashboard.errors')

@push('styles')
<style>
    .countdown-timer-container {
        display: none;
        background: rgba(115, 103, 240, 0.05);
        border: 2px solid #0d6efd;  /* Bootstrap Primary Blue */
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(115, 103, 240, 0.1);
        position: relative;
        overflow: hidden;
    }
    .countdown-timer-container::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 5px;
        height: 100%;
        background: #0d6efd;
    }
    .countdown-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        color: #506078;
        font-weight: 700;
        font-size: 1.1rem;
    }
    .countdown-header i {
        color: #0d6efd;
        margin-left: 10px;
        font-size: 1.3rem;
    }
    .countdown-display {
        display: flex;
        gap: 20px;
        justify-content: center;
    }
    .countdown-unit {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 70px;
    }
    .countdown-value {
        font-size: 2.2rem;
        font-weight: 900;
        color: #0d6efd;
        line-height: 1;
        font-family: 'monospace', sans-serif;
    }
    .countdown-name {
        font-size: 0.85rem;
        color: #506078;
        margin-top: 5px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .deadline-passed {
        border-color: #ea5455;
    }
    .deadline-passed .countdown-value, .deadline-passed .countdown-header i {
        color: #ea5455;
    }
</style>
@endpush

<div id="project-countdown" class="countdown-timer-container">
    <div class="countdown-header">
        <i class="fas fa-hourglass-half"></i>
        <span>الوقت المتبقي لتقديم/تعديل الطلب:</span>
    </div>
    <div class="countdown-display">
        <div class="countdown-unit">
            <span id="timer-days" class="countdown-value">00</span>
            <span class="countdown-name">أيام</span>
        </div>
        <div class="countdown-unit">
            <span id="timer-hours" class="countdown-value">00</span>
            <span class="countdown-name">ساعات</span>
        </div>
        <div class="countdown-unit">
            <span id="timer-minutes" class="countdown-value">00</span>
            <span class="countdown-name">دقائق</span>
        </div>
        <div class="countdown-unit">
            <span id="timer-seconds" class="countdown-value">00</span>
            <span class="countdown-name">ثواني</span>
        </div>
    </div>
</div>

{{ BsForm::select('project_id')
    ->options($projects->pluck('name', 'id'))
    ->value($selected_project_id ?? null)
    ->label(trans('need_requests.attributes.project_id'))
    ->placeholder(trans('need_requests.select'))
    ->attribute('id', 'project-selector')
}}

<script>
    window.projectDeadlines = {!! isset($deadlines) ? json_encode($deadlines) : '{}' !!};
    window.projectLimits = {!! isset($limits) ? json_encode($limits) : '{}' !!};
</script>

<div id="project-limit-info" class="alert alert-primary mt-2" style="display: none; border-radius: 8px;">
    <i class="fas fa-user-check mr-1"></i>
    <span>الحد الأقصى للهويات المسموح به لهذا المشروع هو: <strong id="limit-value"></strong></span>
</div>

<div id="project-criteria-info" class="card mt-2 border-primary mb-2" style="display: none; border-radius: 12px; border-width: 2px; background-color: #f8f9fa;">
    <div class="card-header bg-primary text-white p-1" style="border-radius: 10px 10px 0 0;">
        <h6 class="mb-0 mx-1"><i class="fas fa-clipboard-list mr-1"></i> معايير الترشيح المطلوبة لهذا المشروع:</h6>
    </div>
    <div class="card-body p-2">
        <div id="criteria-list" class="row">
            <!-- سيتم تعبئتها بواسطة JS -->
        </div>
        <div id="criteria-notes-div" class="mt-1 border-top pt-1" style="display: none;">
            <strong class="text-primary small">ملاحظات إضافية:</strong>
            <p id="criteria-notes-text" class="mb-0 small"></p>
        </div>
    </div>
</div>

@if(isset($need_request))
    @if($need_request->isPending())
        {{ BsForm::textarea('person_ids')->value($need_request->person_ids)->label(trans('need_requests.attributes.person_ids'))->placeholder("أدخل أرقام الهويات هنا، رقم في كل سطر...") }}
    @endif
@else
    {{ BsForm::textarea('person_ids')->label(trans('need_requests.attributes.person_ids'))->placeholder("أدخل أرقام الهويات هنا، رقم في كل سطر...") }}
@endif

{{ BsForm::textarea('notes')->label(trans('need_requests.attributes.notes')) }}

@push('scripts')
<script>
    $(document).ready(function() {
        let timerInterval;
        window.projectCriteria = {!! isset($criteria) ? json_encode($criteria) : '{}' !!};

        // Removing gender translation as it is no longer used in criteria
        // function translateGender(g) { ... }

        function displayCriteria(projectId) {
            const c = window.projectCriteria[projectId];
            const $container = $('#project-criteria-info');
            const $list = $('#criteria-list');
            const $notesDiv = $('#criteria-notes-div');
            
            $list.empty();
            $notesDiv.hide();

            console.log("Displaying criteria for project:", projectId, c);

            if (!c) {
                $container.hide();
                return;
            }

            let hasCriteria = false;
            const items = [];

            // Helper to handle both single strings and arrays
            const formatList = (val) => {
                if (Array.isArray(val)) return val.length > 0 ? val.join('، ') : null;
                return val || null;
            };

            if (c.min_family || c.max_family) {
                let text = '';
                if (c.min_family && c.max_family) text = `من ${c.min_family} إلى ${c.max_family}`;
                else if (c.min_family) text = `${c.min_family} أفراد فأكثر`;
                else text = `بحد أقصى ${c.max_family} أفراد`;
                items.push({ label: 'أفراد العائلة', val: text, icon: 'users' });
            }

            const social = formatList(c.social_status);
            if (social) items.push({ label: 'الحالة الاجتماعية', val: social, icon: 'heart' });

            const neighborhoods = formatList(c.neighborhoods);
            if (neighborhoods) items.push({ label: 'الأحياء المستهدفة', val: neighborhoods, icon: 'map-marker-alt' });

            if (c.min_age || c.max_age) {
                let text = '';
                if (c.min_age && c.max_age) text = `بين ${c.min_age} و ${c.max_age} سنة`;
                else if (c.min_age) text = `${c.min_age} سنة فأكثر`;
                else text = `أصغر من ${c.max_age} سنة`;
                items.push({ label: 'عمر رب الأسرة', val: text, icon: 'calendar-alt' });
            }

            if (c.condition !== null && c.condition !== "") {
                items.push({ label: 'حالة مرضية', val: c.condition == 1 ? 'نعم (يوجد)' : 'لا (لا يوجد)', icon: 'stethoscope' });
            }

            if (c.child_count) {
                items.push({ label: 'أدنى عدد أطفال', val: c.child_count, icon: 'baby' });
            }

            if (c.child_min_age || c.child_max_age) {
                let text = '';
                if (c.child_min_age && c.child_max_age) text = `من ${c.child_min_age} إلى ${c.child_max_age} سنة`;
                else if (c.child_min_age) text = `${c.child_min_age} سنة فأكثر`;
                else text = `أصغر من ${c.child_max_age} سنة`;
                items.push({ label: 'عمر الأطفال', val: text, icon: 'child' });
            }

            if (c.disability !== null && c.disability !== "") {
                items.push({ label: 'وجود إعاقة', val: c.disability == 1 ? 'نعم' : 'لا يشترط', icon: 'wheelchair' });
            }

            if (c.chronic !== null && c.chronic !== "") {
                items.push({ label: 'مرض مزمن', val: c.chronic == 1 ? 'نعم' : 'لا يشترط', icon: 'pills' });
            }

            if (items.length > 0) {
                items.forEach(item => {
                    $list.append(`
                        <div class="col-md-4 mb-2">
                            <div class="d-flex align-items-center p-1 border rounded bg-white shadow-sm" style="min-height: 48px;">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width: 28px; height: 28px; flex-shrink: 0;">
                                    <i class="fas fa-${item.icon}" style="font-size: 0.8rem;"></i>
                                </div>
                                <div style="overflow: hidden;">
                                    <div class="text-muted" style="font-size: 0.65rem; font-weight: bold; line-height: 1;">${item.label}</div>
                                    <div class="text-dark font-weight-bold text-truncate" style="font-size: 0.75rem;" title="${item.val}">${item.val}</div>
                                </div>
                            </div>
                        </div>
                    `);
                });
                hasCriteria = true;
            }

            if (c.notes) {
                $('#criteria-notes-text').text(c.notes);
                $notesDiv.show();
                hasCriteria = true;
            }

            if (hasCriteria) {
                $container.fadeIn();
            } else {
                $container.hide();
            }
        }

        function startTimer(deadlineISO) {
            clearInterval(timerInterval);
            if (!deadlineISO) {
                $('#project-countdown').fadeOut();
                return;
            }

            const deadlineTime = new Date(deadlineISO).getTime();

            function refreshTimer() {
                const now = new Date().getTime();
                const diff = deadlineTime - now;

                if (diff <= 0) {
                    clearInterval(timerInterval);
                    $('#timer-days, #timer-hours, #timer-minutes, #timer-seconds').text('00');
                    $('#project-countdown').addClass('deadline-passed');

                    Swal.fire({
                        title: 'انتهى الوقت!',
                        text: 'لقد انتهى موعد تقديم الطلبات لهذا المشروع ولا يمكن الاستمرار.',
                        icon: 'error',
                        confirmButtonText: 'حسناً'
                    }).then(() => {
                        window.location.reload();
                    });
                    return;
                }

                const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);

                $('#timer-days').text(String(d).padStart(2, '0'));
                $('#timer-hours').text(String(h).padStart(2, '0'));
                $('#timer-minutes').text(String(m).padStart(2, '0'));
                $('#timer-seconds').text(String(s).padStart(2, '0'));

                $('#project-countdown').fadeIn().css('display', 'block');
            }

            refreshTimer();
            timerInterval = setInterval(refreshTimer, 1000);
        }

        $('#project-selector').on('change', function() {
            const projectId = $(this).val();
            const deadline = window.projectDeadlines && window.projectDeadlines[projectId] ? window.projectDeadlines[projectId] : null;
            const limit = window.projectLimits && window.projectLimits[projectId] ? window.projectLimits[projectId] : null;

            startTimer(deadline);
            displayCriteria(projectId);

            if (limit) {
                $('#project-limit-info').fadeIn();
                $('#limit-value').text(limit);
            } else {
                $('#project-limit-info').fadeOut();
            }
        }).trigger('change');
    });
</script>
@endpush

