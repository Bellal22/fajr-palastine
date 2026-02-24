<x-layout :title="'تفعيل أدوات طلب الاحتياج'" :breadcrumbs="['dashboard.need_requests.index']">

    {{-- تنسيقات مخصصة لتقليد ستايل الكبونات والتعارض --}}
    @push('styles')
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

    {{ BsForm::post(route('dashboard.need_requests.bulk_store')) }}
    @component('dashboard::components.box')
        @slot('title', 'تفعيل أدوات طلب الاحتياج للمشرفين')

        @include('dashboard.errors')

        <div class="row">
            <div class="col-md-6">
                {{ BsForm::select('project_id')
                    ->options($projects->pluck('name', 'id'))
                    ->label(trans('need_requests.attributes.project_id'))
                    ->placeholder(trans('need_requests.select'))
                    ->attribute('class', 'form-control select2')
                }}
            </div>
            <div class="col-md-6">
                {{ BsForm::number('allowed_id_count')
                    ->label('الحد الأقصى للهويات المسموح به')
                    ->required()
                    ->min(1)
                }}
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-12">
                <label class="form-label">المشرفين (مسؤولي المناطق)</label>
                {{-- استخدام الحقل المتعدد --}}
                {{ BsForm::select('supervisor_ids[]')
                    ->options($supervisors->pluck('name', 'id'))
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
    <script>
        $(document).ready(function() {
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
                closeOnSelect: false {{-- يترك القائمة مفتوحة لاختيار أكثر من شخص بسهولة --}}
            });
        });
    </script>
    @endpush
</x-layout>
