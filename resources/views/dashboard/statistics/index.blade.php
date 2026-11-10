<x-layout title="لوحة إحصائيات الأسر والأفراد" :breadcrumbs="['dashboard.home']">

    {{-- 1. إحصائيات الأسر --}}
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                <i class="fas fa-home text-info mr-2"></i> 1. إحصائيات الأسر (أرباب الأسر)
            </h5>
        </div>
        <div class="col-md-6 col-12">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($comprehensiveStats->registered_families) }}</h3>
                    <p>إجمالي عدد الأسر المسجل</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer open-breakdown-btn" data-type="families" data-status="all" data-title="إحصائيات إجمالي الأسر المسجلة (أرباب الأسر)">
                    تفصيل داخلي حسب العائلة <i class="fas fa-arrow-circle-left ml-1"></i>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($comprehensiveStats->approved_families) }}</h3>
                    <p>إجمالي عدد الأسر المعتمد</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="#" class="small-box-footer open-breakdown-btn" data-type="families" data-status="approved" data-title="إحصائيات الأسر المعتمدة (أرباب الأسر)">
                    تفصيل داخلي حسب العائلة <i class="fas fa-arrow-circle-left ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- 2. إحصائيات الأفراد --}}
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                <i class="fas fa-user-friends text-info mr-2"></i> 2. إحصائيات الأفراد
            </h5>
        </div>
        <div class="col-md-6 col-12">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($comprehensiveStats->registered_individuals) }}</h3>
                    <p>إجمالي عدد الأفراد للأسر المسجلة</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <a href="#" class="small-box-footer open-breakdown-btn" data-type="individuals" data-status="all" data-title="إحصائيات إجمالي الأفراد للأسر المسجلة">
                    تفصيل داخلي حسب العائلة <i class="fas fa-arrow-circle-left ml-1"></i>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($comprehensiveStats->approved_individuals) }}</h3>
                    <p>إجمالي عدد الأفراد للأسر المعتمدة</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <a href="#" class="small-box-footer open-breakdown-btn" data-type="individuals" data-status="approved" data-title="إحصائيات الأفراد للأسر المعتمدة">
                    تفصيل داخلي حسب العائلة <i class="fas fa-arrow-circle-left ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- 3. إحصائيات الذكور --}}
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                <i class="fas fa-male text-primary mr-2"></i> 3. إحصائيات الذكور
            </h5>
        </div>
        <div class="col-md-6 col-12">
            <div class="small-box bg-primary shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($comprehensiveStats->registered_males) }}</h3>
                    <p>إجمالي عدد الذكور المسجلين</p>
                </div>
                <div class="icon">
                    <i class="fas fa-male"></i>
                </div>
                <a href="#" class="small-box-footer open-breakdown-btn" data-type="males" data-status="all" data-title="إحصائيات الذكور المسجلين">
                    تفصيل داخلي حسب العائلة <i class="fas fa-arrow-circle-left ml-1"></i>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($comprehensiveStats->approved_males) }}</h3>
                    <p>إجمالي عدد الذكور المعتمدين</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <a href="#" class="small-box-footer open-breakdown-btn" data-type="males" data-status="approved" data-title="إحصائيات الذكور المعتمدين">
                    تفصيل داخلي حسب العائلة <i class="fas fa-arrow-circle-left ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- 4. إحصائيات الإناث --}}
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                <i class="fas fa-female text-danger mr-2"></i> 4. إحصائيات الإناث
            </h5>
        </div>
        <div class="col-md-6 col-12">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($comprehensiveStats->registered_females) }}</h3>
                    <p>إجمالي عدد الإناث المسجلين</p>
                </div>
                <div class="icon">
                    <i class="fas fa-female"></i>
                </div>
                <a href="#" class="small-box-footer open-breakdown-btn" data-type="females" data-status="all" data-title="إحصائيات الإناث المسجلات">
                    تفصيل داخلي حسب العائلة <i class="fas fa-arrow-circle-left ml-1"></i>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($comprehensiveStats->approved_females) }}</h3>
                    <p>إجمالي عدد الإناث المعتمدين</p>
                </div>
                <div class="icon">
                    <i class="fas fa-female"></i>
                </div>
                <a href="#" class="small-box-footer open-breakdown-btn" data-type="females" data-status="approved" data-title="إحصائيات الإناث المعتمدات">
                    تفصيل داخلي حسب العائلة <i class="fas fa-arrow-circle-left ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- 5. إحصائيات الأطفال --}}
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                <i class="fas fa-child text-warning mr-2"></i> 5. إحصائيات الأطفال (دون 18 عاماً)
            </h5>
        </div>
        <div class="col-md-6 col-12">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($comprehensiveStats->registered_children) }}</h3>
                    <p>إجمالي عدد الأطفال المسجلين</p>
                </div>
                <div class="icon">
                    <i class="fas fa-child"></i>
                </div>
                <a href="#" class="small-box-footer open-breakdown-btn" data-type="children" data-status="all" data-title="إحصائيات الأطفال المسجلين (دون 18 عاماً)">
                    تفصيل داخلي حسب العائلة <i class="fas fa-arrow-circle-left ml-1"></i>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($comprehensiveStats->approved_children) }}</h3>
                    <p>إجمالي عدد الأطفال المعتمدين</p>
                </div>
                <div class="icon">
                    <i class="fas fa-baby"></i>
                </div>
                <a href="#" class="small-box-footer open-breakdown-btn" data-type="children" data-status="approved" data-title="إحصائيات الأطفال المعتمدين (دون 18 عاماً)">
                    تفصيل داخلي حسب العائلة <i class="fas fa-arrow-circle-left ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Modal التفصيل الداخلي حسب العائلة --}}
    <div class="modal fade" id="familyBreakdownModal" tabindex="-1" role="dialog" aria-labelledby="familyBreakdownModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-dark text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="familyBreakdownModalLabel">
                        <i class="fas fa-sitemap text-warning mr-2"></i> <span id="modalCategoryTitle">تفصيل العائلات</span>
                    </h5>
                    <button type="button" class="close text-white opacity-8" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light p-4">
                    {{-- أدوات التصفية والبحث وتصدير الإكسل --}}
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-5 col-12 mb-2 mb-md-0">
                            <div class="input-group shadow-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" id="familySearchInput" class="form-control border-left-0" placeholder="ابحث باسم العائلة (مثال: الأسطل، أبو نمر، العجلة...)" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3 col-12 mb-2 mb-md-0">
                            <select id="familyStatusFilter" class="form-control shadow-sm">
                                <option value="all">جميع الحالات (معتمد وغير معتمد)</option>
                                <option value="approved">المعتمدين فقط</option>
                                <option value="unapproved">غير المعتمدين فقط</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-12 mb-2 mb-md-0 text-md-left">
                            <span class="badge badge-info p-2 font-weight-normal w-100 shadow-sm" style="font-size: 14px;" id="familiesCountBadge">العائلات: 0</span>
                        </div>
                        <div class="col-md-2 col-12 text-md-left">
                            <a id="exportExcelBtn" href="#" target="_blank" class="btn btn-success font-weight-bold shadow-sm w-100">
                                <i class="fas fa-file-excel mr-1"></i> تصدير Excel
                            </a>
                        </div>
                    </div>

                    {{-- شريط التحميل --}}
                    <div id="modalLoadingSpinner" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3.5rem; height: 3.5rem;">
                            <span class="sr-only">جاري التجميع الموحد...</span>
                        </div>
                        <p class="mt-3 text-dark font-weight-bold">جاري تجميع ومعالجة أسماء العائلات بالتوحيد العربي...</p>
                    </div>

                    {{-- جدول البيانات --}}
                    <div id="modalTableWrapper" class="table-responsive bg-white rounded shadow-sm d-none">
                        <table class="table table-hover table-striped mb-0 text-center align-middle">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th class="text-right">اسم العائلة (الموحد)</th>
                                    <th>إجمالي المسجلين</th>
                                    <th>المعتمدين</th>
                                    <th>غير المعتمدين</th>
                                    <th style="width: 180px;">نسبة الاعتماد</th>
                                </tr>
                            </thead>
                            <tbody id="familyBreakdownTbody">
                                {{-- البيانات يتم توليدها بواسطة JS --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- الترقيم والملخص --}}
                    <div class="row mt-3 align-items-center" id="modalPaginationWrapper">
                        <div class="col-md-6 col-12 text-center text-md-left mb-2 mb-md-0">
                            <span id="modalPaginationInfo" class="text-muted font-weight-bold"></span>
                        </div>
                        <div class="col-md-6 col-12">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm justify-content-center justify-content-md-end mb-0" id="modalPaginationUl">
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 py-2">
                    <button type="button" class="btn btn-secondary px-4 font-weight-bold" data-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function () {
            let currentType = 'families';
            let currentStatus = 'all';
            let currentSearch = '';
            let currentPage = 1;
            let searchTimeout = null;

            function updateExportUrl() {
                let exportUrl = "{{ route('dashboard.statistics.family_breakdown.export') }}"
                    + "?type=" + encodeURIComponent(currentType)
                    + "&status=" + encodeURIComponent(currentStatus)
                    + "&search=" + encodeURIComponent(currentSearch);
                $('#exportExcelBtn').attr('href', exportUrl);
            }

            // فتح النافذة المنبثقة عند الضغط على الكرت
            $('.open-breakdown-btn').on('click', function (e) {
                e.preventDefault();
                currentType = $(this).data('type');
                currentStatus = $(this).data('status') || 'all';
                let title = $(this).data('title') || 'تفصيل حسب العائلة';

                $('#modalCategoryTitle').text(title);
                $('#familyStatusFilter').val(currentStatus);
                $('#familySearchInput').val('');
                currentSearch = '';
                currentPage = 1;

                updateExportUrl();
                $('#familyBreakdownModal').modal('show');
                fetchFamilyBreakdown();
            });

            // تغيير التصفية حسب الحالة
            $('#familyStatusFilter').on('change', function () {
                currentStatus = $(this).val();
                currentPage = 1;
                updateExportUrl();
                fetchFamilyBreakdown();
            });

            // البحث التفاعلي مع تأخير بسيط (Debounce)
            $('#familySearchInput').on('input', function () {
                clearTimeout(searchTimeout);
                currentSearch = $(this).val();
                currentPage = 1;
                updateExportUrl();
                searchTimeout = setTimeout(function () {
                    fetchFamilyBreakdown();
                }, 300);
            });

            // التنقل بين الصفحات
            $(document).on('click', '#modalPaginationUl .page-link', function (e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage) {
                    currentPage = page;
                    fetchFamilyBreakdown();
                }
            });

            // جلب البيانات من الخادم
            function fetchFamilyBreakdown() {
                $('#modalLoadingSpinner').removeClass('d-none');
                $('#modalTableWrapper').addClass('d-none');

                $.ajax({
                    url: "{{ route('dashboard.statistics.family_breakdown') }}",
                    type: 'GET',
                    data: {
                        type: currentType,
                        status: currentStatus,
                        search: currentSearch,
                        page: currentPage,
                        per_page: 20
                    },
                    success: function (res) {
                        $('#modalLoadingSpinner').addClass('d-none');
                        $('#modalTableWrapper').removeClass('d-none');

                        if (res.success) {
                            renderTable(res.data, res.pagination);
                            renderPagination(res.pagination);
                            $('#familiesCountBadge').text('العائلات: ' + res.pagination.total.toLocaleString('en-US'));
                        }
                    },
                    error: function () {
                        $('#modalLoadingSpinner').addClass('d-none');
                        alert('حدث خطأ أثناء تحميل إحصائيات العائلات، يرجى المحاولة مرة أخرى.');
                    }
                });
            }

            // بناء أسطر الجدول
            function renderTable(data, pagination) {
                let tbody = $('#familyBreakdownTbody');
                tbody.empty();

                if (data.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="6" class="py-4 text-muted font-weight-bold">
                                <i class="fas fa-search-minus fa-2x d-block mb-2 text-warning"></i>
                                لم يتم العثور على عائلات تطابق البحث المحدد
                            </td>
                        </tr>
                    `);
                    return;
                }

                let startIndex = (pagination.current_page - 1) * pagination.per_page + 1;

                data.forEach(function (row, idx) {
                    let total = row.total_count || 0;
                    let approved = row.approved_count || 0;
                    let unapproved = row.unapproved_count || 0;
                    let percent = total > 0 ? Math.round((approved / total) * 100) : 0;

                    let badgeClass = percent >= 70 ? 'bg-success' : (percent >= 40 ? 'bg-warning text-dark' : 'bg-danger');

                    tbody.append(`
                        <tr>
                            <td class="font-weight-bold text-muted">${(startIndex + idx).toLocaleString('en-US')}</td>
                            <td class="text-right font-weight-bold text-dark" style="font-size: 15px;">
                                <i class="fas fa-folder text-info mr-1"></i> ${escapeHtml(row.display_name)}
                            </td>
                            <td class="font-weight-bold text-primary">${total.toLocaleString('en-US')}</td>
                            <td class="font-weight-bold text-success">${approved.toLocaleString('en-US')}</td>
                            <td class="font-weight-bold text-secondary">${unapproved.toLocaleString('en-US')}</td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="badge ${badgeClass} mr-2 font-weight-bold" style="min-width: 45px;">${percent}%</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar ${badgeClass}" role="progressbar" style="width: ${percent}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `);
                });
            }

            // بناء أزرار الترقيم
            function renderPagination(pagination) {
                let ul = $('#modalPaginationUl');
                ul.empty();

                if (pagination.last_page <= 1) {
                    $('#modalPaginationInfo').text(`عرض ${pagination.total.toLocaleString('en-US')} عائلة`);
                    return;
                }

                let from = (pagination.current_page - 1) * pagination.per_page + 1;
                let to = Math.min(pagination.total, pagination.current_page * pagination.per_page);
                $('#modalPaginationInfo').text(`عرض من ${from.toLocaleString('en-US')} إلى ${to.toLocaleString('en-US')} من إجمالي ${pagination.total.toLocaleString('en-US')} عائلة`);

                // الزر السابق
                ul.append(`
                    <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${pagination.current_page - 1}">السابق</a>
                    </li>
                `);

                let startPage = Math.max(1, pagination.current_page - 2);
                let endPage = Math.min(pagination.last_page, pagination.current_page + 2);

                if (startPage > 1) {
                    ul.append(`<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`);
                    if (startPage > 2) {
                        ul.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                    }
                }

                for (let p = startPage; p <= endPage; p++) {
                    ul.append(`
                        <li class="page-item ${p === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${p}">${p.toLocaleString('en-US')}</a>
                        </li>
                    `);
                }

                if (endPage < pagination.last_page) {
                    if (endPage < pagination.last_page - 1) {
                        ul.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                    }
                    ul.append(`<li class="page-item"><a class="page-link" href="#" data-page="${pagination.last_page}">${pagination.last_page.toLocaleString('en-US')}</a></li>`);
                }

                // الزر التالي
                ul.append(`
                    <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${pagination.current_page + 1}">التالي</a>
                    </li>
                `);
            }

            function escapeHtml(text) {
                if (!text) return '';
                return text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        });
    </script>
    @endpush

</x-layout>
