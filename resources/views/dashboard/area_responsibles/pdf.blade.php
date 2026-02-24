<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>تقرير مسؤولي المناطق - {{ date('Y-m-d') }}</title>
    <style>
        @page {
            margin: 5mm 10mm 10mm 10mm;
        }

        body {
            font-family: 'aealarabiya', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
        }

        .header-section {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #FF6F00;
            padding-bottom: 8px;
        }

        .header-section h1 {
            margin: 0 0 5px 0;
            font-size: 18pt;
            color: #FF6F00;
            font-weight: bold;
        }

        h3 {
            margin: 10px 0 8px 0;
            color: #FF6F00;
            border-bottom: 1pt solid #FFE0B2;
            padding-bottom: 4px;
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 10px 0;
        }

        th, td {
            border: 0.1pt solid #444;
            padding: 6pt 4pt;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #FF6F00;
            color: #ffffff;
            font-weight: bold;
            font-size: 11pt;
            line-height: 2.2;
        }

        .col-index {
            background-color: #FFF3E6;
            color: #FF6F00;
            font-weight: bold;
            width: 5%;
        }

        tbody tr:nth-child(even) {
            background-color: #FFFBF5;
        }

        td {
            font-size: 9.5pt;
            color: #000;
            line-height: 1.4;
        }

        .area-name {
            color: #FF6F00;
            font-weight: bold;
            font-size: 13pt;
        }

        .summary-container {
            width: 100%;
            margin-top: 20px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table {
            background-color: #FFECB3;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @php
        $grandFamilies = 0;
        $grandIndividuals = 0;
        $totalResponsibles = $areaResponsibles->count();
    @endphp

    @foreach($areaResponsibles as $index => $areaResponsible)
        <div class="page-break">
            <div class="header-section">
                <h1>تقرير مسؤولي المناطق والمناديب</h1>
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="text-align: right; border: none; font-size: 9pt;"><strong>تاريخ التقرير:</strong> {{ date('d-m-Y') }}</td>
                        <td style="text-align: left; border: none; font-size: 9pt;"><strong>صفحة:</strong> {{ $loop->iteration }} / {{ $totalResponsibles + 1 }}</td>
                    </tr>
                </table>
            </div>

            <h3>إحصائية المسؤول: <span class="area-name">{{ $areaResponsible->name }}</span></h3>

            <table border="1" cellspacing="0" cellpadding="4">
                <thead>
                    <tr style="background-color: #FF6F00; color: #ffffff;">
                        <th style="width: 5%;">#</th>
                        <th style="width: 25%;">مسؤول المنطقة</th>
                        <th style="width: 30%;">المندوب (البلوك)</th>
                        <th style="width: 20%;">عدد الأسر</th>
                        <th style="width: 20%;">عدد الأفراد الكلي</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $areaFamilies = 0;
                        $areaIndividuals = 0;
                    @endphp
                    @foreach($areaResponsible->blocks as $blockIndex => $block)
                        @php
                            $areaFamilies += $block->families_count;
                            $areaIndividuals += $block->individuals_count;
                            $grandFamilies += $block->families_count;
                            $grandIndividuals += $block->individuals_count;
                        @endphp
                        <tr>
                            <td class="col-index" style="width: 5%;">{{ $blockIndex + 1 }}</td>
                            <td style="width: 25%; text-align: right; padding-right: 15px;">{{ $areaResponsible->name }}</td>
                            <td style="width: 30%; text-align: right; padding-right: 15px;">{{ $block->name }}</td>
                            <td style="width: 20%;"><strong>{{ number_format($block->families_count) }}</strong></td>
                            <td style="width: 20%;"><strong>{{ number_format($block->individuals_count) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="footer-table">
                    <tr style="background-color: #FFECB3; font-weight: bold;">
                        <td colspan="3" style="width: 60%; text-align: right; padding-right: 15px;">مجموع المنطقة</td>
                        <td style="width: 20%;">{{ number_format($areaFamilies) }}</td>
                        <td style="width: 20%;">{{ number_format($areaIndividuals) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach

    {{-- صفحة المجموع العام --}}
    <div class="summary-page">
        <div class="header-section">
            <h1>تقرير مسؤولي المناطق والمناديب</h1>
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="text-align: right; border: none; font-size: 9pt;"><strong>تاريخ التقرير:</strong> {{ date('d-m-Y') }}</td>
                    <td style="text-align: left; border: none; font-size: 9pt;"><strong>صفحة:</strong> {{ $totalResponsibles + 1 }} / {{ $totalResponsibles + 1 }}</td>
                </tr>
            </table>
        </div>

        <h3>المجموع العام لكافة المناطق</h3>

        <table border="1" cellspacing="0" cellpadding="10">
            <thead>
                <tr style="background-color: #FF6F00; color: #ffffff;">
                    <th style="width: 60%; line-height: 2;">البيان</th>
                    <th style="width: 40%; line-height: 2;">العدد الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 60%; text-align: right; padding-right: 20px;">إجمالي عدد الأسر</td>
                    <td style="width: 40%; text-align: center;"><strong>{{ number_format($grandFamilies) }}</strong></td>
                </tr>
                <tr>
                    <td style="width: 60%; text-align: right; padding-right: 20px;">إجمالي عدد الأفراد</td>
                    <td style="width: 40%; text-align: center;"><strong>{{ number_format($grandIndividuals) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
