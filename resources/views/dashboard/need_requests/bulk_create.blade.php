<x-layout :title="'تفعيل أدوات طلب الاحتياج'" :breadcrumbs="['dashboard.need_requests.index']">

    {{-- تنسيقات مخصصة لتقليد ستايل الكبونات والتعارض --}}
    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* تحسين شكل حقل Select2 ليتناسب مع الصورة */
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #d8d6de !important;
            border-radius: 0.357rem !important;
            min-height: 40px !important;
            padding: 2px !important;
        }

        /* ستايل "التاج" أو "الحبة" داخل الحقل */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #f3f2f7 !important; /* لون رمادي فاتح مثل الصورة */
            border: 1px solid #dcdcdc !important;
            color: #5e5873 !important;
            padding: 4px 12px !important;
            margin: 4px !important;
            font-weight: 500;
            border-radius: 4px !important;
            display: flex;
            align-items: center;
        }

        /* تنسيق زر الحذف (x) داخل التاج */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #ea5455 !important; /* لون أحمر */
            margin-left: 8px !important;
            order: 2;
            border: none !important;
            background: transparent !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            background-color: transparent !important;
            color: #bd3939 !important;
        }

        .select2-container--default .select2-search--inline .select2-search__field {
            margin-top: 7px !important;
            margin-right: 8px !important;
        }
    </style>
    @endpush

    @if($activatedProject)
        {{ BsForm::model($activatedProject, route('dashboard.need_requests.bulk_store')) }}
    @else
        {{ BsForm::post(route('dashboard.need_requests.bulk_store')) }}
    @endif
    @component('dashboard::components.box')
        @slot('title', 'تفعيل أدوات طلب الاحتياج للمشرفين')

        @include('dashboard.errors')

        <div class="row">
            <div class="col-md-4">
                {{ BsForm::select('project_id')
                    ->options($projects->pluck('name', 'id'))
                    ->value($selected_project_id ?? null)
                    ->label(trans('need_requests.attributes.project_id'))
                    ->placeholder(trans('need_requests.select'))
                    ->attribute('class', 'form-control select2')
                }}
            </div>
            <div class="col-md-4">
                {{ BsForm::number('allowed_id_count')
                    ->label('الحد الأقصى للهويات المسموح به')
                    ->required()
                    ->min(1)
                }}
            </div>
            <div class="col-md-4">
                {{ BsForm::text('deadline')
                    ->label('موعد انتهاء الترشيح')
                    ->value($activatedProject && $activatedProject->deadline ? $activatedProject->deadline->format('Y-m-d H:i') : null)
                    ->attribute('class', 'form-control datetime-picker')
                    ->attribute('autocomplete', 'off')
                }}
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-12 mb-2">
                <h5 class="text-primary"><i class="fa fa-filter"></i> معايير الترشيح المطلوبة (اختياري)</h5>
                <p class="text-muted small">اترك الحقول فارغة إذا لم يكن هناك شرط محدد. هذه المعايير ستظهر للمشرفين كموجهات للترشيح.</p>
            </div>
            
            {{-- قسم العائلة --}}
            <div class="col-md-6 mb-2">
                <div class="card border border-primary shadow-none" style="background: #f8f9fa;">
                    <div class="card-header p-2 bg-primary text-white">
                        <h6 class="mb-0 small font-weight-bold"><i class="fa fa-users mr-1"></i> معايير العائلة والمنزل</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-md-6">
                                {{ BsForm::number('min_family_members')->label('أدنى أفراد عائلة')->placeholder('مثلاً 5') }}
                            </div>
                            <div class="col-md-6">
                                {{ BsForm::number('max_family_members')->label('أقصى أفراد عائلة') }}
                            </div>
                            <div class="col-md-12 mt-1 py-1">
                                <label class="small font-weight-bold">الأحياء المستهدفة (اترك فارغاً للكل)</label>
                                {{ BsForm::select('target_neighborhoods[]')
                                    ->options($neighborhoods ?? [])
                                    ->multiple()->attribute('class', 'form-control select2-tags')
                                    ->attribute('data-placeholder', 'اكتب اسم الحي واضغط Enter...')
                                }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- قسم رب الأسرة --}}
            <div class="col-md-6 mb-2">
                <div class="card border border-info shadow-none" style="background: #f0f7f9;">
                    <div class="card-header p-2 bg-info text-white">
                        <h6 class="mb-0 small font-weight-bold"><i class="fa fa-user mr-1"></i> معايير رب الأسرة</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-md-6">
                                {{ BsForm::select('has_condition')->label('حالة مرضية')->options(['' => 'لا يشترط', '1' => 'نعم (يوجد)', '0' => 'لا (لا يوجد)']) }}
                            </div>
                            <div class="col-md-6">
                                {{ BsForm::number('min_age')->label('أدنى عمر لرب الأسرة') }}
                            </div>
                            <div class="col-md-6">
                                {{ BsForm::number('max_age')->label('أقصى عمر لرب الأسرة') }}
                            </div>
                            <div class="col-md-12">
                                <label class="small font-weight-bold">الحالة الاجتماعية</label>
                                {{ BsForm::select('social_status[]')
                                    ->options(isset($chooses['social_status']) ? $chooses['social_status']->pluck('name', 'name') : [])
                                    ->multiple()->attribute('class', 'form-control select2-multiple')
                                }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- قسم الأطفال والوضع الصحي --}}
            <div class="col-md-12">
                <div class="card border border-warning shadow-none" style="background: #fffcf5;">
                    <div class="card-header p-2 bg-warning text-dark">
                        <h6 class="mb-0 small font-weight-bold"><i class="fa fa-baby mr-1"></i> معايير الأطفال والوضع الصحي المتخصص</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-md-3">
                                {{ BsForm::number('child_count')->label('أدنى عدد أطفال') }}
                            </div>
                            <div class="col-md-3">
                                {{ BsForm::number('child_min_age')->label('عمر الطفل (من)') }}
                            </div>
                            <div class="col-md-3">
                                {{ BsForm::number('child_max_age')->label('عمر الطفل (إلى)') }}
                            </div>
                            <div class="col-md-3">
                                <!-- empty -->
                            </div>
                            <div class="col-md-3 mt-1">
                                {{ BsForm::select('has_disability')->label('وجود إعاقة')->options([''=>'لا يشترط','1'=>'نعم','0'=>'لا']) }}
                            </div>
                            <div class="col-md-3 mt-1">
                                {{ BsForm::select('has_chronic_disease')->label('مرض مزمن')->options([''=>'لا يشترط','1'=>'نعم','0'=>'لا']) }}
                            </div>
                            <div class="col-md-6 mt-1">
                                {{ BsForm::textarea('criteria_notes')->label('ملاحظات ومعايير إضافية')->rows(1) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-12">
                <label class="form-label">المشرفين (مسؤولي المناطق)</label>
                {{-- استخدام الحقل المتعدد --}}
                {{ BsForm::select('supervisor_ids[]')
                    ->options($supervisors->pluck('name', 'id'))
                    ->value($selected_supervisor_ids ?? [])
                    ->multiple()
                    ->required()
                    ->attribute('class', 'form-control select2-multiple')
                    ->attribute('data-placeholder', 'اختر المشرفين من القائمة...')
                }}
                <small class="text-muted mt-1 d-block">
                    <i class="fa fa-info-circle"></i> ملاحظة: سيتم تفعيل إمكانية رفع طلبات الاحتياج للمشرفين المختارين لهذا المشروع.
                </small>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-12">
                {{ BsForm::textarea('notes')
                    ->label(trans('need_requests.attributes.notes'))
                    ->rows(3)
                }}
            </div>
        </div>

        @slot('footer')
            {{ BsForm::submit()->label(trans('need_requests.actions.save')) }}
        @endslot
    @endcomponent
    {{ BsForm::close() }}

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
    <script>
        $(document).ready(function() {
            // تفعيل Flatpickr للتاريخ والوقت
            flatpickr('.datetime-picker', {
                locale: 'ar',
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                allowInput: true,
            });

            // تفعيل الـ Select2 العادي للمشروع
            $('.select2').select2({
                dir: "rtl",
                width: '100%'
            });

            // تفعيل الـ Select2 المتعدد للمشرفين بستايل الكبونات
            $('.select2-multiple').select2({
                dir: "rtl",
                width: '100%',
                placeholder: $(this).data('placeholder'),
                allowClear: true,
                closeOnSelect: false
            });

            // تفعيل الـ Select2 للتاجات (الأحياء)
            $('.select2-tags').select2({
                dir: "rtl",
                width: '100%',
                tags: true,
                placeholder: $(this).data('placeholder'),
                allowClear: true,
                closeOnSelect: false
            });
        });
    </script>
    @endpush
</x-layout>
