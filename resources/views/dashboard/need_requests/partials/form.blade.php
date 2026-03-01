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

