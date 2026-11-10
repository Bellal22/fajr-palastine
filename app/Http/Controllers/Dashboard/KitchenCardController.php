<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\KitchenCard;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\KitchenCardRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class KitchenCardController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->authorizeResource(KitchenCard::class, 'kitchen_card');
    }

    public function index()
    {
        $query = KitchenCard::latest();

        if (request()->filled('camp_name')) {
            $query->where('camp_name', 'like', '%' . request('camp_name') . '%');
        }

        if (request()->filled('card_number')) {
            $query->where('card_number', 'like', '%' . request('card_number') . '%');
        }

        $cards = $query->paginate();
        return view('dashboard.kitchen_cards.index', compact('cards'));
    }

    public function create()
    {
        return view('dashboard.kitchen_cards.create');
    }

    private function generateShortBarcode()
    {
        do {
            $letters = '';
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            for ($i = 0; $i < 3; $i++) {
                $letters .= $chars[rand(0, strlen($chars) - 1)];
            }
            
            $digits = '';
            for ($i = 0; $i < 3; $i++) {
                $digits .= rand(0, 9);
            }
            
            $code = $letters . $digits;
        } while (KitchenCard::where('barcode', $code)->exists());

        return $code;
    }

    public function store(KitchenCardRequest $request)
    {
        $data = $request->validated();
        
        // Generate a 3-letter, 3-number short mixed-case barcode
        $data['barcode'] = $this->generateShortBarcode();

        $card = KitchenCard::create($data);

        flash()->success(trans('kitchen_cards.messages.created'));

        return redirect()->route('dashboard.kitchen_cards.index');
    }

    public function show(KitchenCard $kitchenCard)
    {
        return view('dashboard.kitchen_cards.show', compact('kitchenCard'));
    }

    public function edit(KitchenCard $kitchenCard)
    {
        return view('dashboard.kitchen_cards.edit', compact('kitchenCard'));
    }

    public function update(KitchenCardRequest $request, KitchenCard $kitchenCard)
    {
        $data = $request->validated();
        
        // Regenerate barcode only if not already in the 6-character short format
        if (strlen($kitchenCard->barcode) !== 6) {
            $data['barcode'] = $this->generateShortBarcode();
        }

        $kitchenCard->update($data);

        flash()->success(trans('kitchen_cards.messages.updated'));

        return redirect()->route('dashboard.kitchen_cards.index');
    }

    public function destroy(KitchenCard $kitchenCard)
    {
        $kitchenCard->delete();

        flash()->success(trans('kitchen_cards.messages.deleted'));

        return redirect()->route('dashboard.kitchen_cards.index');
    }

    public function import(\Illuminate\Http\Request $request)
    {
        $this->authorize('create', KitchenCard::class);

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $request->file('excel_file'));
        $sheet = $data[0] ?? [];
        
        $imported = 0;
        $skipped = 0;

        foreach ($sheet as $index => $row) {
            if ($index == 0) continue; // Skip header

            $campName = trim($row[0] ?? '');
            $cardNumber = trim($row[1] ?? '');

            if (empty($campName) || empty($cardNumber)) {
                continue;
            }

            // Check if unique combination of camp_name and card_number already exists
            $exists = KitchenCard::where('camp_name', $campName)
                ->where('card_number', $cardNumber)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // Generate short mixed-case barcode
            $barcode = $this->generateShortBarcode();

            KitchenCard::create([
                'camp_name' => $campName,
                'card_number' => $cardNumber,
                'barcode' => $barcode,
            ]);

            $imported++;
        }

        flash()->success("تم استيراد {$imported} بطاقة بنجاح، وتخطي {$skipped} بطاقات مكررة.");

        return redirect()->route('dashboard.kitchen_cards.index');
    }

    public function downloadTemplate()
    {
        $this->authorize('create', KitchenCard::class);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KitchenCardsTemplateExport, 'kitchen_cards_template.xlsx');
    }

    public function exportZip()
    {
        $this->authorize('viewAny', KitchenCard::class);

        $cards = KitchenCard::all();
        
        // 1. Create a unique temporary directory inside storage
        $tempDir = storage_path('app/temp_zip_' . uniqid());
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        mkdir($tempDir . '/barcodes', 0777, true);
        
        // 2. Generate the Excel file and save it in the temp directory
        $excelPath = $tempDir . '/kitchen_cards.xlsx';
        \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\KitchenCardsExport, 'temp_zip_excel.xlsx', 'local');
        rename(storage_path('app/temp_zip_excel.xlsx'), $excelPath);
        
        // 3. Generate QR Code images and save them in the barcodes/ directory
        foreach ($cards as $card) {
            // Generate QR Code natively and 100% offline using TCPDF2DBarcode
            $qrcodeUrl = route('complaint.kitchen') . '?barcode=' . $card->barcode;
            $qrcode = new \TCPDF2DBarcode($qrcodeUrl, 'QRCODE,H');
            $qrData = $qrcode->getBarcodePngData(8, 8); // w=8, h=8 for clean high quality image

            if ($qrData) {
                $cleanCamp = Str::slug($card->camp_name) ?: 'camp';
                $fileName = $cleanCamp . '_' . $card->card_number . '.png';
                file_put_contents($tempDir . '/barcodes/' . $fileName, $qrData);
            }
        }
        
        // 4. Create ZIP archive
        $zipPath = storage_path('app/kitchen_cards_' . now()->format('YmdHis') . '.zip');
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $zip->addFile($excelPath, 'kitchen_cards.xlsx');
            
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempDir . '/barcodes'),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = 'barcodes/' . substr($filePath, strlen($tempDir . '/barcodes/') + 0);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        }
        
        // 5. Clean up temporary directory
        $barcodeFiles = glob($tempDir . '/barcodes/*');
        foreach ($barcodeFiles as $file) {
            unlink($file);
        }
        rmdir($tempDir . '/barcodes');
        unlink($excelPath);
        rmdir($tempDir);
        
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}

