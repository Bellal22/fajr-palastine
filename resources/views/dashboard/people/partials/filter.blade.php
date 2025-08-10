{{ BsForm::resource('people')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title', trans('people.filter'))

    <div class="row">

        <div class="col-md-12">
            {{ BsForm::textarea('id_num')
                ->value(request('id_num'))
                ->label(trans('people.attributes.id_num'))
                ->attribute('class', 'form-control id-numbers-input')
                ->attribute('style', 'height: 38px; min-height: 38px; max-height: 200px; overflow-y: auto; resize: vertical; line-height: 1.5; transition: height 0.2s ease; white-space: nowrap;')
                ->attribute('rows', '1')
                ->attribute('placeholder', trans('people.placeholders.id_num_placeholder')) }}
        </div>

        <div class="modal fade" id="not-found-modal" tabindex="-1" role="dialog" aria-labelledby="notFoundModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="notFoundModalTitle">نتائج البحث عن الهويات</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if(!empty($notFoundIds))
                            <div class="mb-4">
                                <h6 class="font-weight-bold">الهويات التالية لم يتم العثور عليها:</h6>
                                <ul>
                                    @foreach($notFoundIds as $id)
                                        <li>{{ $id }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!empty($unavailableIds))
                            <div class="mb-4">
                                <h6 class="font-weight-bold">الهويات التالية مسجلة ولكن غير متاحة في منطقتك:</h6>
                                <ul>
                                    @foreach($unavailableIds as $id)
                                        <li>{{ $id }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(empty($notFoundIds) && empty($unavailableIds))
                            <div class="alert alert-success">
                                جميع الهويات المدخلة موجودة ومتاحة في منطقتك.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="clear-session-button">موافق</button>
                    </div>
                </div>
            </div>
        </div>

        @php
            $currentRouteName = Route::currentRouteName();
        @endphp

        @if ($currentRouteName == 'dashboard.people.index')
            <div class="col-md-6 form-group">
                <label for="area_responsible_id">{{ trans('people.placeholders.area_responsible_label') }}</label>
                <?php
                    $areaResponsiblesQuery = \App\Models\AreaResponsible::query();
                    if (auth()->user()?->isSupervisor()) {
                        // إذا كان المستخدم مشرف، يظهر فقط المسؤول الذي يتوافق مع المستخدم
                        $areaResponsiblesQuery->where('id', auth()->user()->id);
                    }
                    $areaResponsiblesOptions = $areaResponsiblesQuery
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray();
                    $areaResponsiblesOptions = ['' => trans('people.placeholders.select_area_responsible')] + $areaResponsiblesOptions;
                ?>
                {{ BsForm::select('area_responsible_id', $areaResponsiblesOptions, request('area_responsible_id'), [
                    'id' => 'area_responsible_select',
                    'data-url' => route('dashboard.blocks.byAreaResponsible')
                ]) }}
            </div>

            <div class="col-md-6 form-group" style="display: none;">
                <label for="block_id">{{ trans('people.placeholders.block_label') }}</label>
                <?php
                    $blocksQuery = \App\Models\Block::query();
                    if (auth()->user()?->isSupervisor()) {
                        $blocksQuery->where('area_responsible_id', auth()->user()->id);
                    } elseif (auth()->user()?->isAdmin() && request('area_responsible_id')) {
                        $blocksQuery->where('area_responsible_id', request('area_responsible_id'));
                    }
                    $blocksOptions = $blocksQuery
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray();
                    $blocksOptions = ['' => trans('people.placeholders.select_block')] + $blocksOptions;
                ?>
                {{ BsForm::select('block_id',
                    $blocksOptions,
                    request('block_id'),
                    ['id' => 'block_select']
                ) }}
            </div>
        @endif

        @if ($currentRouteName == 'dashboard.people.view')
            @if(auth()->user()?->isAdmin())
                <div class="col-md-6 form-group">
                    <label for="area_responsible_id">{{ trans('people.placeholders.area_responsible_label') }}</label>
                    <?php
                        $areaResponsiblesOptions = \App\Models\AreaResponsible::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                        $areaResponsiblesOptions = ['' => trans('people.placeholders.select_area_responsible')] + $areaResponsiblesOptions;
                    ?>
                    {{ BsForm::select('area_responsible_id', $areaResponsiblesOptions, request('area_responsible_id'), [
                        'id' => 'area_responsible_select',
                        'data-url' => route('dashboard.blocks.byAreaResponsible')
                    ]) }}
                </div>
            @endif

            <div class="col-md-6 form-group">
                <label for="block_id">{{ trans('people.placeholders.block_label') }}</label>
                <?php
                    $blocksQuery = \App\Models\Block::query();
                    if (auth()->user()?->isSupervisor()) {
                        $blocksQuery->where('area_responsible_id', auth()->user()->id);
                    } elseif (auth()->user()?->isAdmin() && request('area_responsible_id')) {
                        $blocksQuery->where('area_responsible_id', request('area_responsible_id'));
                    }
                    $blocksOptions = $blocksQuery
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray();
                    $blocksOptions = ['' => trans('people.placeholders.select_block')] + $blocksOptions;
                ?>
                {{ BsForm::select('block_id',
                    $blocksOptions,
                    request('block_id'),
                    ['id' => 'block_select']
                ) }}
            </div>
        @endif

        @if (! auth()->user()?->isSupervisor())
            <div class="col-md-6">
                {{ BsForm::select('social_status', [
                    'single' => 'أعزب / عزباء',
                    'married' => 'متزوج / متزوجة',
                    'polygamous'=>'متعدد الزوجات',
                    'divorced' => 'مطلق / مطلقة',
                    'widowed' => 'أرمل / أرملة',
                ])->value(request('social_status'))->placeholder('اختر الحالة الاجتماعية') }}
            </div>

            <div class="col-md-3">
                <label for="first_name" class="form-label">@lang('people.attributes.first_name')</label>
                <input type="text" name="first_name" id="first_name"
                    class="form-control"
                    placeholder="@lang('people.placeholders.first_name')"
                    value="{{ request('first_name') }}">
            </div>

            <div class="col-md-3">
                <label for="father_name" class="form-label">@lang('people.attributes.father_name')</label>
                <input type="text" name="father_name" id="father_name"
                    class="form-control"
                    placeholder="@lang('people.placeholders.father_name')"
                    value="{{ request('father_name') }}">
            </div>

            <div class="col-md-3">
                <label for="grandfather_name" class="form-label">@lang('people.attributes.grandfather_name')</label>
                <input type="text" name="grandfather_name" id="grandfather_name"
                    class="form-control"
                    placeholder="@lang('people.placeholders.grandfather_name')"
                    value="{{ request('grandfather_name') }}">
            </div>

            <div class="col-md-3">
                <label for="family_name" class="form-label">@lang('people.attributes.family_name')</label>
                <input type="text" name="family_name" id="family_name"
                    class="form-control"
                    placeholder="@lang('people.placeholders.family_name')"
                    value="{{ request('family_name') }}">
            </div>

            <div class="col-md-6">
                {{ BsForm::select('gender', [
                    'ذكر' => 'ذكر',
                    'أنثى' => 'أنثى',
                ])->value(request('gender'))
                ->placeholder('اختر الجنس') }}
            </div>

            <div class="col-md-6">
                {{ BsForm::select('current_city', [
                    'khanYounis' => 'خانيونس',
                    'rafah' => 'رفح',
                ])->value(request('current_city'))
                ->placeholder('اختر المحافظة الحالية') }}
            </div>

            <div class="col-md-6">
                {{ BsForm::select('neighborhood', [
                    'qizanAlNajjar' => 'قيزان النجار',
                    'qizanAbuRashwan' => 'قيزان أبو رشوان',
                    'juraAlLoot' => 'جورة اللوت',
                    'sheikhNasser' => 'الشيخ ناصر',
                    'maAn' => 'معن',
                    'alManaraNeighborhood' => 'حي المنارة',
                    'easternLine' => 'الخط الشرقي',
                    'westernLine' => 'الخط الغربي',
                    'alMahatta' => 'المحطة',
                    'alKatiba' => 'الكتبية',
                    'alBatanAlSameen' => 'البطن السمين',
                    'alMaskar' => 'المعسكر',
                    'alMashroo' => 'المشروع',
                    'hamidCity' => 'مدينة حمد',
                    'alMawasi' => 'المواصي',
                    'alQarara' => 'القرارة',
                    'eastKhanYounis' => 'شرق خانيونس',
                    'downtown' => 'البلد',
                    'mirage' => 'الميراج',
                    'european' => 'الأوروبي',
                    'alFakhari' => 'الفخاري',
                    'masbah' => 'المسبح',
                    'khirbetAlAdas' => 'خربة العدس',
                    'alJaninehNeighborhood' => 'حي الجنينة',
                    'alAwda' => 'العودة',
                    'alZohourNeighborhood' => 'حي الزهور',
                    'brazilianHousing' => 'الإسكان البرازيلي',
                    'telAlSultan' => 'تل السلطان',
                    'alShabouraNeighborhood' => 'حي الشابورة',
                    'rafahProject' => 'مشروع رفح',
                    'zararRoundabout' => 'دوار زعارير',
                ])->value(request('neighborhood'))->placeholder('اختر الحي/المنطقة') }}
            </div>

            <div class="col-md-3">
                <label for="dob_from" class="form-label">تاريخ الميلاد - من</label>
                <input type="date" name="dob_from" id="dob_from"
                        class="form-control "
                        max="{{ now()->toDateString() }}"
                        placeholder="اختر تاريخ الميلاد من"
                        value="{{ request('dob_from', $person->dob_from ?? '') }}">
            </div>

            <div class="col-md-3">
                <label for="dob_to" class="form-label ">تاريخ الميلاد - إلى</label>
                <input type="date" name="dob_to" id="dob_to"
                        class="form-control"
                        max="{{ now()->toDateString() }}"
                        placeholder="اختر تاريخ الميلاد إلى"
                        value="{{ request('dob_to', $person->dob_to ?? '') }}">
            </div>

            <div class="col-md-3">
                <label for="family_members_min" class="form-label">عدد أفراد الأسرة من</label>
                <input type="number" name="family_members_min" id="family_members_min"
                        class="form-control"
                        placeholder="عدد أفراد الأسرة الحد الأدنى"
                        value="{{ request('family_members_min') }}">
            </div>

            <div class="col-md-3">
                <label for="family_members_max" class="form-label">عدد أفراد الأسرة إلى</label>
                <input type="number" name="family_members_max" id="family_members_max"
                        class="form-control"
                        placeholder="عدد أفراد الأسرة الحد الأقصى"
                        value="{{ request('family_members_max') }}">
            </div>

            <div class="col-md-6">
                {{ BsForm::select('has_condition', [
                    '0' => 'صحيح',
                    '1' => 'يعاني',
                ])->value(request('has_condition'))
                ->placeholder('اختر الحالة') }}
            </div>
        @endif

        <div class="col-md-6">
            {{ BsForm::number('perPage')
                ->value(request('perPage', 15))
                ->min(1)
                ->label(trans('people.perPage')) }}
        </div>

    @slot('footer')
    <button type="submit" class="btn btn-primary btn-sm">
        <i class="fas fa fa-fw fa-filter"></i>
        @lang('people.actions.filter')
    </button>

    <button type="button" class="btn btn-secondary btn-sm" id="resetFilters">
        <i class="fas fa-fw fa-times"></i>
        @lang('people.actions.empty_filters')
    </button>

@endSlot

@endcomponent
@push('scripts')
    <script>

        $(document).ready(function() {
            @if(!empty($notFoundIds) || !empty($unavailableIds))
                $('#not-found-modal').modal('show');
            @endif

            @if(session('notFoundIds') && count(session('notFoundIds')) > 0)
                let html = '<p>الهويات التالية لم يتم العثور عليها:</p><ul>';
                @foreach(session('notFoundIds') as $id)
                    html += '<li>{{ $id }}</li>';
                @endforeach
                html += '</ul>';
                $('#not-found-message').html(html);
            @endif
            $('#clear-session-button').on('click', function() {
                $.ajax({
                    url: '{{ route("dashboard.people.clear") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('Session cleared:', response);
                        $('#not-found-modal').modal('hide');
                    },
                    error: function(xhr) {
                        console.error('Error clearing session:', xhr);
                        alert('حدث خطأ أثناء محاولة تفريغ الجلسة.');
                    }
                });
            });
        });

        document.getElementById('resetFilters').addEventListener('click', function() {
            const form = document.querySelector('form');
            if(form) {
                form.reset();
            }

            const cleanUrl = window.location.origin + window.location.pathname;

            history.pushState({}, document.title, cleanUrl);

            window.location.reload();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const areaSelect = document.getElementById('area_responsible_select');
            const blockSelect = document.getElementById('block_select');

            if (areaSelect) {
                areaSelect.addEventListener('change', function() {
                    const url = this.dataset.url;
                    const areaId = this.value;

                    fetch(`${url}?area_responsible_id=${areaId}`)
                        .then(response => response.json())
                        .then(data => {
                            blockSelect.innerHTML = '';

                            const defaultOption = new Option('اختر المندوب', '');
                            blockSelect.add(defaultOption);
                            Object.entries(data).forEach(([id, name]) => {
                                const option = new Option(name, id);
                                blockSelect.add(option);
                            });
                        });
                });

                if (areaSelect.value) {
                    areaSelect.dispatchEvent(new Event('change'));
                }
            }
        });

        const textarea = document.querySelector('.id-numbers-input');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

    </script>
@endpush
{{ BsForm::close() }}
