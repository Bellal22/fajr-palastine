{{ BsForm::resource('people')->get(url()->current()) }}

@component('dashboard::components.box')
    @slot('title')
        <i class="fas fa-search"></i> @lang('people.filter')
    @endslot

    <div class="row">
        {{-- البحث بالهوية --}}
        <div class="col-12 mb-1 mt-1">
            <h6 class="text-primary mb-0">
                <i class="fas fa-id-card"></i> @lang('people.sections.id_search')
            </h6>
            <hr class="mt-1 mb-2">
        </div>

        <div class="col-md-12 mb-2">
            <label class="mb-1">
                <i class="fas fa-id-card"></i> @lang('people.attributes.id_num')
            </label>
            {{ BsForm::textarea('id_num')
                ->value(request('id_num'))
                ->attribute('class', 'form-control id-numbers-input')
                ->attribute('style', 'height: 38px; min-height: 38px; max-height: 200px; overflow-y: auto; resize: vertical; line-height: 1.5; transition: height 0.2s ease; white-space: nowrap;')
                ->attribute('rows', '1')
                ->attribute('placeholder', trans('people.placeholders.id_num_placeholder'))
                ->label(false) }}
        </div>

        @php
            $currentRouteName = Route::currentRouteName();
        @endphp

        {{-- المسؤول والمندوب --}}
        @if ($currentRouteName == 'dashboard.people.index' || $currentRouteName == 'dashboard.people.view')
            <div class="col-12 mb-1 mt-1">
                <h6 class="text-primary mb-0">
                    <i class="fas fa-user-tie"></i> @lang('people.sections.responsible_delegate')
                </h6>
                <hr class="mt-1 mb-2">
            </div>

            @if ($currentRouteName == 'dashboard.people.index')
                <div class="col-md-6 mb-2">
                    <label class="mb-1">
                        <i class="fas fa-user-tie"></i> @lang('people.placeholders.area_responsible_label')
                    </label>
                    <?php
                        $areaResponsiblesQuery = \App\Models\AreaResponsible::query();

                        if (auth()->user()?->isSupervisor()) {
                            $areaResponsiblesQuery->where('id', auth()->user()->id);
                        }

                        $areaResponsiblesOptions = $areaResponsiblesQuery
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();

                        $areaResponsiblesOptions = ['' => trans('people.placeholders.select_area_responsible')] + $areaResponsiblesOptions;
                    ?>
                    {{ BsForm::select(
                        'area_responsible_id',
                        $areaResponsiblesOptions,
                        request('area_responsible_id'),
                        [
                            'id' => 'area_responsible_select',
                            'data-url' => route('dashboard.blocks.byAreaResponsible')
                        ]
                    )->label(false) }}
                </div>

                <div class="col-md-6 mb-2" style="display: none;">
                    <label class="mb-1">
                        <i class="fas fa-users"></i> @lang('people.placeholders.block_label')
                    </label>
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
                    {{ BsForm::select(
                        'block_id',
                        $blocksOptions,
                        request('block_id'),
                        ['id' => 'block_select']
                    )->label(false) }}
                </div>
            @endif

            @if ($currentRouteName == 'dashboard.people.view')
                @if(auth()->user()?->isAdmin())
                    <div class="col-md-6 mb-2">
                        <label class="mb-1">
                            <i class="fas fa-user-tie"></i> @lang('people.placeholders.area_responsible_label')
                        </label>
                        <?php
                            $areaResponsiblesOptions = \App\Models\AreaResponsible::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray();

                            $areaResponsiblesOptions = ['' => trans('people.placeholders.select_area_responsible')] + $areaResponsiblesOptions;
                        ?>
                        {{ BsForm::select(
                            'area_responsible_id',
                            $areaResponsiblesOptions,
                            request('area_responsible_id'),
                            [
                                'id' => 'area_responsible_select',
                                'data-url' => route('dashboard.blocks.byAreaResponsible')
                            ]
                        )->label(false) }}
                    </div>
                @endif

                <div class="col-md-6 mb-2">
                    <label class="mb-1">
                        <i class="fas fa-users"></i> @lang('people.placeholders.block_label')
                    </label>
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
                    {{ BsForm::select(
                        'block_id',
                        $blocksOptions,
                        request('block_id'),
                        ['id' => 'block_select']
                    )->label(false) }}
                </div>
            @endif
        @endif

        @if (! auth()->user()?->isSupervisor())
            {{-- المعلومات الشخصية --}}
            <div class="col-12 mb-1 mt-2">
                <h6 class="text-primary mb-0">
                    <i class="fas fa-user-circle"></i> @lang('people.sections.personal_info')
                </h6>
                <hr class="mt-1 mb-2">
            </div>

            <div class="col-md-3 mb-2">
                <label class="mb-1">
                    <i class="fas fa-user"></i> @lang('people.attributes.first_name')
                </label>
                <input type="text"
                       name="first_name"
                       class="form-control"
                       placeholder="@lang('people.placeholders.first_name')"
                       value="{{ request('first_name') }}">
            </div>

            <div class="col-md-3 mb-2">
                <label class="mb-1">
                    <i class="fas fa-user"></i> @lang('people.attributes.father_name')
                </label>
                <input type="text"
                       name="father_name"
                       class="form-control"
                       placeholder="@lang('people.placeholders.father_name')"
                       value="{{ request('father_name') }}">
            </div>

            <div class="col-md-3 mb-2">
                <label class="mb-1">
                    <i class="fas fa-user"></i> @lang('people.attributes.grandfather_name')
                </label>
                <input type="text"
                       name="grandfather_name"
                       class="form-control"
                       placeholder="@lang('people.placeholders.grandfather_name')"
                       value="{{ request('grandfather_name') }}">
            </div>

            <div class="col-md-3 mb-2">
                <label class="mb-1">
                    <i class="fas fa-user"></i> @lang('people.attributes.family_name')
                </label>
                <input type="text"
                       name="family_name"
                       class="form-control"
                       placeholder="@lang('people.placeholders.family_name')"
                       value="{{ request('family_name') }}">
            </div>

            <div class="col-md-6 mb-2">
                <label class="mb-1">
                    <i class="fas fa-venus-mars"></i> @lang('people.attributes.gender')
                </label>
                {{ BsForm::select('gender', [
                    'ذكر' => 'ذكر',
                    'أنثى' => 'أنثى',
                ])
                ->value(request('gender'))
                ->placeholder(trans('people.placeholders.select_gender'))
                ->label(false) }}
            </div>

            <div class="col-md-6 mb-2">
                <label class="mb-1">
                    <i class="fas fa-heart"></i> @lang('people.attributes.social_status')
                </label>
                {{ BsForm::select('social_status', [
                    'single' => 'أعزب / عزباء',
                    'married' => 'متزوج / متزوجة',
                    'polygamous' => 'متعدد الزوجات',
                    'divorced' => 'مطلق / مطلقة',
                    'widowed' => 'أرمل / أرملة',
                ])
                ->value(request('social_status'))
                ->placeholder(trans('people.placeholders.select_social_status'))
                ->label(false) }}
            </div>

            {{-- معلومات السكن --}}
            <div class="col-12 mb-1 mt-2">
                <h6 class="text-primary mb-0">
                    <i class="fas fa-map-marked-alt"></i> @lang('people.sections.housing_info')
                </h6>
                <hr class="mt-1 mb-2">
            </div>

            <div class="col-md-6 mb-2">
                <label class="mb-1">
                    <i class="fas fa-city"></i> @lang('people.attributes.current_city')
                </label>
                {{ BsForm::select('current_city', [
                    'khanYounis' => 'خانيونس',
                    'rafah' => 'رفح',
                ])
                ->value(request('current_city'))
                ->placeholder(trans('people.placeholders.select_current_city'))
                ->label(false) }}
            </div>

            <div class="col-md-6 mb-2">
                <label class="mb-1">
                    <i class="fas fa-map-marker-alt"></i> @lang('people.attributes.neighborhood')
                </label>
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
                ])
                ->value(request('neighborhood'))
                ->placeholder(trans('people.placeholders.select_neighborhood'))
                ->label(false) }}
            </div>

            {{-- معلومات الطفل --}}
            <div class="col-12 mb-1 mt-2">
                <h6 class="text-primary mb-0">
                    <i class="fas fa-child"></i> @lang('people.sections.child_info')
                </h6>
                <hr class="mt-1 mb-2">
            </div>

            <div class="col-md-6 mb-2">
                <label class="mb-1">
                    <i class="fas fa-id-card"></i> @lang('people.attributes.child_id_num')
                </label>
                <input type="text"
                       name="child_id_num"
                       class="form-control"
                       placeholder="@lang('people.placeholders.child_id_num')"
                       value="{{ request('child_id_num') }}">
            </div>

            <div class="col-md-6 mb-2">
                <label class="mb-1">
                    <i class="fas fa-calendar"></i> @lang('people.attributes.child_dob_exact')
                </label>
                <input type="date"
                       name="child_dob"
                       class="form-control"
                       max="{{ now()->toDateString() }}"
                       value="{{ request('child_dob') }}">
            </div>

            <div class="col-md-6 mb-2">
                <label class="mb-1">
                    <i class="fas fa-calendar-alt"></i> @lang('people.attributes.child_dob_from')
                </label>
                <input type="date"
                       name="child_dob_from"
                       class="form-control"
                       max="{{ now()->toDateString() }}"
                       value="{{ request('child_dob_from') }}">
            </div>

            <div class="col-md-6 mb-2">
                <label class="mb-1">
                    <i class="fas fa-calendar-alt"></i> @lang('people.attributes.child_dob_to')
                </label>
                <input type="date"
                       name="child_dob_to"
                       class="form-control"
                       max="{{ now()->toDateString() }}"
                       value="{{ request('child_dob_to') }}">
            </div>

            <div class="col-md-6 mb-2">
                <label class="mb-1">
                    <i class="fas fa-hourglass-half"></i> @lang('people.attributes.child_age_months_from')
                </label>
                <input type="number"
                       name="child_age_months_from"
                       class="form-control"
                       min="0"
                       max="300"
                       placeholder="@lang('people.placeholders.age_example', ['age' => 6])"
                       value="{{ request('child_age_months_from') }}">
            </div>

            <div class="col-md-6 mb-2">
                <label class="mb-1">
                    <i class="fas fa-hourglass-half"></i> @lang('people.attributes.child_age_months_to')
                </label>
                <input type="number"
                       name="child_age_months_to"
                       class="form-control"
                       min="0"
                       max="300"
                       placeholder="@lang('people.placeholders.age_example', ['age' => 24])"
                       value="{{ request('child_age_months_to') }}">
            </div>

            {{-- معلومات الأسرة والصحة --}}
            <div class="col-12 mb-1 mt-2">
                <h6 class="text-primary mb-0">
                    <i class="fas fa-home"></i> @lang('people.sections.family_health')
                </h6>
                <hr class="mt-1 mb-2">
            </div>

            <div class="col-md-3 mb-2">
                <label class="mb-1">
                    <i class="fas fa-users"></i> @lang('people.attributes.family_members_min')
                </label>
                <input type="number"
                       name="family_members_min"
                       class="form-control"
                       placeholder="@lang('people.placeholders.minimum')"
                       value="{{ request('family_members_min') }}">
            </div>

            <div class="col-md-3 mb-2">
                <label class="mb-1">
                    <i class="fas fa-users"></i> @lang('people.attributes.family_members_max')
                </label>
                <input type="number"
                       name="family_members_max"
                       class="form-control"
                       placeholder="@lang('people.placeholders.maximum')"
                       value="{{ request('family_members_max') }}">
            </div>

            <div class="col-md-6 mb-2">
                <label class="mb-1">
                    <i class="fas fa-notes-medical"></i> @lang('people.attributes.has_condition')
                </label>
                {{ BsForm::select('has_condition', [
                    '0' => trans('people.health_status.healthy'),
                    '1' => trans('people.health_status.has_condition'),
                ])
                ->value(request('has_condition'))
                ->placeholder(trans('people.placeholders.select_health_status'))
                ->label(false) }}
            </div>
        @endif

        {{-- إعدادات العرض --}}
        <div class="col-12 mb-1 mt-2">
            <h6 class="text-primary mb-0">
                <i class="fas fa-cog"></i> @lang('people.sections.display_settings')
            </h6>
            <hr class="mt-1 mb-2">
        </div>

        <div class="col-md-6 mb-2">
            <label class="mb-1">
                <i class="fas fa-list-ol"></i> @lang('people.perPage')
            </label>
            {{ BsForm::number('perPage')
                ->value(request('perPage', 15))
                ->min(1)
                ->label(false) }}
        </div>
    </div>

    @slot('footer')
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i>
            @lang('people.actions.filter')
        </button>

        <button type="button" class="btn btn-secondary ml-2" id="resetFilters">
            <i class="fas fa-eraser"></i>
            @lang('people.actions.empty_filters')
        </button>
    @endslot
@endcomponent

{{-- Modal for Not Found IDs --}}
<div class="modal fade" id="not-found-modal" tabindex="-1" role="dialog" aria-labelledby="notFoundModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="notFoundModalTitle">
                    @lang('people.modal.search_results_title')
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @if(!empty($notFoundIds))
                    <div class="mb-4">
                        <h6 class="font-weight-bold">
                            <i class="fas fa-times-circle"></i>
                            @lang('people.modal.not_found_ids')
                        </h6>
                        <ul class="mb-0">
                            @foreach($notFoundIds as $id)
                                <li>{{ $id }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($unavailableIds))
                    <div class="mb-4">
                        <h6 class="font-weight-bold">
                            <i class="fas fa-exclamation-triangle"></i>
                            @lang('people.modal.unavailable_ids')
                        </h6>
                        <ul class="mb-0">
                            @foreach($unavailableIds as $id)
                                <li>{{ $id }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($processedIds))
                    <div class="mb-4">
                        <h6 class="font-weight-bold">
                            <i class="fas fa-check-circle"></i>
                            @lang('people.modal.processed_ids')
                        </h6>
                        <ul class="mb-0">
                            @foreach($processedIds as $id)
                                <li>{{ $id }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(empty($notFoundIds) && empty($unavailableIds) && empty($processedIds))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        @lang('people.modal.all_ids_found')
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="clear-session-button">
                    @lang('people.modal.confirm')
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    @if(!empty($notFoundIds) || !empty($unavailableIds) || !empty($processedIds))
        $('#not-found-modal').modal('show');
    @endif

    $('#clear-session-button').on('click', function() {
        $.ajax({
            url: '{{ route("dashboard.people.clear") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#not-found-modal').modal('hide');
            },
            error: function(xhr) {
                alert('{{ trans("people.modal.error_clearing_session") }}');
            }
        });
    });

    $('#resetFilters').on('click', function() {
        $('form')[0].reset();
        history.pushState({}, document.title, window.location.pathname);
        location.reload();
    });

    const areaSelect = $('#area_responsible_select');
    const blockSelect = $('#block_select');

    if (areaSelect.length) {
        areaSelect.on('change', function() {
            const url = $(this).data('url');
            const areaId = $(this).val();

            if (areaId) {
                blockSelect.parent().show();

                $.get(`${url}?area_responsible_id=${areaId}`, function(data) {
                    blockSelect.html('<option value="">{{ trans("people.placeholders.select_block") }}</option>');

                    $.each(data, function(id, name) {
                        blockSelect.append(`<option value="${id}">${name}</option>`);
                    });
                });
            } else {
                blockSelect.parent().hide();
            }
        });

        if (areaSelect.val()) {
            areaSelect.trigger('change');
        }
    }

    $('.id-numbers-input').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});
</script>
@endpush

{{ BsForm::close() }}
