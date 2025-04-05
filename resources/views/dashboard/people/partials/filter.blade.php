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
            <label for="dob" class="form-label">تاريخ الميلاد</label>
            <input type="date" name="dob" id="dob"
                   class="form-control"
                   max="{{ now()->toDateString() }}"
                   placeholder="اختر تاريخ الميلاد"
                   value="{{ request('dob', $person->dob ?? '') }}">
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
            let form = this.closest('form');

            form.querySelectorAll('input, select').forEach(function (element) {
                // نتجاوز عنصر عدد النتائج
                if (element.name === 'perPage') return;

                // تفريغ القيم بناءً على النوع
                if (element.type === 'checkbox' || element.type === 'radio') {
                    element.checked = false;
                } else {
                    element.value = '';
                }
            });

            form.submit(); // إعادة تحميل الصفحة بالقيم الجديدة
        });
    </script>
@endpush
{{ BsForm::close() }}
