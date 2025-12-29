<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إرسالية صادر - {{ $outbound_shipment->shipment_number }}</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 12px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        .info {
            margin: 20px 0;
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }
        .info p {
            margin: 8px 0;
            font-size: 13px;
        }
        h3 {
            margin-top: 30px;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #e9ecef;
            font-weight: bold;
            font-size: 13px;
        }
        td {
            font-size: 12px;
        }
        .signature-section {
            margin-top: 60px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 48%;
            text-align: center;
            vertical-align: top;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .signature-box p {
            margin: 5px 0;
        }
        .signature-box strong {
            font-size: 14px;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 2px solid #000;
            width: 60%;
            margin-left: auto;
            margin-right: auto;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            border: 1px dashed #999;
            min-height: 80px;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>إرسالية صادر</h1>
        <p><strong>رقم الإرسالية:</strong> {{ $outbound_shipment->shipment_number }}</p>
        <p><strong>التاريخ:</strong> {{ $outbound_shipment->created_at->format('Y-m-d h:i A') }}</p>
    </div>

    <div class="info">
        <p><strong>الكوبون:</strong> {{ $outbound_shipment->project->name ?? 'غير محدد' }}</p>
        <p><strong>المخزن الفرعي:</strong> {{ $outbound_shipment->subWarehouse->name ?? 'غير محدد' }}</p>
        @if($outbound_shipment->subWarehouse)
            <p><strong>العنوان:</strong> {{ $outbound_shipment->subWarehouse->address ?? '-' }}</p>
            <p><strong>الهاتف:</strong> {{ $outbound_shipment->subWarehouse->contact_phone ?? '-' }}</p>
        @endif
    </div>

    <h3>بيان الصادر:</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">#</th>
                <th style="width: 20%;">النوع</th>
                <th style="width: 35%;">الاسم</th>
                <th style="width: 15%;">الكمية</th>
                <th style="width: 22%;">الوزن (كجم)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($outbound_shipment->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    @if($item->shippable_type === 'App\Models\ReadyPackage')
                        طرد جاهز
                    @else
                        طرد داخلي
                    @endif
                </td>
                <td>{{ $item->shippable->name ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->weight ? number_format($item->weight, 2) : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5">لا توجد بنود في هذه الإرسالية</td>
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

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>توقيع أمين المخزن</strong></p>
            <div class="signature-line"></div>
        </div>
        <div class="signature-box">
            <p><strong>توقيع السائق</strong></p>
            @if($outbound_shipment->driver_signature)
                <p>{{ $outbound_shipment->driver_signature }}</p>
            @endif
            <div class="signature-line"></div>
        </div>
    </div>
</body>
</html>
