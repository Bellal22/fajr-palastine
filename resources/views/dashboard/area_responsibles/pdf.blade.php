<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>تقرير مسؤولي المناطق - {{ date('Y-m-d') }}</title>
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
            padding: 8px 6px;
            text-align: center;
        }

        th {
            background: linear-gradient(to bottom, #FF8F00, #FF6F00);
            color: white;
            font-weight: bold;
            font-size: 15px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        tbody tr:nth-child(odd) {
            background-color: #FFFBF5;
        }

        tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        td {
            font-size: 14px;
            color: #555;
        }

        .text-right {
            text-align: right !important;
            padding-right: 12px !important;
        }

        .area-name {
            font-weight: bold;
            color: #FF6F00;
            margin-bottom: 3px;
        }

        .area-address {
            color: #666;
            margin-top: 2px;
        }

        .no-delegates {
            color: #999;
            font-style: italic;
        }

        tfoot {
            background: linear-gradient(to bottom, #FFE0B2, #FFECB3);
        }

        tfoot td {
            background: none;
            color: #333;
            font-weight: bold;
            font-size: 15px;
            border-top: 2px solid #FF6F00;
        }

        .footer {
            margin-top: 25px;
            text-align: left;
            font-size: 10px;
            color: #777;
        }

        strong {
            color: #222;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير مسؤولي المناطق والمناديب</h1>
        <div class="top-info">
            <table>
                <tr>
                    <td class="right"><strong>تاريخ التقرير:</strong> {{ date('d-m-Y') }}</td>
                    <td class="left"><strong>الوقت:</strong> {{ date('H:i A') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- تقدر ترجع العنوان إذا بدك --}}
    {{-- <h3>بيان مسؤولي المناطق والمناديب</h3> --}}

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">مسؤول المنطقة</th>
                <th style="width: 30%;">المندوب (البلوك)</th>
                <th style="width: 20%;">عدد الأسر</th>
                <th style="width: 20%;">عدد الأفراد الكلي</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandFamilies = 0;
                $grandIndividuals = 0;
                $counter = 1;
            @endphp
            @foreach($areaResponsibles as $areaResponsible)
                @php
                    $blocksCount = $areaResponsible->blocks->count();
                @endphp

                @if($blocksCount > 0)
                    @foreach($areaResponsible->blocks as $index => $block)
                        @php
                            $grandFamilies += $block->families_count;
                            $grandIndividuals += $block->individuals_count;
                        @endphp
                        <tr>
                            @if($index === 0)
                                <td rowspan="{{ $blocksCount }}">{{ $counter++ }}</td>
                                <td rowspan="{{ $blocksCount }}" class="text-right">
                                    <div class="area-name" style="font-size: 16px;">{{ $areaResponsible->name }}</div>
                                    @if($areaResponsible->address)
                                        <div class="area-address" style="font-size: 13px;">{{ $areaResponsible->address }}</div>
                                    @endif
                                </td>
                            @endif
                            <td>{{ $block->name }}</td>
                            <td>{{ number_format($block->families_count) }}</td>
                            <td><strong>{{ number_format($block->individuals_count) }}</strong></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td class="text-right">
                            <div class="area-name" style="font-size: 16px;">{{ $areaResponsible->name }}</div>
                        </td>
                        <td colspan="3" class="no-delegates" style="font-size: 13px;">لا يوجد مناديب</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>المجموع الكلي</strong></td>
                <td><strong>{{ number_format($grandFamilies) }}</strong></td>
                <td><strong>{{ number_format($grandIndividuals) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        طبع بواسطة النظام في: {{ date('Y-m-d H:i:s') }}
    </div>
</body>
</html>
