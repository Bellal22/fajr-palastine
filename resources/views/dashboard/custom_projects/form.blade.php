<x-layout :title="$isEdit ? ('تعديل وتخصيص: ' . $customProject->name) : 'إضافة مشروع مخصص جديد'" :breadcrumbs="$isEdit ? ['dashboard.custom_projects.edit', $customProject] : ['dashboard.custom_projects.create']">

    @include('dashboard.errors')

    <form action="{{ $isEdit ? route('dashboard.custom_projects.update', $customProject) : route('dashboard.custom_projects.store') }}" method="POST">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="row">
            {{-- القسم الأيمن: البيانات الأساسية للمشروع المخصص --}}
            <div class="col-lg-5">
                <div class="card card-primary card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-project-diagram text-primary ml-1"></i>
                            البيانات الأساسية للمشروع المخصص
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">
                                اسم المشروع المخصص <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name', $customProject->name) }}"
                                   placeholder="مثلاً: مشروع الـ RRM، مشروع استجابة الشتاء، مشروع الإيواء..."
                                   required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        بادئة رمز الكشف (Prefix) <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="code_prefix"
                                           class="form-control font-monospace text-uppercase"
                                           dir="ltr"
                                           maxlength="15"
                                           value="{{ old('code_prefix', $customProject->code_prefix ?: 'RRM') }}"
                                           placeholder="RRM"
                                           required>
                                    <small class="text-muted">يُستخدم لتوليد رمز الكشف التلقائي (مثال: <code>RRM-20261031-1234</code>)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">لون التمييز</label>
                                    @php
                                        $colors = [
                                            'warning' => 'برتقالي / أصفر (Warning)',
                                            'primary' => 'أزرق (Primary)',
                                            'success' => 'أخضر (Success)',
                                            'info' => 'سماوي (Info)',
                                            'danger' => 'أحمر (Danger)',
                                            'secondary' => 'رمادي (Secondary)',
                                        ];
                                        $selectedColor = old('color', $customProject->color ?: 'warning');
                                    @endphp
                                    <select name="color" class="form-control">
                                        @foreach($colors as $colorKey => $colorLabel)
                                            <option value="{{ $colorKey }}" {{ $selectedColor === $colorKey ? 'selected' : '' }}>
                                                {{ $colorLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">أيقونة المشروع في القائمة</label>
                            @php
                                $icons = [
                                    'fas fa-first-aid' => 'حقيبة طوارئ / إسعاف (fa-first-aid)',
                                    'fas fa-boxes' => 'صناديق طرود (fa-boxes)',
                                    'fas fa-hands-helping' => 'مساعدة إنسانية (fa-hands-helping)',
                                    'fas fa-campground' => 'خيمة / إيواء (fa-campground)',
                                    'fas fa-tint' => 'مياه / إغاثة (fa-tint)',
                                    'fas fa-utensils' => 'سلة غذائية / تكية (fa-utensils)',
                                    'fas fa-baby' => 'رعاية أطفال / صحية (fa-baby)',
                                    'fas fa-bolt' => 'استجابة عاجلة (fa-bolt)',
                                ];
                                $selectedIcon = old('icon', $customProject->icon ?: 'fas fa-first-aid');
                            @endphp
                            <select name="icon" class="form-control">
                                @foreach($icons as $iconVal => $iconLabel)
                                    <option value="{{ $iconVal }}" {{ $selectedIcon === $iconVal ? 'selected' : '' }}>
                                        {{ $iconLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">وصف المشروع المخصص (اختياري)</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="3"
                                      placeholder="نبذة مختصرة عن طبيعة المشروع وطريقة توزيعه...">{{ old('description', $customProject->description) }}</textarea>
                        </div>

                        {{-- طبيعة المشروع المخصص (عيني / نقدي) --}}
                        @php
                            $isCashCustom = (bool) old('is_cash', $customProject->is_cash ?? false);
                            $selectedCurrencyCustom = old('cash_currency', $customProject->cash_currency ?: 'ILS');
                            $cashAmountCustom = old('cash_amount', $customProject->cash_amount);
                        @endphp

                        <div class="card card-outline {{ $isCashCustom ? 'card-success' : 'card-secondary' }} mb-3" id="customProjectCashCard">
                            <div class="card-body p-3">
                                <label class="font-weight-bold d-block mb-2">
                                    <i class="fas fa-hand-holding-usd text-success ml-1"></i>
                                    طبيعة المشروع المخصص (عيني / نقدي) <span class="text-danger">*</span>
                                </label>
                                <div class="btn-group btn-group-toggle w-100 mb-3" data-toggle="buttons">
                                    <label class="btn {{ !$isCashCustom ? 'btn-primary active' : 'btn-outline-secondary' }} w-50 font-weight-bold" id="lblCustomInKind">
                                        <input type="radio" name="is_cash" value="0" autocomplete="off" {{ !$isCashCustom ? 'checked' : '' }} onchange="toggleCustomProjectCashFields()">
                                        <i class="fas fa-boxes ml-1"></i> مشروع عيني (طرود)
                                    </label>
                                    <label class="btn {{ $isCashCustom ? 'btn-success active' : 'btn-outline-secondary' }} w-50 font-weight-bold" id="lblCustomCash">
                                        <input type="radio" name="is_cash" value="1" autocomplete="off" {{ $isCashCustom ? 'checked' : '' }} onchange="toggleCustomProjectCashFields()">
                                        <i class="fas fa-money-bill-wave ml-1"></i> مشروع نقدي (مبالغ مالية)
                                    </label>
                                </div>

                                <div id="customProjectCashFields" style="{{ $isCashCustom ? '' : 'display: none;' }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="font-weight-bold text-success small mb-1">
                                                <i class="fas fa-coins ml-1"></i> قيمة المبلغ المالي <span class="text-danger">*</span>
                                            </label>
                                            <input type="number"
                                                   step="0.01"
                                                   min="0.01"
                                                   name="cash_amount"
                                                   id="custom_cash_amount"
                                                   class="form-control border-success font-weight-bold"
                                                   placeholder="مثال: 500"
                                                   value="{{ $cashAmountCustom }}"
                                                   {{ $isCashCustom ? 'required' : '' }}>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="font-weight-bold text-success small mb-1">
                                                <i class="fas fa-money-check-alt ml-1"></i> العملة <span class="text-danger">*</span>
                                            </label>
                                            <select name="cash_currency"
                                                    id="custom_cash_currency"
                                                    class="form-control border-success font-weight-bold"
                                                    {{ $isCashCustom ? 'required' : '' }}>
                                                @foreach(\App\Models\CustomProject::CURRENCIES as $code => $label)
                                                    <option value="{{ $code }}" {{ $selectedCurrencyCustom === $code ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-info-circle text-info ml-1"></i> سيتم احتساب هذا المبلغ النقدي والعملة تلقائياً لكل مستفيد في كشوفات هذا المشروع.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="custom-control custom-switch mt-3">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="isActiveSwitch"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $customProject->is_active ?? true) ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold text-dark" for="isActiveSwitch">
                                تفعيل المشروع وإظهاره في القائمة الجانبية وشاشة التسليم
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- القسم الأيسر: تخصيص طريقة التعامل والتوزيع --}}
            <div class="col-lg-7">
                <div class="card card-info card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-sliders-h text-info ml-1"></i>
                            تخصيص طريقة التعامل والتوزيع لهذا المشروع
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-layer-group text-primary ml-1"></i>
                                        نطاق تسليم الكوبونات / الطرود <span class="text-danger">*</span>
                                    </label>
                                    @php
                                        $scopeVal = old('coupon_scope', $customProject->coupon_scope ?: 'multiple');
                                    @endphp
                                    <select name="coupon_scope" class="form-control">
                                        <option value="multiple" {{ $scopeVal === 'multiple' ? 'selected' : '' }}>
                                            حزمة طرود متعددة (مثل RRM)
                                        </option>
                                        <option value="single" {{ $scopeVal === 'single' ? 'selected' : '' }}>
                                            طرد واحد محدد
                                        </option>
                                        <option value="both" {{ $scopeVal === 'both' ? 'selected' : '' }}>
                                            مرن (يسمح بطرد واحد أو حزمة طرود)
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-warehouse text-secondary ml-1"></i>
                                        المخزن الفرعي الافتراضي (اختياري)
                                    </label>
                                    @php
                                        $defaultWh = old('default_sub_warehouse_id', $customProject->default_sub_warehouse_id);
                                    @endphp
                                    <select name="default_sub_warehouse_id" class="form-control">
                                        <option value="">-- بدون مخزن افتراضي (يُختار عند التسليم) --</option>
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}" {{ (string)$defaultWh === (string)$wh->id ? 'selected' : '' }}>
                                                {{ $wh->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light border rounded p-3 mb-3">
                            <h6 class="font-weight-bold text-dark mb-2">
                                <i class="fas fa-cogs text-info ml-1"></i> سياسة التسليم التلقائية للمشروع:
                            </h6>

                            <div class="custom-control custom-checkbox mb-2">
                                <input type="hidden" name="auto_add_if_missing" value="0">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="chkAutoAdd"
                                       name="auto_add_if_missing"
                                       value="1"
                                       {{ old('auto_add_if_missing', $customProject->auto_add_if_missing ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="chkAutoAdd">
                                    تفعيل "إضافة غير المضافين تلقائياً وتسليمهم" افتراضياً عند تسليم كشوفات هذا المشروع
                                </label>
                            </div>

                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="skip_already_delivered" value="0">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="chkSkipDelivered"
                                       name="skip_already_delivered"
                                       value="1"
                                       {{ old('skip_already_delivered', $customProject->skip_already_delivered ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="chkSkipDelivered">
                                    تفعيل "تجاوز المستلمين مسبقاً (عدم التكرار)" افتراضياً
                                </label>
                            </div>
                        </div>

                        {{-- تحديد الكوبونات / الطرود الافتراضية المرتبطة بالمشروع المخصص --}}
                        <div class="form-group mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="font-weight-bold mb-0">
                                    <i class="fas fa-boxes text-primary ml-1"></i>
                                    الكوبونات / الطرود الافتراضية المرتبطة بهذا المشروع (اختياري):
                                </label>
                                <small class="text-muted">تُحدد تلقائياً عند فتح شاشة تسليم هذا المشروع</small>
                            </div>

                            <input type="text"
                                   id="searchDefaultCoupons"
                                   class="form-control form-control-sm mb-2"
                                   placeholder="ابحث باسم الطرد / الكوبون لتصفيته...">

                            @php
                                $selectedProjectIds = array_map('intval', old('default_project_ids', $customProject->default_project_ids ?? []));
                                $savedQuantities = old('default_project_quantities', $customProject->default_project_quantities ?? []);
                            @endphp

                            <div class="border rounded p-2 bg-light" style="max-height: 310px; overflow-y: auto;">
                                <div class="row" id="defaultCouponsList">
                                    @forelse($projects as $proj)
                                        @php
                                            $isChecked = in_array((int) $proj->id, $selectedProjectIds, true);
                                            $qtyVal = $savedQuantities[$proj->id] ?? 1;
                                        @endphp
                                        <div class="col-md-6 mb-2 default-coupon-item" data-name="{{ mb_strtolower($proj->name) }}">
                                            <div class="border rounded bg-white p-2 d-flex justify-content-between align-items-center shadow-xs">
                                                <div class="custom-control custom-checkbox text-truncate pl-2" style="max-width: 68%;">
                                                    <input type="checkbox"
                                                           class="custom-control-input default-proj-chk"
                                                           id="def_proj_{{ $proj->id }}"
                                                           name="default_project_ids[]"
                                                           value="{{ $proj->id }}"
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold text-dark small cursor-pointer"
                                                           for="def_proj_{{ $proj->id }}"
                                                           title="{{ $proj->name }}">
                                                        {{ $proj->name }}
                                                    </label>
                                                </div>

                                                <div class="input-group input-group-sm" style="width: 92px;">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text px-1 small">الكمية</span>
                                                    </div>
                                                    <input type="number"
                                                           name="default_project_quantities[{{ $proj->id }}]"
                                                           class="form-control text-center font-weight-bold px-1"
                                                           min="1"
                                                           max="100"
                                                           value="{{ $qtyVal }}">
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center text-muted py-3">
                                            لا توجد طرود / كوبونات مضافة حالياً.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <a href="{{ route('dashboard.custom_projects.index') }}" class="btn btn-default font-weight-bold">
                                <i class="fas fa-arrow-right ml-1"></i> رجوع للقائمة
                            </a>
                            <button type="submit" class="btn btn-primary font-weight-bold px-4">
                                <i class="fas fa-save ml-1"></i> {{ $isEdit ? 'حفظ التعديلات والتخصيص' : 'إنشاء المشروع المخصص' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function toggleCustomProjectCashFields() {
            const isCash = $('input[name="is_cash"]:checked').val() === '1';
            const $container = $('#customProjectCashFields');
            const $amountInput = $('#custom_cash_amount');
            const $currencySelect = $('#custom_cash_currency');
            const $card = $('#customProjectCashCard');
            const $lblInKind = $('#lblCustomInKind');
            const $lblCash = $('#lblCustomCash');

            if (isCash) {
                $container.slideDown(200);
                $amountInput.attr('required', 'required');
                $currencySelect.attr('required', 'required');
                $card.removeClass('card-secondary').addClass('card-success');
                $lblCash.removeClass('btn-outline-secondary').addClass('btn-success active');
                $lblInKind.removeClass('btn-primary active').addClass('btn-outline-secondary');
            } else {
                $container.slideUp(200);
                $amountInput.removeAttr('required');
                $currencySelect.removeAttr('required');
                $card.removeClass('card-success').addClass('card-secondary');
                $lblInKind.removeClass('btn-outline-secondary').addClass('btn-primary active');
                $lblCash.removeClass('btn-success active').addClass('btn-outline-secondary');
            }
        }

        $(document).ready(function() {
            $('#searchDefaultCoupons').on('input', function() {
                const term = $(this).val().toLowerCase().trim();
                $('.default-coupon-item').each(function() {
                    const name = ($(this).data('name') || '').toString().toLowerCase();
                    $(this).toggle(!term || name.indexOf(term) !== -1);
                });
            });
        });
    </script>
    @endpush

</x-layout>

