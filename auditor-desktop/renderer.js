const { ipcRenderer } = require('electron');

// Global state variables
let dbConfig = null;
let computedFamilies = [];
let areasList = [];
let charts = {};

let currentPage = 1;
const ITEMS_PER_PAGE = 50;
let currentFiltered = [];

// Selectors
const elements = {
    // Navigation
    navItems: document.querySelectorAll('.nav-item'),
    tabContents: document.querySelectorAll('.tab-content'),

    // Connection Tab
    connectionForm: document.getElementById('connection-form'),
    btnTestConnection: document.getElementById('btn-test-connection'),
    dbHost: document.getElementById('db-host'),
    dbPort: document.getElementById('db-port'),
    dbDatabase: document.getElementById('db-database'),
    dbUsername: document.getElementById('db-username'),
    dbPassword: document.getElementById('db-password'),
    connectionAlert: document.getElementById('connection-status-alert'),

    // Loading overlay
    loadingOverlay: document.getElementById('loading-overlay'),
    loadingText: document.getElementById('loading-text'),

    // Scoring & Audit Tab
    btnRunAudit: document.getElementById('btn-run-audit'),
    filterAreas: document.getElementById('filter-areas'),
    filterCategory: document.getElementById('filter-category'),
    filterUnserved: document.getElementById('filter-unserved'),
    auditTableBody: document.getElementById('audit-table-body'),
    tableSearch: document.getElementById('table-search'),
    resultsCount: document.getElementById('results-count'),
    btnExportAll: document.getElementById('btn-export-all'),

    // Batch Lookup Modal
    btnOpenBatch: document.getElementById('btn-open-batch'),
    btnCloseBatch: document.getElementById('btn-close-batch'),
    batchModal: document.getElementById('batch-modal'),
    batchIdNums: document.getElementById('batch-id-nums'),
    btnRunBatch: document.getElementById('btn-run-batch'),
    batchResultsContainer: document.getElementById('batch-results-container'),
    batchResultsBody: document.getElementById('batch-results-body'),
    btnExportBatch: document.getElementById('btn-export-batch'),

    // Audit Results Overlay
    auditResultsOverlay: document.getElementById('audit-results-overlay'),
    btnBackToAudit: document.getElementById('btn-back-to-audit'),
    btnExportAllResults: document.getElementById('btn-export-all-results'),

    // Not used anymore but kept as null to prevent errors if referenced
    sidebarFilterBox: document.querySelector('.sidebar-filter-box'),

    // Sliders & Rules
    ruleAgeThreshold: document.getElementById('rule-age-threshold'),
    ruleInfantScore: document.getElementById('rule-infant-score'),
    ruleChildScore: document.getElementById('rule-child-score'),
    ruleTentScore: document.getElementById('rule-tent-score'),
    ruleDestroyedScore: document.getElementById('rule-destroyed-score'),
    ruleForceIncapacitated: document.getElementById('rule-force-incapacitated'),
    ruleCheckAdultSon: document.getElementById('rule-check-adult-son'),
    ruleCustomKeywords: document.getElementById('rule-custom-keywords'),

    valAgeThreshold: document.getElementById('val-age-threshold'),
    valInfantScore: document.getElementById('val-infant-score'),
    valChildScore: document.getElementById('val-child-score'),
    valTentScore: document.getElementById('val-tent-score'),
    valDestroyedScore: document.getElementById('val-destroyed-score'),

    // Stats
    statCatA: document.getElementById('stat-cat-a'),
    statCatB: document.getElementById('stat-cat-b'),
    statCatC: document.getElementById('stat-cat-c'),
    statCatD: document.getElementById('stat-cat-d'),

    // Outliers Tab
    btnExportOutliers: document.getElementById('btn-export-outliers'),
    overServedBody: document.getElementById('over-served-body'),
    neglectedBody: document.getElementById('neglected-body'),

    // Analytics
    valGini: document.getElementById('val-gini'),
    valAvgQty: document.getElementById('val-avg-qty'),
    valCriticalCoverage: document.getElementById('val-critical-coverage')
};

// Initial setup
document.addEventListener('DOMContentLoaded', async () => {
    setupNavigation();
    setupSliders();

    // Toggle SSH fields view dynamically
    const useSshCheckbox = document.getElementById('use-ssh-tunnel');
    const sshFieldsContainer = document.getElementById('ssh-fields');
    useSshCheckbox.addEventListener('change', () => {
        if (useSshCheckbox.checked) {
            sshFieldsContainer.classList.remove('hidden');
        } else {
            sshFieldsContainer.classList.add('hidden');
        }
    });

    await loadSavedConfig();

    // Wire up audit and export listeners
    elements.btnRunAudit.addEventListener('click', runAuditAnalysis);
    elements.tableSearch.addEventListener('input', filterAndRenderAuditTable);
    elements.filterCategory.addEventListener('change', filterAndRenderAuditTable);
    elements.btnExportAll.addEventListener('click', exportAllToExcel);
    if (elements.btnExportAllResults) {
        elements.btnExportAllResults.addEventListener('click', exportAllToExcel);
    }
    if (elements.btnBackToAudit) {
        elements.btnBackToAudit.addEventListener('click', () => {
            elements.auditResultsOverlay.classList.add('hidden');
        });
    }
    elements.btnExportOutliers.addEventListener('click', exportOutliersToExcel);

    const btnPrev = document.getElementById('btn-prev-page');
    const btnNext = document.getElementById('btn-next-page');
    if (btnPrev) {
        btnPrev.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderCurrentPage();
            }
        });
    }
    if (btnNext) {
        btnNext.addEventListener('click', () => {
            const totalPages = Math.ceil(currentFiltered.length / ITEMS_PER_PAGE) || 1;
            if (currentPage < totalPages) {
                currentPage++;
                renderCurrentPage();
            }
        });
    }

    // Batch Lookup Listeners
    if (elements.btnOpenBatch) {
        elements.btnOpenBatch.addEventListener('click', () => {
            elements.batchModal.classList.remove('hidden');
        });
    }
    if (elements.btnCloseBatch) {
        elements.btnCloseBatch.addEventListener('click', () => {
            elements.batchModal.classList.add('hidden');
        });
    }
    if (elements.btnRunBatch) {
        elements.btnRunBatch.addEventListener('click', runBatchLookup);
    }
    if (elements.btnExportBatch) {
        elements.btnExportBatch.addEventListener('click', exportBatchToExcel);
    }

    // Hide preloader after 2 seconds
    setTimeout(() => {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.style.opacity = '0';
            preloader.style.visibility = 'hidden';
            preloader.style.pointerEvents = 'none';
        }
        document.body.classList.add('loaded');
    }, 2000);
});

// Loading Overlay Helpers
function showLoading(text = 'جاري المعالجة...') {
    elements.loadingText.textContent = text;
    elements.loadingOverlay.classList.remove('hidden');
}
function hideLoading() {
    elements.loadingOverlay.classList.add('hidden');
}

// 1. Navigation setup
function setupNavigation() {
    elements.navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const target = item.getAttribute('data-target');

            // Check if DB configuration is saved before accessing other tabs
            if (target !== 'connection-tab' && !dbConfig) {
                showAlert('danger', 'الرجاء حفظ إعدادات الاتصال بقاعدة البيانات أولاً!');
                return;
            }

            elements.navItems.forEach(i => i.classList.remove('active'));
            elements.tabContents.forEach(c => c.classList.remove('active'));

            item.classList.add('active');
            document.getElementById(target).classList.add('active');

            // Show or hide sidebar filter box dynamically
            if (target === 'connection-tab' || !dbConfig) {
                if (elements.sidebarFilterBox) elements.sidebarFilterBox.style.display = 'none';
            } else {
                if (elements.sidebarFilterBox) elements.sidebarFilterBox.style.display = 'block';
            }

            // Fix Chart.js rendering when tab is activated
            if (target === 'analytics-tab') {
                setTimeout(() => {
                    if (charts.categories) charts.categories.resize();
                    if (charts.scoringVsAid) charts.scoringVsAid.resize();
                }, 100);
            }
        });
    });
}

// 2. Rules Sliders setup
function setupSliders() {
    const sliders = [
        { el: elements.ruleAgeThreshold, val: elements.valAgeThreshold },
        { el: elements.ruleInfantScore, val: elements.valInfantScore },
        { el: elements.ruleChildScore, val: elements.valChildScore },
        { el: elements.ruleTentScore, val: elements.valTentScore },
        { el: elements.ruleDestroyedScore, val: elements.valDestroyedScore }
    ];

    sliders.forEach(s => {
        s.el.addEventListener('input', () => {
            s.val.textContent = s.el.value;
        });
    });
}

// 3. Database Connection logic
async function loadSavedConfig() {
    const saved = await ipcRenderer.invoke('load-connection-config');
    if (saved) {
        dbConfig = saved;
        elements.dbHost.value = saved.host || '';
        elements.dbPort.value = saved.port || '3306';
        elements.dbDatabase.value = saved.database || '';
        elements.dbUsername.value = saved.username || '';
        elements.dbPassword.value = saved.password || '';

        // SSH loading
        const useSshCheckbox = document.getElementById('use-ssh-tunnel');
        const sshFieldsContainer = document.getElementById('ssh-fields');
        if (saved.useSshTunnel) {
            useSshCheckbox.checked = true;
            sshFieldsContainer.classList.remove('hidden');
            if (saved.ssh) {
                document.getElementById('ssh-host').value = saved.ssh.host || '46.202.158.78';
                document.getElementById('ssh-port').value = saved.ssh.port || '22';
                document.getElementById('ssh-username').value = saved.ssh.username || 'root';
                document.getElementById('ssh-password').value = saved.ssh.password || '';
            }
        } else {
            useSshCheckbox.checked = false;
            sshFieldsContainer.classList.add('hidden');
        }

        // Auto connect & load metadata
        await connectAndLoadMetadata();

        // Show sidebar filter box if config is present and tab is active
        const activeTab = document.querySelector('.tab-content.active');
        if (activeTab && activeTab.id !== 'connection-tab') {
            if (elements.sidebarFilterBox) elements.sidebarFilterBox.style.display = 'block';
        } else {
            if (elements.sidebarFilterBox) elements.sidebarFilterBox.style.display = 'none';
        }
    } else {
        if (elements.sidebarFilterBox) elements.sidebarFilterBox.style.display = 'none';
    }
}

elements.btnTestConnection.addEventListener('click', async () => {
    const tempConfig = getConnectionConfigFromForm();
    if (!tempConfig.host || !tempConfig.database || !tempConfig.username) {
        showAlert('danger', 'الرجاء ملء حقول الاتصال المطلوبة أولاً');
        return;
    }

    showLoading('جاري فحص الاتصال بسيرفر MySQL...');
    const result = await ipcRenderer.invoke('test-connection', tempConfig);
    hideLoading();

    if (result.success) {
        showAlert('success', 'تم الاتصال بالسيرفر بنجاح!');
    } else {
        showAlert('danger', `فشل الاتصال: ${result.error}`);
    }
});

elements.connectionForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const config = getConnectionConfigFromForm();

    showLoading('جاري حفظ الإعدادات وفحص الاتصال...');
    const testResult = await ipcRenderer.invoke('test-connection', config);

    if (testResult.success) {
        dbConfig = config;
        await ipcRenderer.invoke('save-connection-config', config);
        showAlert('success', 'تم حفظ الإعدادات والاتصال بنجاح');
        await connectAndLoadMetadata();

        // Auto switch to scoring tab
        document.getElementById('nav-scoring').click();
    } else {
        showAlert('danger', `فشل حفظ الإعدادات. خطأ بالاتصال: ${testResult.error}`);
    }
    hideLoading();
});

function getConnectionConfigFromForm() {
    const useSsh = document.getElementById('use-ssh-tunnel').checked;
    return {
        host: elements.dbHost.value.trim(),
        port: elements.dbPort.value.trim(),
        database: elements.dbDatabase.value.trim(),
        username: elements.dbUsername.value.trim(),
        password: elements.dbPassword.value,
        useSshTunnel: useSsh,
        ssh: useSsh ? {
            host: document.getElementById('ssh-host').value.trim(),
            port: document.getElementById('ssh-port').value.trim(),
            username: document.getElementById('ssh-username').value.trim(),
            password: document.getElementById('ssh-password').value
        } : null
    };
}

function showAlert(type, message) {
    elements.connectionAlert.className = `alert alert-${type}`;
    elements.connectionAlert.textContent = message;
    elements.connectionAlert.classList.remove('hidden');
    setTimeout(() => {
        elements.connectionAlert.classList.add('hidden');
    }, 6000);
}

// 4. Load metadata (Areas)
async function connectAndLoadMetadata() {
    if (!dbConfig) return;

    const result = await ipcRenderer.invoke('fetch-initial-data', dbConfig);
    if (result.success) {
        areasList = result.areas;

        // Populate areas select
        elements.filterAreas.innerHTML = areasList.map(a =>
            `<option value="${a.id}">${a.name}</option>`
        ).join('');
    } else {
        showAlert('danger', `فشل تحميل المناطق: ${result.error}`);
    }
}

// 5. Audit Processing
async function runAuditAnalysis() {
    const selectedAreas = Array.from(elements.filterAreas.selectedOptions).map(opt => Number(opt.value));
    if (selectedAreas.length === 0) {
        alert('الرجاء اختيار منطقة واحدة على الأقل');
        return;
    }

    showLoading('جاري سحب بيانات المستفيدين وإجراء التحليل الذكي...');

    const settings = {
        age_threshold: elements.ruleAgeThreshold.value,
        infant_score: elements.ruleInfantScore.value,
        child_score: elements.ruleChildScore.value,
        tent_score: elements.ruleTentScore.value,
        destroyed_score: elements.ruleDestroyedScore.value,
        force_incapacitated: elements.ruleForceIncapacitated.checked,
        check_adult_son: elements.ruleCheckAdultSon.checked,
        custom_keywords: elements.ruleCustomKeywords.value,
        cat_a_min: 85,
        cat_b_min: 60,
        cat_c_min: 35
    };

    const filters = {
        areaIds: selectedAreas,
        blockId: null
    };

    const response = await ipcRenderer.invoke('run-audit', { dbConfig, auditSettings: settings, filters });
    hideLoading();

    if (response.success) {
        computedFamilies = response.families;

        // Update stats
        elements.statCatA.textContent = response.stats.A;
        elements.statCatB.textContent = response.stats.B;
        elements.statCatC.textContent = response.stats.C;
        elements.statCatD.textContent = response.stats.D;

        filterAndRenderAuditTable();
        calculateOutliersAndRisks();
        renderAnalyticsCharts();
        elements.auditResultsOverlay.classList.remove('hidden');
    } else {
        alert(`فشل التحليل: ${response.error}`);
    }
}

// Batch Lookup function
let lastBatchFamilies = [];

async function runBatchLookup() {
    const idsText = elements.batchIdNums.value.trim();
    if (!idsText) {
        alert('الرجاء إدخال أرقام هويات للبحث عنها');
        return;
    }

    // Parse IDs (split by newline, comma, or space)
    const idNums = idsText.split(/[\n, ]+/).map(s => s.trim()).filter(s => s.length >= 8);

    if (idNums.length === 0) {
        alert('الرجاء إدخال أرقام هويات صحيحة');
        return;
    }

    elements.batchResultsContainer.classList.remove('hidden');
    elements.batchResultsBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> جاري فحص وتحليل الهويات...</td></tr>';

    const settings = {
        age_threshold: elements.ruleAgeThreshold.value,
        infant_score: elements.ruleInfantScore.value,
        child_score: elements.ruleChildScore.value,
        tent_score: elements.ruleTentScore.value,
        destroyed_score: elements.ruleDestroyedScore.value,
        force_incapacitated: elements.ruleForceIncapacitated.checked,
        check_adult_son: elements.ruleCheckAdultSon.checked,
        custom_keywords: elements.ruleCustomKeywords.value,
        cat_a_min: 85,
        cat_b_min: 60,
        cat_c_min: 35
    };

    const response = await ipcRenderer.invoke('run-batch-audit', { dbConfig, idNums, auditSettings: settings });

    if (response.success) {
        lastBatchFamilies = response.families;
        if (lastBatchFamilies.length === 0) {
            elements.batchResultsBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger"><i class="fas fa-exclamation-circle"></i> لم يتم العثور على أي من الهويات المدخلة في قاعدة البيانات</td></tr>';
            return;
        }

        elements.batchResultsBody.innerHTML = lastBatchFamilies.map(f => `
            <tr>
                <td style="white-space: nowrap;">${f.id_num}</td>
                <td style="min-width: 150px;"><strong>${f.full_name}</strong></td>
                <td>${f.address}</td>
                <td style="font-family: 'Outfit'; font-weight:700;">${f.score}</td>
                <td style="white-space: nowrap;"><span class="badge badge-${f.badge}">${f.category}</span></td>
                <td class="text-muted" style="font-size:0.8rem; line-height:1.6; min-width: 200px;">${f.reasons}</td>
            </tr>
        `).join('');
    } else {
        elements.batchResultsBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">حدث خطأ أثناء الفحص: ${response.error}</td></tr>`;
    }
}

async function exportBatchToExcel() {
    if (lastBatchFamilies.length === 0) {
        alert('لا توجد نتائج لتصديرها.');
        return;
    }

    const dataToExport = lastBatchFamilies.map(f => ({
        'رقم الهوية': f.id_num,
        'الاسم الكامل': f.full_name,
        'العنوان': f.address,
        'أفراد العائلة': f.members_count,
        'النقاط': f.score,
        'التصنيف': f.category,
        'مرات الاستلام': f.times_served,
        'الكمية المستلمة': f.total_qty,
        'آخر استلام': f.last_delivery,
        'المسوغات والتحليل الطبي': f.reasons
    }));

    showLoading('جاري تصدير نتائج الدفعة...');
    const res = await ipcRenderer.invoke('export-excel', {
        data: dataToExport,
        fileName: 'تقرير_الاستعلام_المتعدد.xlsx'
    });
    hideLoading();

    if (res.success) {
        alert(`تم تصدير الملف بنجاح إلى: ${res.filePath}`);
    } else if (res.error) {
        alert(`فشل تصدير الملف: ${res.error}`);
    }
}

// 6. Filter & Render Tabular List
function filterAndRenderAuditTable() {
    const searchVal = elements.tableSearch.value.trim().toLowerCase();
    const selectedCategory = elements.filterCategory.value;
    const filterUnserved = elements.filterUnserved.checked;

    let filtered = computedFamilies;

    // Search
    if (searchVal) {
        filtered = filtered.filter(f =>
            f.id_num.toString().includes(searchVal) ||
            f.full_name.toLowerCase().includes(searchVal) ||
            (f.phone || '').includes(searchVal)
        );
    }

    // Category filter
    if (selectedCategory) {
        filtered = filtered.filter(f => f.category === selectedCategory);
    }

    // Unserved filter
    if (filterUnserved) {
        filtered = filtered.filter(f => f.times_served === 0);
    }

    elements.resultsCount.textContent = `العائلات المطابقة: ${filtered.length}`;
    currentFiltered = filtered;
    currentPage = 1;
    renderCurrentPage();
}

function renderCurrentPage() {
    const totalPages = Math.ceil(currentFiltered.length / ITEMS_PER_PAGE) || 1;
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const btnPrev = document.getElementById('btn-prev-page');
    const btnNext = document.getElementById('btn-next-page');
    const pageInfo = document.getElementById('page-info');

    if (btnPrev) btnPrev.disabled = currentPage === 1;
    if (btnNext) btnNext.disabled = currentPage === totalPages;
    if (pageInfo) pageInfo.textContent = `صفحة ${currentPage} من ${totalPages}`;

    if (currentFiltered.length === 0) {
        elements.auditTableBody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center text-muted">لا يوجد نتائج تطابق معايير البحث والفلترة.</td>
        </tr>
        `;
        return;
    }

    const start = (currentPage - 1) * ITEMS_PER_PAGE;
    const end = start + ITEMS_PER_PAGE;
    const pageItems = currentFiltered.slice(start, end);

    elements.auditTableBody.innerHTML = pageItems.map((f, index) => `
        <tr>
            <td style="white-space: nowrap; text-align: center;">${start + index + 1}</td>
            <td style="white-space: nowrap;">${f.id_num}</td>
            <td style="min-width: 150px; white-space: nowrap;"><strong>${f.full_name}</strong></td>
            <td style="white-space: nowrap; text-align: center;">${f.members_count}</td>
            <td style="font-family: 'Outfit'; font-weight:700; white-space: nowrap; text-align: center;">${f.score}</td>
            <td style="white-space: nowrap;"><span class="badge badge-${f.badge}">${f.category}</span></td>
            <td class="text-muted" style="font-size:0.8rem; min-width: 250px; line-height: 1.6;">${f.reasons}</td>
            <td style="white-space: nowrap;">
                <span class="${f.times_served > 0 ? 'text-primary' : 'text-danger'}">
                    ${f.last_delivery} <br> <span style="font-size: 0.75rem;">(${f.times_served} مرات)</span>
                </span>
            </td>
        </tr>
    `).join('');
}

// 7. Outliers and Neglected Analytics
function calculateOutliersAndRisks() {
    // 1. Over-Served Outliers (Z-Score > 2.5 of total aid received among those served)
    const servedFamilies = computedFamilies.filter(f => f.times_served > 0);
    const qtyArray = servedFamilies.map(f => f.total_qty);

    let overServed = [];
    if (qtyArray.length > 0) {
        const mean = qtyArray.reduce((a, b) => a + b, 0) / qtyArray.length;
        const variance = qtyArray.reduce((a, b) => a + Math.pow(b - mean, 2), 0) / qtyArray.length;
        const stdDev = Math.sqrt(variance);

        overServed = servedFamilies.filter(f => {
            const z = stdDev > 0 ? (f.total_qty - mean) / stdDev : 0;
            return z > 2.5;
        });

        elements.valAvgQty.textContent = `${Math.round(mean * 10) / 10}`;
    } else {
        elements.valAvgQty.textContent = '0.0';
    }

    // Sort over-served by times served descending
    overServed.sort((a, b) => b.total_qty - a.total_qty);

    if (overServed.length === 0) {
        elements.overServedBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted">لا توجد شبهات تكرار أو مستفيدين بشكل شاذ في هذه المنطقة.</td>
            </tr>
        `;
    } else {
        elements.overServedBody.innerHTML = overServed.map(f => `
            <tr>
                <td>${f.id_num}</td>
                <td><strong>${f.name}</strong></td>
                <td><span class="badge badge-${f.badge}">${f.category} (${f.score})</span></td>
                <td>${f.times_served}</td>
                <td style="font-weight:700; color:var(--color-danger);">${f.total_qty}</td>
                <td>${f.last_delivery}</td>
            </tr>
        `).join('');
    }

    // 2. Neglected/Under-served (A or B category and last delivery is older than 5 months (150 days) or never)
    const fiveMonthsAgo = new Date();
    fiveMonthsAgo.setMonth(fiveMonthsAgo.getMonth() - 5);

    const neglected = computedFamilies.filter(f => {
        if (!['A', 'B'].includes(f.category)) return false;

        if (f.last_delivery === 'لم يستلم أبداً' || !f.last_delivery) {
            return true;
        }

        try {
            const lastDate = new Date(f.last_delivery);
            return !isNaN(lastDate.getTime()) && lastDate < fiveMonthsAgo;
        } catch (e) {
            return true;
        }
    });

    // Sort neglected by score descending
    neglected.sort((a, b) => b.score - a.score);

    if (neglected.length === 0) {
        elements.neglectedBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted">لا يوجد عائلات مهملة عالية الاستحقاق! المنطقة مغطاة بالكامل.</td>
            </tr>
        `;
    } else {
        elements.neglectedBody.innerHTML = neglected.slice(0, 100).map(f => `
            <tr>
                <td>${f.id_num}</td>
                <td><strong>${f.name}</strong></td>
                <td><span class="badge badge-${f.badge}">${f.category} (${f.score})</span></td>
                <td class="text-muted" style="font-size:0.75rem;">${f.reasons}</td>
                <td style="color:var(--color-warning); font-weight:700;">${f.last_delivery}</td>
            </tr>
        `).join('');
    }

    // Calculate critical coverage percentage
    const totalCritical = computedFamilies.filter(f => f.category === 'A').length;
    if (totalCritical > 0) {
        const criticalServedRecently = computedFamilies.filter(f => {
            if (f.category !== 'A') return false;
            if (f.last_delivery === 'لم يستلم أبداً' || !f.last_delivery) return false;
            try {
                const lastDate = new Date(f.last_delivery);
                return !isNaN(lastDate.getTime()) && lastDate >= fiveMonthsAgo;
            } catch (e) {
                return false;
            }
        }).length;
        const coverage = Math.round((criticalServedRecently / totalCritical) * 100);
        elements.valCriticalCoverage.textContent = `${coverage} % `;
    } else {
        elements.valCriticalCoverage.textContent = '100%';
    }

    // Calculate Gini Coefficient of Aid Distribution
    // G = Sum(|x_i - x_j|) / (2 * n * Sum(x_i))
    const allQtys = computedFamilies.map(f => f.total_qty);
    const n = allQtys.length;
    const sum = allQtys.reduce((a, b) => a + b, 0);

    if (n > 1 && sum > 0) {
        let absoluteDiffsSum = 0;
        // Optimization: Sort quantities and calculate Gini efficiently
        const sorted = [...allQtys].sort((a, b) => a - b);
        for (let i = 0; i < n; i++) {
            absoluteDiffsSum += sorted[i] * (2 * i - n + 1);
        }
        const gini = absoluteDiffsSum / (n * sum);
        elements.valGini.textContent = `${Math.round(gini * 100) / 100}`;
    } else {
        elements.valGini.textContent = '0.00';
    }
}

// 8. Render analytics charts
function renderAnalyticsCharts() {
    // Destroy previous charts if they exist
    if (charts.categories) charts.categories.destroy();
    if (charts.scoringVsAid) charts.scoringVsAid.destroy();

    // 1. Doughnut chart for categories
    const counts = { A: 0, B: 0, C: 0, D: 0 };
    computedFamilies.forEach(f => {
        counts[f.category]++;
    });

    const ctxCat = document.getElementById('chart-categories').getContext('2d');
    charts.categories = new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: ['أولوية قصوى (A)', 'أولوية مرتفعة (B)', 'أولوية متوسطة (C)', 'حالات مستقرة (D)'],
            datasets: [{
                data: [counts.A, counts.B, counts.C, counts.D],
                backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'],
                borderColor: '#121824',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#8f9cae', font: { family: 'Cairo' } }
                }
            }
        }
    });

    // 2. Bar chart for average aid quantity per priority category
    const catA = computedFamilies.filter(f => f.category === 'A');
    const catB = computedFamilies.filter(f => f.category === 'B');
    const catC = computedFamilies.filter(f => f.category === 'C');
    const catD = computedFamilies.filter(f => f.category === 'D');

    const avgA = catA.length ? catA.reduce((sum, f) => sum + f.total_qty, 0) / catA.length : 0;
    const avgB = catB.length ? catB.reduce((sum, f) => sum + f.total_qty, 0) / catB.length : 0;
    const avgC = catC.length ? catC.reduce((sum, f) => sum + f.total_qty, 0) / catC.length : 0;
    const avgD = catD.length ? catD.reduce((sum, f) => sum + f.total_qty, 0) / catD.length : 0;

    const ctxBar = document.getElementById('chart-scoring-vs-aid').getContext('2d');
    charts.scoringVsAid = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['أولوية قصوى (A)', 'أولوية مرتفعة (B)', 'أولوية متوسطة (C)', 'حالات مستقرة (D)'],
            datasets: [{
                label: 'متوسط المساعدات المستلمة',
                data: [
                    Math.round(avgA * 10) / 10,
                    Math.round(avgB * 10) / 10,
                    Math.round(avgC * 10) / 10,
                    Math.round(avgD * 10) / 10
                ],
                backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'],
                borderRadius: 8,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: { color: '#666666', font: { family: 'Cairo', weight: 'bold' } },
                    grid: { display: false }
                },
                y: {
                    title: { display: true, text: 'متوسط الكمية المستلمة', color: '#666666', font: { family: 'Cairo', weight: 'bold' } },
                    ticks: { color: '#666666' },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

// 9. Excel Exports
async function exportAllToExcel() {
    if (computedFamilies.length === 0) {
        alert('لا توجد بيانات لتصديرها. الرجاء تشغيل التدقيق أولاً.');
        return;
    }

    const dataToExport = computedFamilies.map(f => ({
        'رقم الهوية': f.id_num,
        'الاسم الكامل': f.full_name,
        'العنوان': f.address,
        'أفراد العائلة': f.members_count,
        'النقاط': f.score,
        'التصنيف': f.category,
        'مرات الاستلام': f.times_served,
        'الكمية المستلمة': f.total_qty,
        'آخر استلام': f.last_delivery,
        'المسوغات والتحليل الطبي': f.reasons
    }));

    showLoading('جاري تحضير ملف الإكسل للتصدير...');
    const res = await ipcRenderer.invoke('export-excel', {
        data: dataToExport,
        fileName: 'تقرير_التدقيق_الذكي_الشامل.xlsx'
    });
    hideLoading();

    if (res.success) {
        alert(`تم تصدير الملف بنجاح إلى: ${res.filePath}`);
    } else if (res.error) {
        alert(`فشل تصدير الملف: ${res.error}`);
    }
}

async function exportOutliersToExcel() {
    if (computedFamilies.length === 0) {
        alert('لا توجد بيانات لتصديرها. الرجاء تشغيل التدقيق أولاً.');
        return;
    }

    // Get overserved list
    const servedFamilies = computedFamilies.filter(f => f.times_served > 0);
    const qtyArray = servedFamilies.map(f => f.total_qty);

    let overServed = [];
    if (qtyArray.length > 0) {
        const mean = qtyArray.reduce((a, b) => a + b, 0) / qtyArray.length;
        const variance = qtyArray.reduce((a, b) => a + Math.pow(b - mean, 2), 0) / qtyArray.length;
        const stdDev = Math.sqrt(variance);
        overServed = servedFamilies.filter(f => (stdDev > 0 ? (f.total_qty - mean) / stdDev : 0) > 2.5);
    }

    // Get neglected list
    const fiveMonthsAgo = new Date();
    fiveMonthsAgo.setMonth(fiveMonthsAgo.getMonth() - 5);
    const neglected = computedFamilies.filter(f => {
        if (!['A', 'B'].includes(f.category)) return false;
        if (f.last_delivery === 'لم يستلم أبداً' || !f.last_delivery) return true;
        try {
            const lastDate = new Date(f.last_delivery);
            return !isNaN(lastDate.getTime()) && lastDate < fiveMonthsAgo;
        } catch (e) { return true; }
    });

    const dataToExport = [];

    dataToExport.push({ 'نوع التحليل': 'شبهة تكرار (مستفيد شاذ)', 'رقم الهوية': '', 'الاسم الكامل': '', 'النقاط': '', 'مرات الاستلام': '', 'الكمية المستلمة': '', 'آخر استلام': '', 'المسوغات': '' });
    overServed.forEach(f => {
        dataToExport.push({
            'نوع التحليل': 'مستفيد شاذ (فوق المعدل)',
            'رقم الهوية': f.id_num,
            'الاسم الكامل': f.full_name,
            'النقاط': f.score,
            'مرات الاستلام': f.times_served,
            'الكمية المستلمة': f.total_qty,
            'آخر استلام': f.last_delivery,
            'المسوغات': f.reasons
        });
    });

    dataToExport.push({ 'نوع التحليل': 'عائلات مهمشة (عالية الاستحقاق منسية)', 'رقم الهوية': '', 'الاسم الكامل': '', 'النقاط': '', 'مرات الاستلام': '', 'الكمية المستلمة': '', 'آخر استلام': '', 'المسوغات': '' });
    neglected.forEach(f => {
        dataToExport.push({
            'نوع التحليل': 'مهمشة منسية',
            'رقم الهوية': f.id_num,
            'الاسم الكامل': f.full_name,
            'النقاط': f.score,
            'مرات الاستلام': f.times_served,
            'الكمية المستلمة': f.total_qty,
            'آخر استلام': f.last_delivery,
            'المسوغات': f.reasons
        });
    });

    showLoading('جاري تحضير ملف المخاطر للتصدير...');
    const res = await ipcRenderer.invoke('export-excel', {
        data: dataToExport,
        fileName: 'تقرير_المخاطر_والقيم_الشاذة.xlsx'
    });
    hideLoading();

    if (res.success) {
        alert(`تم تصدير الملف بنجاح إلى: ${res.filePath}`);
    } else if (res.error) {
        alert(`فشل تصدير الملف: ${res.error}`);
    }
}
