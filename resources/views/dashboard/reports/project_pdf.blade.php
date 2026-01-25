<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>تقرير مشروع - {{ $project->name }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 11px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #FF6F00;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0 0 15px 0;
            font-size: 24px;
            color: #FF6F00;
            font-weight: bold;
        }

        .top-info {
            margin-top: 10px;
        }

        .top-info table {
            width: 100%;
            border: none;
            margin: 0;
            box-shadow: none;
        }

        .top-info td {
            border: none;
            padding: 5px;
            font-size: 12px;
        }

        .top-info .right {
            text-align: right;
            width: 50%;
        }

        .top-info .left {
            text-align: left;
            width: 50%;
        }

        .info {
            margin: 20px 0;
            background: linear-gradient(to left, #fff3e6, #ffffff);
            padding: 15px 20px;
            border-right: 4px solid #FF6F00;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .info p {
            margin: 8px 0;
            font-size: 12px;
            line-height: 1.8;
        }

        h3 {
            margin: 30px 0 15px 0;
            color: #FF6F00;
            border-bottom: 2px solid #FFE0B2;
            padding-bottom: 8px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        th, td {
            border: 1px solid #E0E0E0;
            padding: 10px 8px;
            text-align: center;
        }

        th {
            background: linear-gradient(to bottom, #FF8F00, #FF6F00);
            color: white;
            font-weight: bold;
            font-size: 12px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        tbody tr:nth-child(odd) {
            background-color: #FFFBF5;
        }

        tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        td {
            font-size: 11px;
            color: #555;
        }

        tfoot {
            background: linear-gradient(to bottom, #FFE0B2, #FFECB3);
        }

        tfoot th {
            background: none;
            color: #333;
            font-weight: bold;
            font-size: 12px;
        }

        .notes {
            margin-top: 25px;
            padding: 15px;
            border: 2px dashed #FF6F00;
            border-radius: 8px;
            min-height: 80px;
            background: linear-gradient(135deg, #fffbf5 0%, #fff3e6 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 13px;
            color: #FF6F00;
        }

        .notes p {
            font-size: 11px;
            line-height: 1.8;
            color: #555;
        }

        .signature-table {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            padding: 20px;
            text-align: center;
            vertical-align: top;
            border: 2px solid #E0E0E0;
            background-color: #FAFAFA;
        }

        .signature-table .signature-title {
            font-size: 13px;
            font-weight: bold;
            color: #FF6F00;
            margin-bottom: 10px;
        }

        .signature-table .signature-space {
            height: 60px;
            margin-bottom: 10px;
        }

        .signature-table .signature-line {
            border-top: 2px solid #333;
            width: 70%;
            margin: 0 auto;
        }

        strong {
            color: #222;
            font-weight: bold;
        }

        .empty-row {
            color: #999;
            font-style: italic;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
        }

        /* Progress bar styles */
        .progress-container {
            margin: 20px 0;
            background: #eee;
            border-radius: 6px;
            overflow: hidden;
            height: 12px;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(to right, #FF6F00, #FF8F00);
            transition: width 0.5s;
        }

        /* Color variants for progress bar */
        .bg-success {
            background: linear-gradient(to right, #28a745, #20c997);
        }
        .bg-warning {
            background: linear-gradient(to right, #ffc107, #fd7e14);
        }
        .bg-danger {
            background: linear-gradient(to right, #dc3545, #c82333);
        }

        .counter-grid {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            text-align: center;
        }

        .counter-item {
            flex: 1;
            padding: 15px;
            border-radius: 8px;
            margin: 0 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
        }

        .counter-item h2 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .counter-item p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
        }

        .summary-box {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .summary-item {
            display: inline-block;
            margin: 0 15px;
            font-size: 14px;
        }

        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .font-weight-bold { font-weight: bold; }
        .h5 { font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير مشروع</h1>
        <div class="top-info">
            <table>
                <tr>
                    <td class="right"><strong>اسم المشروع:</strong> {{ $project->name }}</td>
                    <td class="left"><strong>تاريخ التقرير:</strong> {{ now()->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <td class="right"><strong>الوصف:</strong> {{ $project->description ?? 'لا يوجد' }}</td>
                    <td class="left"><strong>الحالة:</strong> {{ $project->status }}</td>
                </tr>
                <tr>
                    <td class="right"><strong>تاريخ البدء:</strong> {{ $project->start_date ? $project->start_date->format('Y-m-d') : 'غير محدد' }}</td>
                    <td class="left"><strong>تاريخ الانتهاء:</strong> {{ $project->end_date ? $project->end_date->format('Y-m-d') : 'غير محدد' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="info">
        <p><strong>إجمالي المستهدفين:</strong> {{ $project->total_candidates }}</p>
        <p><strong>تم الاستلام:</strong> {{ $project->received_count }}</p>
        <p><strong>لم يستلم:</strong> {{ $project->not_received_count }}</p>
        <p><strong>نسبة الإنجاز:</strong>
            @if($project->total_candidates > 0)
                {{ round(($project->received_count / $project->total_candidates) * 100, 1) }}%
            @else
                0%
            @endif
        </p>
    </div>

    <h3>توزيع المستفيدين (تم الاستلام) حسب المناطق</h3>
    <table>
        <thead>
            <tr>
                <th>المنطقة</th>
                <th>عدد المستفيدين</th>
                <th>النسبة</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAreaCount = 0; @endphp
            @forelse($project->area_breakdown as $areaId => $count)
                @php $totalAreaCount += $count; @endphp
                <tr>
                    <td>{{ $areas[$areaId]->name ?? 'غير محدد' }}</td>
                    <td>{{ $count }}</td>
                    <td>
                        @if($project->received_count > 0)
                            {{ round(($count / $project->received_count) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty-row">لا يوجد بيانات متاحة.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th>الإجمالي</th>
                <th>{{ $totalAreaCount }}</th>
                <th>100%</th>
            </tr>
        </tfoot>
    </table>

    <h3>توزيع المستفيدين (تم الاستلام) حسب المستودع الفرعي</h3>
    <table>
        <thead>
            <tr>
                <th>المستودع الفرعي</th>
                <th>عدد المستفيدين</th>
                <th>النسبة</th>
            </tr>
        </thead>
        <tbody>
            @php $totalWarehouseCount = 0; @endphp
            @forelse($project->warehouse_breakdown as $subWarehouseId => $count)
                @php $totalWarehouseCount += $count; @endphp
                <tr>
                    <td>{{ $subWarehouses[$subWarehouseId]->name ?? 'غير محدد' }}</td>
                    <td>{{ $count }}</td>
                    <td>
                        @if($project->received_count > 0)
                            {{ round(($count / $project->received_count) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty-row">لا يوجد بيانات متاحة.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th>الإجمالي</th>
                <th>{{ $totalWarehouseCount }}</th>
                <th>100%</th>
            </tr>
        </tfoot>
    </table>

    <h3>إجمالي الكوبونات المسلمة</h3>
    <div class="summary-box">
        <div class="summary-item">
            <strong>عدد الكوبونات:</strong> {{ $totalDeliveredCoupons }}
        </div>
    </div>
</body>
</html>
