<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\AreaResponsible;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\AreaResponsibleRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AreaResponsibleController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * AreaResponsibleController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(AreaResponsible::class, 'area_responsible');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $area_responsibles = AreaResponsible::filter()->withCount('neighborhoods')->latest()->paginate();

        return view('dashboard.area_responsibles.index', compact('area_responsibles'));
    }

    /**
     * Display a comprehensive report of area responsibles and their delegates.
     *
     * @return \Illuminate\Http\Response
     */
    public function report()
    {
        $areaResponsibles = AreaResponsible::with(['blocks'])->get();

        foreach ($areaResponsibles as $area) {
            foreach ($area->blocks as $block) {
                // Count families (heads)
                $block->families_count = $block->people()
                    ->whereNull('relative_id')
                    ->where('area_responsible_id', $area->id)
                    ->count();

                // Count individuals (sum of relatives_count + 1 for heads)
                $block->individuals_count = $block->people()
                    ->whereNull('relative_id')
                    ->where('area_responsible_id', $area->id)
                    ->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(relatives_count, 0), (SELECT COUNT(*) FROM persons AS p2 WHERE p2.relative_id = persons.id_num) + 1)'));
            }
        }

        return view('dashboard.area_responsibles.report', compact('areaResponsibles'));
    }

    /**
     * Export the report as PDF.
     */
    public function reportPdf()
    {
        $areaResponsibles = AreaResponsible::with(['blocks'])->get();

        foreach ($areaResponsibles as $area) {
            foreach ($area->blocks as $block) {
                $block->families_count = $block->people()
                    ->whereNull('relative_id')
                    ->where('area_responsible_id', $area->id)
                    ->count();

                $block->individuals_count = $block->people()
                    ->whereNull('relative_id')
                    ->where('area_responsible_id', $area->id)
                    ->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(relatives_count, 0), (SELECT COUNT(*) FROM persons AS p2 WHERE p2.relative_id = persons.id_num) + 1)'));
            }
        }

        $html = view('dashboard.area_responsibles.pdf', compact('areaResponsibles'))->render();

        // إنشاء كائن TCPDF مع دعم العربية
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // إعدادات PDF الأساسية
        $pdf->SetCreator('Area Responsibles System');
        $pdf->SetAuthor('System');
        $pdf->SetTitle('تقرير مسؤولي المناطق');

        // تفعيل دعم اللغة العربية والاتجاه RTL
        $pdf->setLanguageArray(array(
            'a_meta_charset' => 'UTF-8',
            'a_meta_dir' => 'rtl',
            'a_meta_language' => 'ar',
        ));

        // إعدادات الهوامش - تقليل الهامش العلوي
        $pdf->SetMargins(10, 5, 10);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(5);
        $pdf->SetAutoPageBreak(true, 10);

        // تعطيل الهيدر والفوتر الافتراضيين
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // إضافة صفحة
        $pdf->AddPage();

        // تعيين الخط الرسمي (aealarabiya)
        $pdf->SetFont('aealarabiya', '', 9);

        // تعيين اتجاه RTL
        $pdf->setRTL(true);

        // كتابة HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // إخراج PDF
        return response($pdf->Output('area_responsibles_report_' . date('Y-m-d_H-i-s') . '.pdf', 'D'))
            ->header('Content-Type', 'application/pdf');
    }

    /**
     * Export the report as Excel.
     */
    public function reportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AreaResponsiblesReportExport, 'area_responsibles_report_' . date('Y-m-d') . '.xlsx');
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = \App\Models\City::has('neighborhoods')->with('neighborhoods')->get();
        return view('dashboard.area_responsibles.create', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\AreaResponsibleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AreaResponsibleRequest $request)
    {
        $area_responsible = AreaResponsible::create($request->all());
        $area_responsible->neighborhoods()->sync($request->input('neighborhoods', []));

        flash()->success(trans('area_responsibles.messages.created'));

        return redirect()->route('dashboard.area_responsibles.show', $area_responsible);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @return \Illuminate\Http\Response
     */
    public function show(AreaResponsible $area_responsible)
    {
        $area_responsible->load('neighborhoods.city');
        return view('dashboard.area_responsibles.show', compact('area_responsible'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @return \Illuminate\Http\Response
     */
    public function edit(AreaResponsible $area_responsible)
    {
        $cities = \App\Models\City::has('neighborhoods')->with('neighborhoods')->get();
        $area_responsible->load('neighborhoods');
        return view('dashboard.area_responsibles.edit', compact('area_responsible', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\AreaResponsibleRequest $request
     * @param \App\Models\AreaResponsible $area_responsible
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AreaResponsibleRequest $request, AreaResponsible $area_responsible)
    {
        $area_responsible->update($request->all());
        $area_responsible->neighborhoods()->sync($request->input('neighborhoods', []));

        flash()->success(trans('area_responsibles.messages.updated'));

        return redirect()->route('dashboard.area_responsibles.show', $area_responsible);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(AreaResponsible $area_responsible)
    {
        $area_responsible->delete();

        flash()->success(trans('area_responsibles.messages.deleted'));

        return redirect()->route('dashboard.area_responsibles.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', AreaResponsible::class);

        $area_responsibles = AreaResponsible::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.area_responsibles.trashed', compact('area_responsibles'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(AreaResponsible $area_responsible)
    {
        $this->authorize('viewTrash', $area_responsible);

        return view('dashboard.area_responsibles.show', compact('area_responsible'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(AreaResponsible $area_responsible)
    {
        $this->authorize('restore', $area_responsible);

        $area_responsible->restore();

        flash()->success(trans('area_responsibles.messages.restored'));

        return redirect()->route('dashboard.area_responsibles.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\AreaResponsible $area_responsible
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(AreaResponsible $area_responsible)
    {
        $this->authorize('forceDelete', $area_responsible);

        $area_responsible->forceDelete();

        flash()->success(trans('area_responsibles.messages.deleted'));

        return redirect()->route('dashboard.area_responsibles.trashed');
    }

    public function refreshCount(AreaResponsible $areaResponsible)
    {
        try {
            $oldCount = $areaResponsible->people_count;
            $newCount = $areaResponsible->updatePeopleCount();

            return response()->json([
                'success' => true,
                'old_count' => $oldCount,
                'new_count' => $newCount,
                'message' => 'تم تحديث العدد بنجاح'
            ]);
        } catch (\Exception $e) {
            logger()->error('خطأ في تحديث عدد مسؤول المنطقة يدوياً', [
                'area_responsible_id' => $areaResponsible->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine() // إضافة معلومات السطر
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في التحديث: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تصدير بيانات جميع مسؤولي المناطق في ملف ZIP
     */
    public function exportAll()
    {
        // زيادة وقت التنفيذ والذاكرة لتجنب مشاكل Time Out
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        try {
            $areaResponsibles = AreaResponsible::with('blocks')->get();

            if ($areaResponsibles->isEmpty()) {
                flash()->error('لا يوجد مسؤولي مناطق لتصدير بياناتهم.');
                return redirect()->back();
            }

            // إنشاء مجلد مؤقت
            $zipFileName = 'area_responsibles_stats_' . date('Y-m-d_H-i-s') . '.zip';
            $zipFilePath = storage_path('app/public/' . $zipFileName);
            $zip = new \ZipArchive;

            if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {

                // التأكد من وجود المجلد في التخزين العام
                if (!\Illuminate\Support\Facades\Storage::disk('public')->exists('temp')) {
                    \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('temp');
                }

                foreach ($areaResponsibles as $areaResponsible) {
                    // تنظيف اسم الملف من الأحرف غير المسموح بها
                    $safeName = preg_replace('~[\\\\/:*?"<>|]~', '_', $areaResponsible->name);
                    $excelFileName = "{$safeName}.xlsx";

                    // استخدام التخزين على القرص العام
                    $stored = \Maatwebsite\Excel\Facades\Excel::store(
                        new \App\Exports\AreaResponsibleExport($areaResponsible),
                        'temp/' . $excelFileName,
                        'public'
                    );

                    if ($stored) {
                        $fullExcelPath = storage_path('app/public/temp/' . $excelFileName);
                        if (file_exists($fullExcelPath)) {
                             $zip->addFile($fullExcelPath, $excelFileName);
                        }
                    }
                }

                $zip->close();

                // تنظيف الملفات المؤقتة
                \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory('temp');

                return response()->download($zipFilePath)->deleteFileAfterSend(true);
            } else {
                flash()->error('فشل إنشاء ملف الضغط.');
                return redirect()->back();
            }

        } catch (\Exception $e) {
            logger()->error('Export All Area Responsibles Failed', ['error' => $e->getMessage()]);
            flash()->error('حدث خطأ أثناء التصدير: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}