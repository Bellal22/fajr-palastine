<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف استلام طرود - مشروع الـ RRM - {{ $batch->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        .print-header {
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .table-print {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }
        .table-print th, .table-print td {
            border: 1px solid #444 !important;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .table-print th {
            background-color: #f2f2f2 !important;
            color: #000;
            font-weight: bold;
            text-align: center;
        }
        .signature-box {
            border-top: 1px dashed #000;
            width: 80%;
            margin: 30px auto 0;
            text-align: center;
            padding-top: 5px;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    {{-- Toolbar (Hidden in Print) --}}
    <div class="no-print mb-4 p-3 bg-light rounded border d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-print mr-1 text-primary"></i> كشف استلام رسمي لمشروع الـ RRM (جاهز للطباعة)
            </h5>
            <small class="text-muted">اضغط زر طباعة أدناه لتجهيز المستند الورقي أو حفظه كـ PDF.</small>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary font-weight-bold">
                <i class="fas fa-print mr-1"></i> طباعة الآن
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times mr-1"></i> إغلاق
            </button>
        </div>
    </div>

    {{-- Print Header --}}
    <div class="print-header">
        <div class="row align-items-center text-center">
            <div class="col-3 text-right">
                <h6 class="font-weight-bold mb-1">جمعية فجر فلسطين</h6>
                <small class="d-block text-muted">إدارة المشاريع والإغاثة</small>
                <small class="d-block font-weight-bold text-dark">مشروع الـ RRM</small>
            </div>
            <div class="col-6">
                <h4 class="font-weight-bold mb-1">كشف استلام وتوزيع طرود - آلية الاستجابة السريعة</h4>
                <h5 class="font-weight-bold text-primary mb-0">{{ $batch->name }}</h5>
                <small class="text-muted">رمز الكشف: <strong>{{ $batch->code }}</strong></small>
            </div>
            <div class="col-3 text-left">
                <small class="d-block">تاريخ الكشف: <strong>{{ $batch->delivery_date ? $batch->delivery_date->format('Y-m-d') : date('Y-m-d') }}</strong></small>
                <small class="d-block">المخزن: <strong>{{ $batch->subWarehouse ? $batch->subWarehouse->name : '-' }}</strong></small>
                <small class="d-block">عدد المستفيدين: <strong>{{ $beneficiaries->count() }}</strong></small>
            </div>
        </div>
    </div>

    {{-- Batch General Notes --}}
    @if(!empty($batch->notes))
        <div class="p-2 mb-3 bg-light border rounded small">
            <strong><i class="fas fa-comment-dots mr-1"></i> ملاحظات الكشف العامة:</strong>
            <span>{{ $batch->notes }}</span>
        </div>
    @endif

    {{-- Parcel Bundle Info --}}
    @if(!empty($parcelsSummary))
        <div class="p-2 mb-3 bg-light border rounded small">
            <strong><i class="fas fa-boxes mr-1"></i> مكونات وأصناف حزمة الـ RRM المسلمة (الكمية الإجمالية من الأنواع):</strong>
            @foreach($parcelsSummary as $pName => $pQty)
                <span class="mr-3">
                    • {{ $pName }} <strong>(الكمية: {{ number_format($pQty) }})</strong>
                </span>
            @endforeach
        </div>
    @elseif(isset($batch->projects) && $batch->projects->count() > 0)
        @php
            $projects = $batch->projects;
            $quantities = $batch->project_quantities ?? [];
        @endphp
        <div class="p-2 mb-3 bg-light border rounded small">
            <strong><i class="fas fa-boxes mr-1"></i> مكونات حزمة الـ RRM الموزعة:</strong>
            @foreach($projects as $p)
                <span class="mr-3">
                    • {{ $p->name }} 
                    @if(isset($quantities[$p->id]))
                        <strong>(الكمية: {{ $quantities[$p->id] }})</strong>
                    @endif
                </span>
            @endforeach
        </div>
    @endif

    {{-- Beneficiaries Table with Signature Box --}}
    <table class="table-print mb-4">
        <thead>
            <tr>
                <th width="4%">#</th>
                <th width="13%">رقم الهوية</th>
                <th width="21%">اسم المستفيد الرباعي</th>
                <th width="11%">رقم الجوال</th>
                <th width="13%">المندوب / المنطقة</th>
                <th width="13%">الطرود المستلمة</th>
                <th width="13%">ملاحظات المستفيد</th>
                <th width="12%">توقيع المستلم / البصمة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($beneficiaries as $idx => $b)
                <tr>
                    <td class="text-center font-weight-bold">{{ $idx + 1 }}</td>
                    <td class="text-center font-weight-bold">{{ $b['id_num'] }}</td>
                    <td class="font-weight-bold">{{ $b['name'] }}</td>
                    <td class="text-center">{{ $b['phone'] }}</td>
                    <td>{{ $b['area_name'] }}</td>
                    <td class="small">{{ $b['parcels_summary'] }}</td>
                    <td class="small font-weight-bold text-center">{{ $b['notes'] ?: '-' }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-weight-bold" style="background: #f9f9f9;">
                <td colspan="6" class="text-right">
                    إجمالي المستفيدين: {{ $beneficiaries->count() }} مستفيد
                    @if(optional($batch->customProject)->is_cash && optional($batch->customProject)->cash_amount)
                        <span class="mr-3 text-success">
                            | القيمة النقدية للمستفيد: <strong>{{ $batch->customProject->formatted_cash_value }}</strong>
                            (إجمالي المبلغ المصروف: <strong>{{ number_format($beneficiaries->count() * (float) $batch->customProject->cash_amount, 2) }} {{ $batch->customProject->currency_name }}</strong>)
                        </span>
                    @endif
                </td>
                <td colspan="2" class="text-center">
                    إجمالي الطرود: {{ $beneficiaries->sum('total_quantity') }} طرد
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- Signatures Footer --}}
    <div class="row mt-5 pt-3">
        <div class="col-4 text-center">
            <h6 class="font-weight-bold mb-4">مسؤول التوزيع الميداني</h6>
            <div class="signature-box">الاسم والتوقيع</div>
        </div>
        <div class="col-4 text-center">
            <h6 class="font-weight-bold mb-4">أمين المخزن الفرعي</h6>
            <div class="signature-box">الاسم والتوقيع</div>
        </div>
        <div class="col-4 text-center">
            <h6 class="font-weight-bold mb-4">مدير دائرة المشاريع والإغاثة</h6>
            <div class="signature-box">الاعتماد والختم</div>
        </div>
    </div>

</body>
</html>
