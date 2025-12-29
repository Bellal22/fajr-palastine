<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ shape_arabic('إرسالية وارد - ' . $inbound_shipment->shipment_number) }}</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
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
            /* Ensure values and labels flow correctly with text-align right */
            direction: ltr;
        }
        .info {
            margin: 20px 0;
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            direction: ltr;
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
            direction: ltr; /* Ensure columns flow Left-to-Right in code, but we reverse content */
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
            direction: ltr;
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
        .notes {
            margin-top: 30px;
            padding: 15px;
            border: 1px dashed #999;
            min-height: 80px;
            direction: ltr;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
            text-align: right;
        }
        /* Align content to the right */
        p, div, table {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ shape_arabic('إرسالية وارد') }}</h1>
        <!-- Swapped Order: Value then Label -->
        <p>{{ $inbound_shipment->shipment_number }} <strong>{{ shape_arabic('رقم الإرسالية:') }}</strong></p>
        <p>{{ $inbound_shipment->created_at->format('Y-m-d h:i A') }} <strong>{{ shape_arabic('التاريخ:') }}</strong></p>
    </div>

    <div class="info">
        <!-- Swapped Order: Value then Label -->
        <p>{{ shape_arabic($inbound_shipment->supplier->name ?? 'غير محدد') }} <strong>{{ shape_arabic('المورد:') }}</strong></p>
        <p>{{ shape_arabic(trans('inbound_shipments.types.' . $inbound_shipment->inbound_type)) }} <strong>{{ shape_arabic('نوع الإرسالية:') }}</strong></p>
    </div>

    @if($inbound_shipment->isSingleItem())
        <h3>{{ shape_arabic('بيان الأصناف:') }}</h3>
        <table>
            <thead>
                <tr>
                    <!-- Reversed Columns: Weight, Quantity, Name, # -->
                    <th style="width: 25%;">{{ shape_arabic(trans('inbound_shipments.attributes.weight_kg')) }}</th>
                    <th style="width: 25%;">{{ shape_arabic(trans('inbound_shipments.attributes.quantity')) }}</th>
                    <th style="width: 40%;">{{ shape_arabic(trans('inbound_shipments.attributes.item_name')) }}</th>
                    <th style="width: 10%;">#</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inbound_shipment->items as $index => $item)
                <tr>
                    <td>{{ $item->weight }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ shape_arabic($item->name) }}</td>
                    <td>{{ $index + 1 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($inbound_shipment->isReadyPackage())
        <h3>{{ shape_arabic('بيان الطرود:') }}</h3>
        <table>
            <thead>
                <tr>
                    <!-- Reversed Columns: Weight, Quantity, Description, Name, # -->
                    <th style="width: 15%;">{{ shape_arabic(trans('inbound_shipments.attributes.weight_kg')) }}</th>
                    <th style="width: 15%;">{{ shape_arabic(trans('inbound_shipments.attributes.quantity')) }}</th>
                    <th style="width: 30%;">{{ shape_arabic(trans('inbound_shipments.attributes.description')) }}</th>
                    <th style="width: 30%;">{{ shape_arabic(trans('inbound_shipments.attributes.package_name')) }}</th>
                    <th style="width: 10%;">#</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inbound_shipment->readyPackages as $index => $package)
                <tr>
                    <td>{{ $package->weight }}</td>
                    <td>{{ $package->quantity }}</td>
                    <td>{{ shape_arabic($package->description) }}</td>
                    <td>{{ shape_arabic($package->name) }}</td>
                    <td>{{ $index + 1 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($inbound_shipment->notes)
    <div class="notes">
        <div class="notes-title">{{ shape_arabic('ملاحظات:') }}</div>
        <!-- Notes are usually long text. Mixing RTL/LTR block flow is hard.
             If we assume mostly Arabic, reshaping it works.
             But if it wraps, line 1 will be at top, line 2 below.
             Standard reshaping handles characters within a string.
             Word wrapping in LTR container works left-to-right.
             This is the one limitation. For short notes, it is fine. -->
        <p>{{ shape_arabic($inbound_shipment->notes) }}</p>
    </div>
    @endif

    <div class="signature-section">
        <!-- Layout: Left Cell, Right Cell.
             We want StoreKeeper on Right, Receiver on Left.
             So First Cell = Receiver, Second Cell = Storekeeper.
             (Since LTR fills Left then Right) -->
        <div class="signature-box">
            <p><strong>{{ shape_arabic('توقيع المستلم') }}</strong></p>
            <br><br>
            <p>.............................</p>
        </div>
        <div class="signature-box">
            <p><strong>{{ shape_arabic('توقيع أمين المخزن') }}</strong></p>
            <br><br>
            <p>.............................</p>
        </div>
    </div>
</body>
</html>
