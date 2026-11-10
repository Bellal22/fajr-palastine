<x-layout :title="trans('settings.tabs.main')" :breadcrumbs="['dashboard.settings.index']">
    {{ BsForm::resource('settings')->patch(route('dashboard.settings.update')) }}
    @component('dashboard::components.box')

        @bsMultilangualFormTabs

        {{ BsForm::text('name')->value(Settings::locale($locale->code)->get('name')) }}

        {{ BsForm::text('copyright')->value(Settings::locale($locale->code)->get('copyright')) }}

        @endBsMultilangualFormTabs

        @if(is_array(trans('settings.dashboard_templates')) && ! empty(trans('settings.dashboard_templates')))
            {{ BsForm::select('dashboard_template')
                    ->options(trans('settings.dashboard_templates'))
                    ->value(Settings::get('dashboard_template', config('layouts.dashboard'))) }}
        @endif

        @if(is_array(trans('settings.frontend_templates')) && ! empty(trans('settings.frontend_templates')))
            {{ BsForm::select('frontend_template')
                    ->options(trans('settings.frontend_templates'))
                    ->value(Settings::get('frontend_template', config('layouts.frontend'))) }}
        @endif

        {{ BsForm::checkbox('delete_forever')->value(1)->checked(Settings::get('delete_forever'))->withDefault() }}

        <div class="row">
            <div class="col-md-6">
                {{ BsForm::image('logo')->collection('logo')->files(
         optional(Settings::instance('logo'))->getMediaResource('logo')
 ) }}
            </div>
            <div class="col-md-6">
                {{ BsForm::image('favicon')->collection('favicon')->files(
           optional(Settings::instance('favicon'))->getMediaResource('favicon')
   ) }}
            </div>
        </div>

        @slot('footer')
            <div class="d-flex justify-content-between align-items-center">
                {{ BsForm::submit()->label(trans('settings.actions.save')) }}
                
                <button type="button" id="test-connection-btn" class="btn btn-outline-info">
                    <i class="fas fa-network-wired"></i> اختبار الربط
                </button>
                
                <button type="button" id="bulk-sync-btn" class="btn btn-outline-success">
                    <i class="fas fa-sync-alt"></i> مزامنة كافة البيانات الآن
                </button>
            </div>

            @push('scripts')
                <script>
                    $(document).ready(function() {
                        // اختبار الربط
                        $('#test-connection-btn').on('click', function() {
                            var btn = $(this);
                            var originalHtml = btn.html();
                            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الاختبار...');
                            $.ajax({
                                url: "{{ route('dashboard.settings.test_connection') }}",
                                method: "POST",
                                data: { _token: "{{ csrf_token() }}" },
                                success: function(response) {
                                    if (response.status === 'success') { toastr.success(response.message); }
                                    else { toastr.error(response.message); }
                                },
                                error: function() { toastr.error('حدث خطأ أثناء محاولة الاتصال'); },
                                complete: function() { btn.prop('disabled', false).html(originalHtml); }
                            });
                        });

                        // المزامنة الشاملة
                        $('#bulk-sync-btn').on('click', function() {
                            if (!confirm('هل أنت متأكد من رغبتك في مزامنة جميع المشاريع والموردين الآن؟ قد تستغرق العملية بعض الوقت.')) return;
                            
                            var btn = $(this);
                            var originalHtml = btn.html();
                            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري المزامنة...');
                            
                            $.ajax({
                                url: "{{ route('dashboard.settings.bulk_sync') }}",
                                method: "POST",
                                data: { _token: "{{ csrf_token() }}" },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        toastr.success(response.message);
                                    } else {
                                        toastr.error(response.message);
                                    }
                                },
                                error: function() { toastr.error('حدث خطأ أثناء المزامنة الشاملة'); },
                                complete: function() { btn.prop('disabled', false).html(originalHtml); }
                            });
                        });
                    });
                </script>
            @endpush
        @endslot
    @endcomponent
    {{ BsForm::close() }}
</x-layout>