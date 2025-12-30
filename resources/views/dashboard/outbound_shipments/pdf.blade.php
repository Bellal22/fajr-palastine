<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>إرسالية صادر - {{ $outbound_shipment->shipment_number }}</title>
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

        .signature-table .signature-name {
            margin: 10px 0;
            font-size: 11px;
            color: #555;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>إرسالية صادر</h1>
        <div class="top-info">
            <table>
                <tr>
                    <td class="right"><strong>رقم الإرسالية:</strong> {{ $outbound_shipment->shipment_number }}</td>
                    <td class="left"><strong>التاريخ:</strong> {{ $outbound_shipment->created_at->format('Y-m-d h:i A') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="info">
        <p><strong>الكوبون (المشروع):</strong> {{ $outbound_shipment->project->name ?? 'غير محدد' }}</p>
        <p><strong>المخزن الفرعي:</strong> {{ $outbound_shipment->subWarehouse->name ?? 'غير محدد' }}</p>
        @if($outbound_shipment->subWarehouse)
            <p><strong>العنوان:</strong> {{ $outbound_shipment->subWarehouse->address ?? '-' }}</p>
            <p><strong>الهاتف:</strong> {{ $outbound_shipment->subWarehouse->phone ?? '-' }}</p>
            @if($outbound_shipment->subWarehouse->contact_person)
                <p><strong>المسؤول:</strong> {{ $outbound_shipment->subWarehouse->contact_person }}</p>
            @endif
        @endif
        @if($outbound_shipment->driver_name)
            <p><strong>اسم السائق:</strong> {{ $outbound_shipment->driver_name }}</p>
        @endif
    </div>

    <h3>بيان الصادر</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">#</th>
                <th style="width: 20%;">النوع</th>
                <th style="width: 37%;">اسم الطرد</th>
                <th style="width: 15%;">الكمية</th>
                <th style="width: 20%;">الوزن (كجم)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($outbound_shipment->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    @if($item->shippable_type === 'App\Models\ReadyPackage')
                        <span class="badge badge-success">طرد جاهز</span>
                    @else
                        <span class="badge badge-info">طرد داخلي</span>
                    @endif
                </td>
                <td>{{ $item->shippable->name ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->weight ? number_format($item->weight, 2) : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="empty-row">لا توجد بنود في هذه الإرسالية</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($outbound_shipment->notes)
    <div class="notes">
        <div class="notes-title">ملاحظات:</div>
        <p>{{ $outbound_shipment->notes }}</p>
    </div>
    @endif

    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-title">توقيع أمين المخزن</div>
                @if($outbound_shipment->subWarehouse && $outbound_shipment->subWarehouse->contact_person)
                    <p class="signature-name">{{ $outbound_shipment->subWarehouse->contact_person }}</p>
                @endif
                <div class="signature-space"></div>
                <div class="signature-line"></div>
            </td>
            <td>
                <div class="signature-title">توقيع السائق</div>
                @if($outbound_shipment->driver_name)
                    <p class="signature-name">{{ $outbound_shipment->driver_name }}</p>
                @endif
                <div class="signature-space"></div>
                <div class="signature-line"></div>
            </td>
        </tr>
    </table>
</body>
</html>
