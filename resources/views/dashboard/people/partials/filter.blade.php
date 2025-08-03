{{ BsForm::resource('people')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title', trans('people.filter'))

    <div class="row">

        <div class="col-md-6">
            {{ BsForm::text('id_num')->value(request('id_num'))->label(trans('people.attributes.id_num')) }}
        </div>

        @php
            $currentRouteName = Route::currentRouteName();
        @endphp

        @if ($currentRouteName == 'dashboard.people.index')
            <div class="col-md-6 form-group">
                <label for="area_responsible_id">مسؤول المنطقة</label>
                <?php
                    $areaResponsiblesQuery = \App\Models\AreaResponsible::query();

                    if (auth()->user()?->isSupervisor()) {
                        $areaResponsiblesQuery->where('id', auth()->user()->id);
                    }

                    $areaResponsiblesOptions = $areaResponsiblesQuery
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray();
                    $areaResponsiblesOptions = [null => 'اختر مسؤول المنطقة (لا يوجد)'] + $areaResponsiblesOptions;
                ?>
                {{ BsForm::select(
                    'area_responsible_id',
                    $areaResponsiblesOptions,
                    request('area_responsible_id')
                ) }}
            </div>
        @endif

        @php
            $currentRouteName = Route::currentRouteName();
        @endphp

        @if ($currentRouteName == 'dashboard.people.view')
            <div class="col-md-6 form-group">
                <label for="block_id">المندوب</label>
                <?php
                    $blocksQuery = \App\Models\Block::query();
                    if (auth()->user()?->isSupervisor()) {
                        $blocksQuery->where('area_responsible_id', auth()->user()->id);
                    }
                    $blocksOptions = $blocksQuery
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray();
                    $blocksOptions = [null => 'اختر المندوب (لا يوجد)'] + $blocksOptions;
                ?>
                {{ BsForm::select(
                    'block_id',
                    $blocksOptions,
                    request('block_id')
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

            <div class="col-md-6">
                {{ BsForm::number('perPage')
                    ->value(request('perPage', 15))
                    ->min(1)
                    ->label(trans('people.perPage')) }}
            </div>
        @endif
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

        document.getElementById('resetFilters').addEventListener('click', function() {
            // إعادة تعيين حقول النموذج
            const form = document.querySelector('form');
            if(form) {
                form.reset();
            }

            // الحصول على الرابط الحالي بدون كويري باراميترز
            const cleanUrl = window.location.origin + window.location.pathname;

            // تحديث الرابط في شريط العناوين
            history.pushState({}, document.title, cleanUrl);

            // إعادة تحميل الصفحة
            window.location.reload();
        });
        // document.getElementById('resetFilters').addEventListener('click', function () {
        //     let form = this.closest('form');

        //     form.querySelectorAll('input, select').forEach(function (element) {
        //         // نتجاوز عنصر عدد النتائج
        //         if (element.name === 'perPage') return;

        //         // تفريغ القيم بناءً على النوع
        //         if (element.type === 'checkbox' || element.type === 'radio') {
        //             element.checked = false;
        //         } else {
        //             element.value = '';
        //         }
        //     });

        //     form.submit(); // إعادة تحميل الصفحة بالقيم الجديدة
        // });
    </script>
@endpush
{{ BsForm::close() }}
