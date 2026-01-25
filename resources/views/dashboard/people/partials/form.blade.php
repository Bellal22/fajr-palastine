@include('dashboard.errors')

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title mb-0">
            <i class="fas fa-user-plus ml-2"></i>
            البيانات الشخصية
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                {{ BsForm::text('first_name')->label('الاسم الأول')->required()->attribute('oninput', "validateArabicInput('first_name')") }}
            </div>
            <div class="col-md-3">
                {{ BsForm::text('father_name')->label('اسم الأب')->required()->attribute('oninput', "validateArabicInput('father_name')") }}
            </div>
            <div class="col-md-3">
                {{ BsForm::text('grandfather_name')->label('اسم الجد')->required()->attribute('oninput', "validateArabicInput('grandfather_name')") }}
            </div>
            <div class="col-md-3">
                {{ BsForm::text('family_name')->label('اسم العائلة')->required()->attribute('oninput', "validateArabicInput('family_name')") }}
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                {{ BsForm::number('id_num')->label('رقم الهوية')->required()->attribute('oninput', "validateIdOnInput('id_num')") }}
                <span id="id_num_error" class="text-danger" style="display:none; font-size: 0.9rem;"></span>
            </div>
            <div class="col-md-3">
                @php
                    $genderOptions = isset($chooses['gender']) ? $chooses['gender']->pluck('name', 'name')->toArray() : ['ذكر' => 'ذكر', 'أنثى' => 'أنثى'];
                @endphp
                {{ BsForm::select('gender')->options($genderOptions)->label('الجنس')->placeholder('اختر الجنس')->required() }}
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>تاريخ الميلاد <span class="text-danger">*</span></label>
                    <input type="date" name="dob" id="dob" class="form-control" value="{{ old('dob', (isset($person) && $person->dob) ? $person->dob->format('Y-m-d') : '') }}" required>
                </div>
                <span id="dob_error" class="text-danger" style="display:none; font-size: 0.9rem;"></span>
            </div>
            <div class="col-md-3">
                {{ BsForm::text('phone')->label('رقم الجوال')->placeholder('059-xxxxxxx')->required()->attribute('dir', 'ltr')->attribute('oninput', "validatePhoneInput()") }}
                <span id="phone_error" class="text-danger" style="display:none; font-size: 0.9rem;"></span>
            </div>
        </div>
    </div>
</div>

{{-- الحالة الاجتماعية والصحية --}}
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h3 class="card-title mb-0">
            <i class="fas fa-info-circle ml-2"></i>
            الحالة الاجتماعية والصحية
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                @php
                    $socialStatusOptions = isset($chooses['social_status']) ? $chooses['social_status']->mapWithKeys(fn($i) => [$i->slug ?: $i->name => $i->name])->toArray() : [];
                @endphp
                {{ BsForm::select('social_status')
                    ->options($socialStatusOptions)
                    ->label('الحالة الاجتماعية')
                    ->placeholder('اختر الحالة')
                    ->required()
                    ->attribute('id', 'social_status')
                }}
            </div>
            <div class="col-md-6">
                @php
                    $employmentStatusOptions = isset($chooses['employment_status']) ? $chooses['employment_status']->mapWithKeys(fn($i) => [$i->slug ?: $i->name => $i->name])->toArray() : ['لا يعمل' => 'لا يعمل', 'موظف' => 'موظف', 'عامل' => 'عامل'];
                @endphp
                {{ BsForm::select('employment_status')
                    ->options($employmentStatusOptions)
                    ->label('حالة العمل')
                    ->required()
                }}
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>هل لديك حالة صحية / مرض مزمن / إصابة؟</label>
                    <select name="has_condition" id="has_condition" class="form-control" onchange="toggleConditionDescription()">
                        <option value="0" {{ (isset($person) && $person->has_condition == 0) ? 'selected' : '' }}>لا</option>
                        <option value="1" {{ (isset($person) && $person->has_condition == 1) ? 'selected' : '' }}>نعم</option>
                    </select>
                </div>
            </div>
            <div class="col-md-8" id="condition_description_group" style="display: {{ (isset($person) && $person->has_condition == 1) ? 'block' : 'none' }};">
                {{ BsForm::textarea('condition_description')->rows(2)->label('وصف الحالة الصحية')->attribute('id', 'condition_description') }}
            </div>
        </div>
    </div>
</div>

{{-- معلومات السكن --}}
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h3 class="card-title mb-0">
            <i class="fas fa-home ml-2"></i>
            معلومات السكن
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                {{ BsForm::select('city')
                    ->options($cities)
                    ->label('المحافظة الأصلية')
                    ->placeholder('اختر المحافظة')
                    ->required()
                }}
            </div>
            <div class="col-md-6">
                @php
                    $housingDamageOptions = isset($chooses['housing_damage_status']) ? $chooses['housing_damage_status']->mapWithKeys(fn($i) => [$i->slug ?: $i->name => $i->name])->toArray() : [];
                @endphp
                {{ BsForm::select('housing_damage_status')
                     ->options($housingDamageOptions)
                     ->label('حالة السكن السابق')
                     ->placeholder('اختر الحالة')
                     ->required()
                }}
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                {{ BsForm::select('current_city')
                     ->options($cities)
                     ->label('المحافظة الحالية')
                     ->placeholder('اختر المحافظة')
                     ->required()
                     ->attribute('id', 'current_city')
                }}
            </div>
            <div class="col-md-4">
                @php
                    $housingTypeOptions = isset($chooses['housing_type']) ? $chooses['housing_type']->mapWithKeys(fn($i) => [$i->slug ?: $i->name => $i->name])->toArray() : [];
                @endphp
                {{ BsForm::select('housing_type')
                     ->options($housingTypeOptions)
                     ->label('نوع السكن الحالي')
                     ->placeholder('اختر نوع السكن')
                     ->required()
                }}
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="neighborhood">الحي السكني</label>
                    <select name="neighborhood" id="neighborhood" class="form-control" required>
                        <option value="">اختر الحي السكني</option>
                        @if(isset($person) && $person->neighborhood)
                            <option value="{{ $person->neighborhood }}" selected>{{ $person->neighborhood }}</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
             <div class="col-md-4" id="areaResponsibleField" style="display:none;">
                <div class="form-group">
                    <label for="area_responsible_id">مسؤول المنطقة</label>
                     <select name="area_responsible_id" id="area_responsible_id" class="form-control">
                        <option value="">اختر المسؤول</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4" id="blockField" style="display:none;">
                <div class="form-group">
                    <label for="block_id">المندوب</label>
                     <select name="block_id" id="block_id" class="form-control">
                        <option value="">اختر المندوب</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                {{ BsForm::text('landmark')->label('أقرب معلم')->attribute('oninput', "validateArabicInput('landmark')") }}
            </div>
        </div>
    </div>
</div>

{{-- أفراد الأسرة --}}
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-users ml-2"></i>
                أفراد الأسرة
            </h3>
            <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#familyMemberModal">
                <i class="fas fa-plus ml-1"></i> إضافة فرد
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="family-table">
                <thead class="thead-dark">
                    <tr>
                        <th>الاسم الكامل</th>
                        <th>الهوية</th>
                        <th>القرابة</th>
                        <th>الميلاد</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody id="family-table-body">
                    @if(isset($person) && $person->familyMembers)
                        @foreach($person->familyMembers as $index => $member)
                            <tr data-index="{{ $index }}">
                                <td>
                                    {{ $member->first_name }} {{ $member->father_name }} {{ $member->grandfather_name }} {{ $member->family_name }}
                                    <input type="hidden" name="family_members[{{ $index }}][first_name]" value="{{ $member->first_name }}">
                                    <input type="hidden" name="family_members[{{ $index }}][father_name]" value="{{ $member->father_name }}">
                                    <input type="hidden" name="family_members[{{ $index }}][grandfather_name]" value="{{ $member->grandfather_name }}">
                                    <input type="hidden" name="family_members[{{ $index }}][family_name]" value="{{ $member->family_name }}">
                                    <input type="hidden" name="family_members[{{ $index }}][id_num]" value="{{ $member->id_num }}">
                                    <input type="hidden" name="family_members[{{ $index }}][dob]" value="{{ $member->dob }}">
                                    <input type="hidden" name="family_members[{{ $index }}][gender]" value="{{ $member->gender }}">
                                    <input type="hidden" name="family_members[{{ $index }}][relationship]" value="{{ $member->relationship }}">
                                    <input type="hidden" name="family_members[{{ $index }}][has_condition]" value="{{ $member->has_condition }}">
                                    <input type="hidden" name="family_members[{{ $index }}][condition_description]" value="{{ $member->condition_description }}">
                                    <input type="hidden" name="family_members[{{ $index }}][phone]" value="{{ $member->phone }}">
                                </td>
                                <td>{{ $member->id_num }}</td>
                                <td>{{ $member->relationship }}</td>
                                <td>{{ $member->dob }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-member-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal إضافة فرد للأسرة --}}
<div class="modal fade" id="familyMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus ml-2"></i>
                    إضافة فرد جديد
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                         <div class="form-group">
                            <label>الاسم الأول <span class="text-danger">*</span></label>
                            <input type="text" id="m_firstname" class="form-control">
                         </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                           <label>اسم الأب <span class="text-danger">*</span></label>
                           <input type="text" id="m_fathername" class="form-control">
                        </div>
                   </div>
                   <div class="col-md-3">
                        <div class="form-group">
                           <label>اسم الجد <span class="text-danger">*</span></label>
                           <input type="text" id="m_grandfathername" class="form-control">
                        </div>
                   </div>
                   <div class="col-md-3">
                        <div class="form-group">
                           <label>اسم العائلة <span class="text-danger">*</span></label>
                           <input type="text" id="m_familyname" class="form-control">
                        </div>
                   </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>صلة القرابة <span class="text-danger">*</span></label>
                            <select id="m_relationship" class="form-control">
                                <option value="">اختر الصلة</option>
                                @php
                                    $relChoices = isset($chooses['relationship']) ? $chooses['relationship'] : collect();
                                @endphp
                                @if($relChoices->count())
                                    @foreach($relChoices as $choice)
                                        <option value="{{ $choice->slug ?: $choice->name }}">{{ $choice->name }}</option>
                                    @endforeach
                                @else
                                    @foreach(['father'=>'أب', 'mother'=>'أم', 'brother'=>'أخ', 'sister'=>'أخت', 'husband'=>'زوج', 'wife'=>'زوجة', 'son'=>'ابن', 'daughter'=>'ابنة', 'others'=>'اخرون'] as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>رقم الهوية <span class="text-danger">*</span></label>
                            <input type="number" id="m_idnum" class="form-control" maxlength="9">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>تاريخ الميلاد <span class="text-danger">*</span></label>
                            <input type="date" id="m_dob" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>الجنس <span class="text-danger">*</span></label>
                            <select id="m_gender" class="form-control">
                                <option value="">اختر الجنس</option>
                                @if(isset($chooses['gender']))
                                    @foreach($chooses['gender'] as $choice)
                                        <option value="{{ $choice->name }}">{{ $choice->name }}</option>
                                    @endforeach
                                @else
                                    <option value="ذكر">ذكر</option>
                                    <option value="أنثى">أنثى</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6" id="m_phone_group" style="display:none">
                        <div class="form-group">
                            <label>رقم الجوال (للزوجة)</label>
                            <input type="text" id="m_phone" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>هل يوجد حالة صحية؟</label>
                            <select id="m_hascondition" class="form-control">
                                <option value="0">لا</option>
                                <option value="1">نعم</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8" id="m_condition_description_group" style="display:none">
                        <div class="form-group">
                            <label>وصف الحالة</label>
                            <textarea id="m_conditiondescription" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times ml-1"></i> إلغاء
                </button>
                <button type="button" class="btn btn-primary" id="add-member-confirm-btn">
                    <i class="fas fa-plus ml-1"></i> إضافة للقائمة
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Native Date Validation
        $('#dob').on('change', function() {
            const selectedDate = new Date($(this).val());
            const today = new Date();
            if (selectedDate > today) {
                alert('تاريخ الميلاد لا يمكن أن يكون في المستقبل');
                $(this).val('');
            }
        });

        // Validation Functions
        window.validateArabicInput = function(id) {
            const input = document.getElementById(id);
            const regex = /^[\u0600-\u06FF\s]+$/;
            if (input.value && !regex.test(input.value)) {
                // input.classList.add('is-invalid');
            } else {
                // input.classList.remove('is-invalid');
            }
        };

        window.validateIdOnInput = function(id) {
            // Basic length check
        };

        window.toggleConditionDescription = function() {
            const val = $('#has_condition').val();
            $ ('#condition_description_group').toggle(val == '1');
            if(val == '0') $('#condition_description').val('');
        };

        // Hierarchical Mapping Data
        const neighborhoodsData = @json($neighborhoodsGroupedByCity);

        // Pre-selected values from Model or Old Input
        const initialStates = {
            neighborhood: "{{ old('neighborhood', isset($person) ? $person->neighborhood : '') }}",
            area_responsible_id: "{{ old('area_responsible_id', isset($person) ? $person->area_responsible_id : '') }}",
            block_id: "{{ old('block_id', isset($person) ? $person->block_id : '') }}"
        };

        $('#current_city').on('change', function() {
            const city = $(this).val();
            const options = neighborhoodsData[city] || [];
            const $neighborhoodSelect = $('#neighborhood');
            
            $neighborhoodSelect.empty().append('<option value="">اختر الحي السكني</option>');
            options.forEach(opt => {
                const selected = (opt.value === initialStates.neighborhood) ? 'selected' : '';
                $neighborhoodSelect.append(`<option value="${opt.value}" ${selected}>${opt.label}</option>`);
            });

            // Reset initial neighborhood after first load
            initialStates.neighborhood = '';
            $neighborhoodSelect.trigger('change');
        });

        $('#neighborhood').on('change', function() {
            const neighborhood = $(this).val();
            const $responsibleSelect = $('#area_responsible_id');
            const $areaResField = $('#areaResponsibleField');
            
            if (!neighborhood) {
                $responsibleSelect.empty().append('<option value="">اختر المسؤول</option>');
                $areaResField.hide();
                $responsibleSelect.trigger('change');
                return;
            }

            // AJAX Method لجلب مسؤولي المنطقة حسب الحي السكني
            $.ajax({
                url: "{{ route('dashboard.ajax.getResponsiblesByNeighborhood') }}",
                data: { neighborhood_name: neighborhood },
                success: function(response) {
                    $responsibleSelect.empty().append('<option value="">اختر المسؤول</option>');
                    if (response.responsibles && response.responsibles.length > 0) {
                        $areaResField.show();
                        response.responsibles.forEach(r => {
                            const selected = (r.id == initialStates.area_responsible_id) ? 'selected' : '';
                            $responsibleSelect.append(`<option value="${r.id}" ${selected}>${r.name}</option>`);
                        });
                    } else {
                        $areaResField.hide();
                    }
                    initialStates.area_responsible_id = '';
                    $responsibleSelect.trigger('change');
                },
                error: function() {
                    console.error('Error fetching responsibles');
                }
            });
        });

        $('#area_responsible_id').on('change', function() {
            const responsibleId = $(this).val();
            const $blockSelect = $('#block_id');
            const $blockField = $('#blockField');

            if (!responsibleId) {
                $blockSelect.empty().append('<option value="">اختر المندوب</option>');
                $blockField.hide();
                return;
            }

            // AJAX Method لجلب المندوبين حسب مسؤول المنطقة
            $.ajax({
                url: "{{ route('dashboard.ajax.getBlocksByResponsible') }}",
                data: { responsible_id: responsibleId },
                success: function(response) {
                    $blockSelect.empty().append('<option value="">اختر المندوب</option>');
                    if (response.blocks && response.blocks.length > 0) {
                        $blockField.show();
                        response.blocks.forEach(b => {
                            const selected = (b.id == initialStates.block_id) ? 'selected' : '';
                            $blockSelect.append(`<option value="${b.id}" ${selected}>${b.name}</option>`);
                        });
                    } else {
                        $blockField.hide();
                    }
                    initialStates.block_id = '';
                },
                error: function() {
                    console.error('Error fetching blocks');
                }
            });
        });

        // Trigger chain on load
        if($('#current_city').val()) {
            $('#current_city').trigger('change');
        }

        // Family Member Modal Logic
        $('#m_relationship').on('change', function() {
           $('#m_phone_group').toggle($(this).val() === 'wife');
        });

        $('#m_hascondition').on('change', function() {
            $('#m_condition_description_group').toggle($(this).val() == '1');
        });

        let memberIndex = {{ isset($person) && $person->familyMembers ? $person->familyMembers->count() : 0 }};

        $('#add-member-confirm-btn').on('click', function() {
            const fname = $('#m_firstname').val();
            const father = $('#m_fathername').val();
            const grand = $('#m_grandfathername').val();
            const family = $('#m_familyname').val();
            const rel = $('#m_relationship').val();
            const idnum = $('#m_idnum').val();
            const dob = $('#m_dob').val();
            const gender = $('#m_gender').val();
            const phone = $('#m_phone').val();
            const hasCond = $('#m_hascondition').val();
            const condDesc = $('#m_conditiondescription').val();

            if(!fname || !father || !grand || !family || !rel || !idnum || !dob || !gender) {
                alert('يرجى ملء جميع الحقول المطلوبة');
                return;
            }

            const tbody = $('#family-table-body');
            const tr = `
                <tr>
                    <td>
                        ${fname} ${father} ${grand} ${family}
                        <input type="hidden" name="family_members[${memberIndex}][first_name]" value="${fname}">
                        <input type="hidden" name="family_members[${memberIndex}][father_name]" value="${father}">
                        <input type="hidden" name="family_members[${memberIndex}][grandfather_name]" value="${grand}">
                        <input type="hidden" name="family_members[${memberIndex}][family_name]" value="${family}">
                        <input type="hidden" name="family_members[${memberIndex}][id_num]" value="${idnum}">
                        <input type="hidden" name="family_members[${memberIndex}][dob]" value="${dob}">
                        <input type="hidden" name="family_members[${memberIndex}][gender]" value="${gender}">
                        <input type="hidden" name="family_members[${memberIndex}][relationship]" value="${rel}">
                        <input type="hidden" name="family_members[${memberIndex}][has_condition]" value="${hasCond}">
                        <input type="hidden" name="family_members[${memberIndex}][condition_description]" value="${condDesc}">
                        <input type="hidden" name="family_members[${memberIndex}][phone]" value="${phone}">
                    </td>
                    <td>${idnum}</td>
                    <td>${rel}</td>
                    <td>${dob}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-member-btn"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            tbody.append(tr);
            memberIndex++;

            $('#familyMemberModal').modal('hide');
            $('#m_firstname, #m_fathername, #m_grandfathername, #m_familyname, #m_relationship, #m_idnum, #m_dob, #m_gender, #m_phone, #m_conditiondescription').val('');
            $('#m_hascondition').val('0');
            $('#m_condition_description_group').hide();
            $('#m_phone_group').hide();
        });

        $(document).on('click', '.remove-member-btn', function() {
            $(this).closest('tr').remove();
        });
    });
</script>
@endpush
