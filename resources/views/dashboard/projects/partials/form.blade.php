@include('dashboard.errors')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* تحسين شكل حقل Select2 ليتناسب مع التصميم العصري */
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #d8d6de !important;
            border-radius: 0.357rem !important;
            min-height: 40px !important;
            padding: 2px !important;
        }

        /* ستايل "التاج" أو "الحبة" داخل الحقل */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #f3f2f7 !important;
            border: 1px solid #dcdcdc !important;
            color: #5e5873 !important;
            padding: 4px 12px !important;
            margin: 4px !important;
            font-weight: 500;
            border-radius: 4px !important;
            display: flex;
            align-items: center;
            float: right; /* لضمان الترتيب الصحيح في RTL */
        }

        /* تنسيق زر الحذف (x) */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #ea5455 !important;
            margin-left: 8px !important;
            margin-right: 0px !important;
            order: 2;
            border: none !important;
            background: transparent !important;
        }

        .select2-container--default .select2-search--inline .select2-search__field {
            margin-top: 7px !important;
            margin-right: 8px !important;
        }

        /* تنسيق زر مسح الكل (allowClear) في الحقول المتعددة */
        .select2-container--default .select2-selection--multiple .select2-selection__clear {
            position: absolute !important;
            left: 10px !important;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            cursor: pointer;
            color: #ea5455;
            font-size: 1.2rem;
            margin-top: 0 !important;
        }

        .select2-container--default .select2-selection--multiple {
            padding-left: 30px !important; /* مساحة لزر مسح الكل */
            position: relative;
        }
    </style>
@endpush

<div class="row">
    <div class="col-md-12">
        {{ BsForm::text('name')
            ->label(trans('projects.attributes.name'))
            ->required()
        }}
    </div>
    <div class="col-md-12">
        {{ BsForm::textarea('description')
            ->rows(3)
        }}
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="start_date">{{ trans('projects.attributes.start_date') }}</label>
            <input type="text" name="start_date" id="start_date" class="form-control flatpickr-datepicker" value="{{ old('start_date', (isset($project) && $project->start_date) ? (is_string($project->start_date) ? date('Y-m-d', strtotime($project->start_date)) : $project->start_date->format('Y-m-d')) : '') }}" autocomplete="off">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="end_date">{{ trans('projects.attributes.end_date') }}</label>
            <input type="text" name="end_date" id="end_date" class="form-control flatpickr-datepicker" value="{{ old('end_date', (isset($project) && $project->end_date) ? (is_string($project->end_date) ? date('Y-m-d', strtotime($project->end_date)) : $project->end_date->format('Y-m-d')) : '') }}" autocomplete="off">
        </div>
    </div>
    <div class="col-md-4">
        {{ BsForm::select('status')
            ->options([
                'active' => 'نشط',
                'completed' => 'مكتمل',
                'suspended' => 'معلق'
            ])
            ->label(trans('projects.attributes.status'))
            ->value(old('status', $project->status ?? 'active'))
        }}
    </div>
</div>

@php
    $isCashCoupon = (bool) old('is_cash', isset($project) ? $project->is_cash : false);
    $selectedAmountType = old('cash_amount_type', isset($project) && $project->cash_amount_type ? $project->cash_amount_type : 'fixed');
    $selectedCurrency = old('cash_currency', isset($project) && $project->cash_currency ? $project->cash_currency : 'ILS');
    $cashAmountValue = old('cash_amount', isset($project) ? $project->cash_amount : '');
    $selectedDefaultDeliveryMethod = old('default_delivery_method', isset($project) && $project->default_delivery_method ? $project->default_delivery_method : 'كاش');
@endphp

<div class="card card-outline {{ $isCashCoupon ? 'card-success' : 'card-primary' }} mt-2 mb-3" id="couponNatureCard">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-md-5 mb-2 mb-md-0">
                <label class="font-weight-bold d-block mb-2">
                    <i class="fas fa-hand-holding-usd text-success mr-1"></i>
                    طبيعة الكوبون (عيني / نقدي) <span class="text-danger">*</span>
                </label>
                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                    <label class="btn {{ !$isCashCoupon ? 'btn-primary active' : 'btn-outline-secondary' }} w-50 font-weight-bold" id="lblCouponInKind">
                        <input type="radio" name="is_cash" value="0" autocomplete="off" {{ !$isCashCoupon ? 'checked' : '' }} onchange="toggleProjectCashFields()">
                        <i class="fas fa-box-open mr-1"></i> كوبون عيني (طرود)
                    </label>
                    <label class="btn {{ $isCashCoupon ? 'btn-success active' : 'btn-outline-secondary' }} w-50 font-weight-bold" id="lblCouponCash">
                        <input type="radio" name="is_cash" value="1" autocomplete="off" {{ $isCashCoupon ? 'checked' : '' }} onchange="toggleProjectCashFields()">
                        <i class="fas fa-money-bill-wave mr-1"></i> كوبون نقدي (مالي)
                    </label>
                </div>
            </div>

            <div class="col-md-7" id="projectCashSummaryNotice" style="{{ $isCashCoupon ? '' : 'display: none;' }}">
                <span class="text-success font-weight-bold">
                    <i class="fas fa-check-circle mr-1"></i> تم تفعيل الكوبون النقدي (المخزن ليس شرطاً للتسليم، والتسليم يتم عبر كاش/محفظة/بنك).
                </span>
            </div>
        </div>

        {{-- تفاصيل الكوبون النقدي --}}
        <div class="row mt-3" id="projectCashFieldsContainer" style="{{ $isCashCoupon ? '' : 'display: none;' }}">
            <div class="col-12">
                <div class="bg-light p-3 rounded border">
                    <div class="row align-items-center">
                        {{-- تحديد آلية المبلغ: موحد أو منفصل --}}
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="font-weight-bold text-success mb-2 d-block">
                                <i class="fas fa-balance-scale mr-1"></i> تحديد المبلغ للمستفيدين <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn {{ $selectedAmountType === 'fixed' ? 'btn-success active' : 'btn-outline-secondary' }} w-50 font-weight-bold" id="lblProjectAmountFixed">
                                    <input type="radio" name="cash_amount_type" value="fixed" autocomplete="off" {{ $selectedAmountType === 'fixed' ? 'checked' : '' }} onchange="toggleProjectAmountType()">
                                    <i class="fas fa-equals mr-1"></i> مبلغ موحد
                                </label>
                                <label class="btn {{ $selectedAmountType === 'custom' ? 'btn-success active' : 'btn-outline-secondary' }} w-50 font-weight-bold" id="lblProjectAmountCustom">
                                    <input type="radio" name="cash_amount_type" value="custom" autocomplete="off" {{ $selectedAmountType === 'custom' ? 'checked' : '' }} onchange="toggleProjectAmountType()">
                                    <i class="fas fa-user-edit mr-1"></i> منفصل عند التسليم
                                </label>
                            </div>
                        </div>

                        {{-- العملة ورمز العملة --}}
                        <div class="col-md-2 mb-3 mb-md-0">
                            <label for="cash_currency" class="font-weight-bold text-success mb-2 d-block">
                                <i class="fas fa-money-check-alt mr-1"></i> العملة <span class="text-danger">*</span>
                            </label>
                            <select name="cash_currency"
                                    id="cash_currency"
                                    class="form-control border-success font-weight-bold"
                                    style="height: 38px;"
                                    {{ $isCashCoupon ? 'required' : '' }}>
                                @foreach(\App\Models\Project::CURRENCIES as $code => $label)
                                    <option value="{{ $code }}" {{ $selectedCurrency === $code ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- طريقة التسليم الافتراضية --}}
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="default_delivery_method" class="font-weight-bold text-success mb-2 d-block">
                                <i class="fas fa-truck-loading mr-1"></i> طريقة التسليم
                            </label>
                            <select name="default_delivery_method" id="default_delivery_method" class="form-control border-success font-weight-bold" style="height: 38px;">
                                @foreach(\App\Models\Project::DELIVERY_METHODS as $mKey => $mLabel)
                                    <option value="{{ $mKey }}" {{ $selectedDefaultDeliveryMethod === $mKey ? 'selected' : '' }}>
                                        {{ $mLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- قيمة المبلغ الموحد --}}
                        <div class="col-md-3" id="projectFixedAmountContainer" style="{{ $selectedAmountType === 'fixed' ? '' : 'display: none;' }}">
                            <label for="cash_amount" class="font-weight-bold text-success mb-2 d-block">
                                <i class="fas fa-coins mr-1"></i> القيمة الموحدة <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   step="0.01"
                                   min="0.01"
                                   name="cash_amount"
                                   id="cash_amount"
                                   class="form-control border-success font-weight-bold"
                                   style="height: 38px;"
                                   placeholder="أدخل القيمة (مثال: 100)"
                                   value="{{ $cashAmountValue }}"
                                   {{ ($isCashCoupon && $selectedAmountType === 'fixed') ? 'required' : '' }}>
                        </div>

                        {{-- توضيح المبلغ المنفصل --}}
                        <div class="col-md-3" id="projectCustomAmountNote" style="{{ $selectedAmountType === 'custom' ? '' : 'display: none;' }}">
                            <div class="alert alert-info py-2 px-2 mb-0" style="font-size: 12px;">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>مبلغ منفصل:</strong> يُحدد المبلغ لكل مستفيد عند التسليم.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<h5 class="mb-3">الشركاء</h5>
<div class="row">
    <div class="col-md-6">
        {{ BsForm::select('granting_entities[]')
            ->label('الجهات المانحة')
            ->options(App\Models\Supplier::whereIn('type', ['donor', 'operator'])->pluck('name', 'id')->toArray())
            ->multiple()
            ->attribute('class', 'form-control select2')
            ->value(isset($project) ? $project->grantingEntities->pluck('id')->toArray() : [])
        }}
    </div>

    <div class="col-md-6">
        {{ BsForm::select('executing_entities[]')
            ->label('الجهات المنفذة (الشركاء)')
            ->options(App\Models\Supplier::whereIn('type', ['donor', 'operator'])->pluck('name', 'id')->toArray())
            ->multiple()
            ->attribute('class', 'form-control select2')
            ->value(isset($project) ? $project->executingEntities->pluck('id')->toArray() : [])
        }}
    </div>
</div>

<hr>

<h5 class="mb-3">تعارض المشاريع</h5>
<div class="row">
    <div class="col-md-12">
        {{ BsForm::select('conflicts[]')
            ->label('هذا المشروع يتضارب مع')
            ->options(App\Models\Project::where('id', '!=', $project->id ?? 0)->pluck('name', 'id'))
            ->multiple()
            ->attribute('class', 'form-control select2')
            ->value(isset($project) ? $project->conflicts->pluck('id')->toArray() : [])
        }}
        <small class="text-muted">ملاحظة: إذا اخترت مشروعاً هنا، سيتم تحذيرك (أو منعك) عند إضافة نفس المستفيدين لكلا المشروعين.</small>
    </div>
</div>

<div id="inKindOnlySections" style="{{ $isCashCoupon ? 'display: none;' : '' }}">
    <hr>

    <h5 class="mb-3">الطرود</h5>
    <div class="row">
        <div class="col-md-6">
            {{ BsForm::select('ready_packages[]')
                ->label('الطرود الجاهزة')
                ->options(App\Models\ReadyPackage::pluck('name', 'id'))
                ->multiple()
                ->attribute('class', 'form-control select2')
                ->value(isset($project) ? $project->readyPackages->pluck('id')->toArray() : [])
            }}
        </div>

        <div class="col-md-6">
            {{ BsForm::select('internal_packages[]')
                ->label('الطرود الداخلية')
                ->options(App\Models\InternalPackage::pluck('name', 'id'))
                ->multiple()
                ->attribute('class', 'form-control select2')
                ->value(isset($project) ? $project->internalPackages->pluck('id')->toArray() : [])
            }}
        </div>
    </div>

    <hr>

    <h5 class="mt-3 mb-3">أنواع الكوبونات والكميات</h5>
    <div id="coupon_types_container">
        @php
            App\Models\CouponType::ensureCashColumnsExist();
            $couponTypes = App\Models\CouponType::all();
            $storedCouponTypes = isset($project) ? $project->couponTypes : collect();
        @endphp

        @if($storedCouponTypes->count() > 0)
            @foreach($storedCouponTypes as $index => $type)
                <div class="row mb-2 type-row">
                    <div class="col-md-6">
                        <select name="coupon_types[{{ $index }}][coupon_type_id]" class="form-control coupon-type-field" {{ !$isCashCoupon ? 'required' : '' }}>
                            <option value="">-- اختر النوع --</option>
                            @foreach($couponTypes as $ctype)
                                <option value="{{ $ctype->id }}" {{ $type->id == $ctype->id ? 'selected' : '' }}>{{ $ctype->name }}{{ $ctype->is_cash ? ' (نقدي: ' . $ctype->formatted_cash_value . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="coupon_types[{{ $index }}][quantity]" class="form-control coupon-type-field" placeholder="الكمية" value="{{ $type->pivot->quantity }}" {{ !$isCashCoupon ? 'required' : '' }}>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-block" onclick="removeTypeRow(this)"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            @endforeach
        @else
            <div class="row mb-2 type-row">
                <div class="col-md-6">
                    <select name="coupon_types[0][coupon_type_id]" class="form-control coupon-type-field" {{ !$isCashCoupon ? 'required' : '' }}>
                        <option value="">-- اختر النوع --</option>
                        @foreach($couponTypes as $ctype)
                            <option value="{{ $ctype->id }}">{{ $ctype->name }}{{ $ctype->is_cash ? ' (نقدي: ' . $ctype->formatted_cash_value . ')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="coupon_types[0][quantity]" class="form-control coupon-type-field" placeholder="الكمية" {{ !$isCashCoupon ? 'required' : '' }}>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-block" onclick="removeTypeRow(this)"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        @endif
    </div>
    <button type="button" class="btn btn-success btn-sm mt-2" onclick="addTypeRow()"><i class="fas fa-plus"></i> إضافة نوع</button>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

    <script>
        // تخزين خيارات أنواع الكوبونات في متغير عالمي لاستخدامه عند إضافة صفوف جديدة
        const couponOptions = `
            <option value="">-- اختر النوع --</option>
            @foreach(App\Models\CouponType::all() as $ctype)
                <option value="{{ $ctype->id }}">{{ $ctype->name }}</option>
            @endforeach
        `;

        let typeIndex = {{ isset($project) ? $project->couponTypes->count() : 1 }};

        // دالة تهيئة Select2 الموحدة
        function initSelect2(selector) {
            $(selector).each(function() {
                var $el = $(this);
                $el.select2({
                    dir: "rtl",
                    width: '100%',
                    placeholder: $el.data('placeholder') || $el.attr('placeholder') || "-- اختر --",
                    allowClear: true,
                    closeOnSelect: $el.attr('multiple') ? false : true
                });
            });
        }

        function toggleProjectAmountType() {
            const isCustom = document.querySelector('input[name="cash_amount_type"]:checked')?.value === 'custom';
            const fixedContainer = document.getElementById('projectFixedAmountContainer');
            const customNote = document.getElementById('projectCustomAmountNote');
            const amountInput = document.getElementById('cash_amount');
            const lblFixed = document.getElementById('lblProjectAmountFixed');
            const lblCustom = document.getElementById('lblProjectAmountCustom');

            if (isCustom) {
                if (fixedContainer) fixedContainer.style.display = 'none';
                if (customNote) customNote.style.display = '';
                if (amountInput) amountInput.removeAttribute('required');
                if (lblCustom) {
                    lblCustom.classList.remove('btn-outline-secondary');
                    lblCustom.classList.add('btn-success', 'active');
                }
                if (lblFixed) {
                    lblFixed.classList.remove('btn-success', 'active');
                    lblFixed.classList.add('btn-outline-secondary');
                }
            } else {
                if (fixedContainer) fixedContainer.style.display = '';
                if (customNote) customNote.style.display = 'none';
                const isCash = document.querySelector('input[name="is_cash"]:checked')?.value === '1';
                if (amountInput && isCash) amountInput.setAttribute('required', 'required');
                if (lblFixed) {
                    lblFixed.classList.remove('btn-outline-secondary');
                    lblFixed.classList.add('btn-success', 'active');
                }
                if (lblCustom) {
                    lblCustom.classList.remove('btn-success', 'active');
                    lblCustom.classList.add('btn-outline-secondary');
                }
            }
        }

        function toggleProjectCashFields() {
            const isCash = document.querySelector('input[name="is_cash"]:checked')?.value === '1';
            const cashContainer = document.getElementById('projectCashFieldsContainer');
            const summaryNotice = document.getElementById('projectCashSummaryNotice');
            const cashAmountInput = document.getElementById('cash_amount');
            const cashCurrencySelect = document.getElementById('cash_currency');
            const card = document.getElementById('couponNatureCard');
            const lblInKind = document.getElementById('lblCouponInKind');
            const lblCash = document.getElementById('lblCouponCash');
            const inKindSections = document.getElementById('inKindOnlySections');

            if (isCash) {
                if (cashContainer) cashContainer.style.display = '';
                if (summaryNotice) summaryNotice.style.display = '';
                if (inKindSections) {
                    inKindSections.style.display = 'none';
                    $(inKindSections).find('input, select').prop('disabled', true);
                }
                cashAmountInput.removeAttribute('disabled');
                cashCurrencySelect.removeAttribute('disabled');
                cashCurrencySelect.setAttribute('required', 'required');
                card.classList.remove('card-primary');
                card.classList.add('card-success');
                lblCash.classList.remove('btn-outline-secondary');
                lblCash.classList.add('btn-success', 'active');
                lblInKind.classList.remove('btn-primary', 'active');
                lblInKind.classList.add('btn-outline-secondary');
                document.querySelectorAll('.coupon-type-field').forEach(el => el.removeAttribute('required'));
                toggleProjectAmountType();
            } else {
                if (cashContainer) cashContainer.style.display = 'none';
                if (summaryNotice) summaryNotice.style.display = 'none';
                if (inKindSections) {
                    inKindSections.style.display = '';
                    $(inKindSections).find('input, select').prop('disabled', false);
                }
                cashAmountInput.setAttribute('disabled', 'disabled');
                cashCurrencySelect.setAttribute('disabled', 'disabled');
                cashAmountInput.removeAttribute('required');
                cashCurrencySelect.removeAttribute('required');
                card.classList.remove('card-success');
                card.classList.add('card-primary');
                lblInKind.classList.remove('btn-outline-secondary');
                lblInKind.classList.add('btn-primary', 'active');
                lblCash.classList.remove('btn-success', 'active');
                lblCash.classList.add('btn-outline-secondary');
                document.querySelectorAll('.coupon-type-field').forEach(el => el.setAttribute('required', 'required'));
            }
        }

        function addTypeRow() {
            const container = document.getElementById('coupon_types_container');
            const isCash = document.querySelector('input[name="is_cash"]:checked')?.value === '1';
            const reqAttr = isCash ? '' : 'required';
            
            const newRow = `
                <div class="row mb-2 type-row">
                    <div class="col-md-6">
                        <select name="coupon_types[${typeIndex}][coupon_type_id]" class="form-control select2-dynamic coupon-type-field" ${reqAttr}>
                            ${couponOptions}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="coupon_types[${typeIndex}][quantity]" class="form-control coupon-type-field" placeholder="الكمية" ${reqAttr}>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-block" onclick="removeTypeRow(this)"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newRow);

            // تفعيل Select2 للحقل الجديد فقط باستخدام الـ class الديناميكي
            initSelect2(`.select2-dynamic[name="coupon_types[${typeIndex}][coupon_type_id]"]`);

            typeIndex++;
        }

        function removeTypeRow(button) {
            if(document.querySelectorAll('.type-row').length > 1) {
                button.closest('.type-row').remove();
            } else {
                alert('يجب إضافة نوع واحد على الأقل');
            }
        }

        $(document).ready(function() {
            // التحقق المبدئي لحالة الكوبون النقدي
            toggleProjectCashFields();

            // تفعيل Flatpickr
            flatpickr('.flatpickr-datepicker', {
                locale: 'ar',
                dateFormat: 'Y-m-d',
                allowInput: true
            });

            // تفعيل Select2 لجميع الحقول عند تحميل الصفحة
            initSelect2('.select2');
            
            // تفعيل Select2 لحقول أنواع الكوبونات الموجودة مسبقاً (في حالة التعديل مثلاً)
            $('select[name^="coupon_types"]').each(function() {
                initSelect2(this);
            });
        });
    </script>
@endpush
