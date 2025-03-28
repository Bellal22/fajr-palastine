{{ BsForm::resource('people')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title', trans('people.filter'))

    <div class="row">
        <div class="col-md-6">
            {{ BsForm::text('id_num')->value(request('id_num')) }}
        </div>
        <div class="col-md-6">
            {{ BsForm::select('social_status', [
                'single' => 'أعزب / عزباء',
                'married' => 'متزوج / متزوجة',
                'polygamous'=>'متعدد الزوجات',
                'divorced' => 'مطلق / مطلقة',
                'widowed' => 'أرمل / أرملة',
            ])->value(request('social_status'))->placeholder('اختر الحالة الاجتماعية') }}
        </div>

        <div class="col-md-6">
            {{ BsForm::select('gender', [
                'ذكر' => 'ذكر',
                'أنثى' => 'أنثى',
            ])->value(request('gender'))
            ->placeholder('اختر الجنس') }}
        </div>

        <div class="col-md-6">
            {{ BsForm::number('relatives_count')->value(request('relatives_count')) }}
        </div>

        <div class="col-md-6">
            {{ BsForm::date('dob')
                ->value(request('dob'))
                ->max(now()->toDateString()) // منع اختيار تاريخ مستقبلي
                ->placeholder('اختر تاريخ الميلاد') }}
        </div>

        <div class="col-md-6">
            {{ BsForm::select('current_city', [
                'khanYounis' => 'خانيونس',
                'rafah' => 'رفح',
            ])->value(request('current_city'))
            ->placeholder('اختر المحافظة الحالية') }}
        </div>

        <div class="col-md-6">
            {{ BsForm::number('perPage')
                ->value(request('perPage', 15))
                ->min(1)
                 ->label(trans('people.perPage')) }}
        </div>
    </div>

    @slot('footer')
    <button type="submit" class="btn btn-primary btn-sm">
        <i class="fas fa fa-fw fa-filter"></i>
        @lang('people.actions.filter')
    </button>

    <button type="button" class="btn btn-secondary btn-sm" id="resetFilters">
        <i class="fas fa fa-fw fa-times"></i>
       @lang('people.actions.empty_filters')
    </button>
@endSlot

@endcomponent
@push('scripts')
    <script>
        document.getElementById('resetFilters').addEventListener('click', function () {
            let form = this.closest('form'); // البحث عن الفورم الذي يحتوي على الزر
            form.reset(); // مسح كل المدخلات
        });
    </script>
@endpush
{{ BsForm::close() }}
