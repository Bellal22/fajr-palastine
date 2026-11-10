@include('dashboard.errors')

{{ BsForm::text('name') }}

@php
    $isCashType = (bool) old('is_cash', isset($coupon_type) ? $coupon_type->is_cash : false);
    $selectedAmountType = old('cash_amount_type', isset($coupon_type) && $coupon_type->cash_amount_type ? $coupon_type->cash_amount_type : 'fixed');
    $selectedCurrency = old('cash_currency', isset($coupon_type) && $coupon_type->cash_currency ? $coupon_type->cash_currency : 'ILS');
    $cashAmountValue = old('cash_amount', isset($coupon_type) ? $coupon_type->cash_amount : '');
@endphp

<div class="card card-outline {{ $isCashType ? 'card-success' : 'card-primary' }} mt-3 mb-3" id="couponTypeNatureCard">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-md-5 mb-2 mb-md-0">
                <label class="font-weight-bold d-block mb-2">
                    <i class="fas fa-hand-holding-usd text-success mr-1"></i>
                    طبيعة الكوبون (عيني / نقدي)
                </label>
                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                    <label class="btn {{ !$isCashType ? 'btn-primary active' : 'btn-outline-secondary' }} w-50 font-weight-bold" id="lblTypeInKind">
                        <input type="radio" name="is_cash" value="0" autocomplete="off" {{ !$isCashType ? 'checked' : '' }} onchange="toggleCouponTypeCashFields()">
                        <i class="fas fa-box-open mr-1"></i> كوبون عيني
                    </label>
                    <label class="btn {{ $isCashType ? 'btn-success active' : 'btn-outline-secondary' }} w-50 font-weight-bold" id="lblTypeCash">
                        <input type="radio" name="is_cash" value="1" autocomplete="off" {{ $isCashType ? 'checked' : '' }} onchange="toggleCouponTypeCashFields()">
                        <i class="fas fa-money-bill-wave mr-1"></i> كوبون نقدي
                    </label>
                </div>
            </div>

            <div class="col-md-7" id="couponTypeCashSummaryNotice" style="{{ $isCashType ? '' : 'display: none;' }}">
                <span class="text-success font-weight-bold">
                    <i class="fas fa-check-circle mr-1"></i> تم تفعيل نمط الكوبون النقدي، حدد خيارات القيمة والعملة بالأسفل.
                </span>
            </div>
        </div>

        {{-- تفاصيل الكوبون النقدي (المبلغ موحد أو منفصل، العملة والرمز) --}}
        <div class="row mt-3" id="couponTypeCashFieldsContainer" style="{{ $isCashType ? '' : 'display: none;' }}">
            <div class="col-12">
                <div class="bg-light p-3 rounded border">
                    <div class="row align-items-center">
                        {{-- آلية تحديد المبلغ: موحد أو منفصل --}}
                        <div class="col-md-5 mb-3 mb-md-0">
                            <label class="font-weight-bold text-success mb-2 d-block">
                                <i class="fas fa-balance-scale mr-1"></i> تحديد المبلغ للمستفيدين <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn {{ $selectedAmountType === 'fixed' ? 'btn-success active' : 'btn-outline-secondary' }} w-50 font-weight-bold" id="lblAmountFixed">
                                    <input type="radio" name="cash_amount_type" value="fixed" autocomplete="off" {{ $selectedAmountType === 'fixed' ? 'checked' : '' }} onchange="toggleCouponAmountType()">
                                    <i class="fas fa-equals mr-1"></i> مبلغ موحد
                                </label>
                                <label class="btn {{ $selectedAmountType === 'custom' ? 'btn-success active' : 'btn-outline-secondary' }} w-50 font-weight-bold" id="lblAmountCustom">
                                    <input type="radio" name="cash_amount_type" value="custom" autocomplete="off" {{ $selectedAmountType === 'custom' ? 'checked' : '' }} onchange="toggleCouponAmountType()">
                                    <i class="fas fa-user-edit mr-1"></i> منفصل عند التسليم
                                </label>
                            </div>
                            <small class="form-text text-muted mt-1">
                                موحد (قيمة واحدة للكل) أو منفصل (يتم تحديده لكل مستفيد عند التسليم).
                            </small>
                        </div>

                        {{-- العملة ورمز العملة --}}
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="cash_currency" class="font-weight-bold text-success mb-2 d-block">
                                <i class="fas fa-money-check-alt mr-1"></i> العملة والرمز <span class="text-danger">*</span>
                            </label>
                            <select name="cash_currency"
                                    id="cash_currency"
                                    class="form-control border-success font-weight-bold"
                                    style="height: 38px;"
                                    {{ $isCashType ? 'required' : '' }}>
                                @foreach(\App\Models\CouponType::CURRENCIES as $code => $label)
                                    <option value="{{ $code }}" {{ $selectedCurrency === $code ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- قيمة المبلغ الموحد --}}
                        <div class="col-md-4" id="fixedAmountContainer" style="{{ $selectedAmountType === 'fixed' ? '' : 'display: none;' }}">
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
                                   placeholder="مثال: 100"
                                   value="{{ $cashAmountValue }}"
                                   {{ ($isCashType && $selectedAmountType === 'fixed') ? 'required' : '' }}>
                        </div>

                        {{-- رسالة توضيحية للمبلغ المنفصل --}}
                        <div class="col-md-4" id="customAmountNote" style="{{ $selectedAmountType === 'custom' ? '' : 'display: none;' }}">
                            <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 13px;">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>مبلغ منفصل:</strong> سيتم إدخال وتحديد المبلغ الخاص بكل مستفيد بشكل منفصل عند تنفيذ التسليم بالعملة المختارة.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleCouponAmountType() {
        const isCustom = document.querySelector('input[name="cash_amount_type"]:checked')?.value === 'custom';
        const fixedContainer = document.getElementById('fixedAmountContainer');
        const customNote = document.getElementById('customAmountNote');
        const amountInput = document.getElementById('cash_amount');
        const lblFixed = document.getElementById('lblAmountFixed');
        const lblCustom = document.getElementById('lblAmountCustom');

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

    function toggleCouponTypeCashFields() {
        const isCash = document.querySelector('input[name="is_cash"]:checked')?.value === '1';
        const container = document.getElementById('couponTypeCashFieldsContainer');
        const summaryNotice = document.getElementById('couponTypeCashSummaryNotice');
        const amountInput = document.getElementById('cash_amount');
        const currencySelect = document.getElementById('cash_currency');
        const card = document.getElementById('couponTypeNatureCard');
        const lblInKind = document.getElementById('lblTypeInKind');
        const lblCash = document.getElementById('lblTypeCash');

        if (isCash) {
            if (container) container.style.display = '';
            if (summaryNotice) summaryNotice.style.display = '';
            currencySelect.setAttribute('required', 'required');
            card.classList.remove('card-primary');
            card.classList.add('card-success');
            lblCash.classList.remove('btn-outline-secondary');
            lblCash.classList.add('btn-success', 'active');
            lblInKind.classList.remove('btn-primary', 'active');
            lblInKind.classList.add('btn-outline-secondary');
            toggleCouponAmountType();
        } else {
            if (container) container.style.display = 'none';
            if (summaryNotice) summaryNotice.style.display = 'none';
            amountInput.removeAttribute('required');
            currencySelect.removeAttribute('required');
            card.classList.remove('card-success');
            card.classList.add('card-primary');
            lblInKind.classList.remove('btn-outline-secondary');
            lblInKind.classList.add('btn-primary', 'active');
            lblCash.classList.remove('btn-success', 'active');
            lblCash.classList.add('btn-outline-secondary');
        }
    }
</script>
@endpush
            lblCash.classList.remove('btn-success', 'active');
            lblCash.classList.add('btn-outline-secondary');
        }
    }
</script>
@endpush
