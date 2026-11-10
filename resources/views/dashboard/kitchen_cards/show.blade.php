<x-layout :title="trans('kitchen_cards.actions.show')" :breadcrumbs="['dashboard.kitchen_cards.show', $kitchenCard]">

    <div class="row justify-content-center">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-qrcode"></i> تفاصيل بطاقة المطبخ والرمز المربع
                @endslot

                <div class="text-center my-4" id="printable-card" style="border: 2px dashed #007bff; padding: 30px; border-radius: 15px; background: #fff; max-width: 450px; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <div style="font-family: 'Cairo', sans-serif; text-align: center; direction: rtl;">
                        <h4 style="color: #007bff; font-weight: bold; margin-bottom: 5px;">جمعية الفجر الشبابي الفلسطيني</h4>
                        <h6 style="color: #666; margin-bottom: 25px; font-weight: 600;">بطاقة مراجعة المطبخ</h6>
                        
                        <div class="row mb-4" style="font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 15px; margin: 0 10px;">
                            <div class="col-6 text-right">
                                <strong style="color: #0056b3;">اسم المخيم:</strong> <span style="font-weight: 600;">{{ $kitchenCard->camp_name }}</span>
                            </div>
                            <div class="col-6 text-left" style="border-right: 1px solid #eee;">
                                <strong style="color: #0056b3;">رقم البطاقة:</strong> <span style="font-weight: 600;">{{ $kitchenCard->card_number }}</span>
                            </div>
                        </div>

                        <div class="my-4 d-flex flex-column align-items-center justify-content-center">
                            <!-- QR Code Container -->
                            <div id="qrcode-canvas" style="border: 1px solid #ddd; padding: 12px; border-radius: 12px; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: inline-block;"></div>
                            <div style="font-weight: bold; margin-top: 15px; font-family: monospace; letter-spacing: 1px; color: #555; font-size: 14px;">{{ $kitchenCard->barcode }}</div>
                        </div>
                    </div>
                </div>

                @slot('footer')
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dashboard.kitchen_cards.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                        <button onclick="printCard()" class="btn btn-success">
                            <i class="fas fa-print"></i> طباعة البطاقة والرمز
                        </button>
                    </div>
                @endslot
            @endcomponent
        </div>
    </div>

    <!-- Offline QRCode Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new QRCode(document.getElementById("qrcode-canvas"), {
                text: "{{ route('complaint.kitchen') . '?barcode=' . $kitchenCard->barcode }}",
                width: 200,
                height: 200,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        });

        function printCard() {
            const printContent = document.getElementById("printable-card").innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>طباعة بطاقة الباركود</title>');
            printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">');
            printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/all.min.css">');
            printWindow.document.write('<style>body { font-family: "Cairo", sans-serif; padding: 40px; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #fff; direction: rtl; } #printable-card { border: 2px dashed #007bff; padding: 30px; border-radius: 15px; width: 450px; text-align: center; } img { display: inline-block !important; margin: 0 auto; }</style>');
            printWindow.document.write('</head><body><div id="printable-card">');
            printWindow.document.write(printContent);
            printWindow.document.write('</div></body></html>');
            printWindow.document.close();
            
            printWindow.setTimeout(function() {
                printWindow.print();
                printWindow.close();
            }, 600);
        }
    </script>

</x-layout>
