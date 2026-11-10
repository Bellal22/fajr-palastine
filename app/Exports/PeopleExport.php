<?php

namespace App\Exports;

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 0);
ini_set('max_input_time', -1);

use App\Models\Person;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Filters\PersonFilter;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class PeopleExport implements FromQuery, WithHeadings, WithChunkReading, WithStyles, WithMapping, ShouldQueue
{
    use Exportable;

    protected array $filters;
    protected Request $request;

    public function __construct(Request $request, array $filters = [])
    {
        $this->request = $request;
        $this->filters = $filters;
    }

    /* =============== مصدر البيانات =============== */
    public function query()
    {
        try {
            $query = Person::query()
                ->from('persons AS p')
                ->leftJoin('persons AS h', 'p.relative_id', '=', 'h.id_num')
                ->leftJoin('area_responsibles AS ar', 'p.area_responsible_id', '=', 'ar.id')
                ->leftJoin('blocks AS b', 'p.block_id', '=', 'b.id')
                ->leftJoin('area_responsibles AS har', 'h.area_responsible_id', '=', 'har.id')
                ->leftJoin('blocks AS hb', 'h.block_id', '=', 'hb.id')
                ->select([
                    // بيانات المبحوث (Searched Person)
                    'p.id_num as searched_id_num',
                    DB::raw('TRIM(CONCAT(p.first_name, " ", p.father_name, " ", p.grandfather_name, " ", p.family_name)) as searched_full_name'),

                    // بيانات رب الأسرة (Head details - Coalsced if p is head)
                    DB::raw('COALESCE(h.id_num, p.id_num) as head_id_num'),
                    DB::raw('COALESCE(h.first_name, p.first_name) as first_name'),
                    DB::raw('COALESCE(h.father_name, p.father_name) as father_name'),
                    DB::raw('COALESCE(h.grandfather_name, p.grandfather_name) as grandfather_name'),
                    DB::raw('COALESCE(h.family_name, p.family_name) as family_name'),
                    DB::raw('COALESCE(h.gender, p.gender) as gender'),
                    DB::raw('COALESCE(h.phone, p.phone) as phone'),
                    DB::raw('COALESCE(har.name, ar.name) as area_responsible_name'),
                    DB::raw('COALESCE(hb.name, b.name) as block_name'),
                    DB::raw('COALESCE(h.social_status, p.social_status) as social_status'),
                    DB::raw('COALESCE(h.dob, p.dob) as dob'),
                    DB::raw('COALESCE(h.relatives_count, p.relatives_count) as relatives_count'),

                    // الزوجات (منسوبة لرب الأسرة)
                    DB::raw('(SELECT id_num
                              FROM persons
                              WHERE relative_id = COALESCE(p.relative_id, p.id_num)
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1) AS wife1_id_num'),
                    DB::raw('(SELECT TRIM(CONCAT(first_name," ",father_name," ",grandfather_name," ",family_name))
                              FROM persons
                              WHERE relative_id = COALESCE(p.relative_id, p.id_num)
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1) AS wife1_full_name'),

                    DB::raw('(SELECT id_num
                              FROM persons
                              WHERE relative_id = COALESCE(p.relative_id, p.id_num)
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1 OFFSET 1) AS wife2_id_num'),
                    DB::raw('(SELECT TRIM(CONCAT(first_name," ",father_name," ",grandfather_name," ",family_name))
                              FROM persons
                              WHERE relative_id = COALESCE(p.relative_id, p.id_num)
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1 OFFSET 1) AS wife2_full_name'),

                    DB::raw('(SELECT id_num
                              FROM persons
                              WHERE relative_id = COALESCE(p.relative_id, p.id_num)
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1 OFFSET 2) AS wife3_id_num'),
                    DB::raw('(SELECT TRIM(CONCAT(first_name," ",father_name," ",grandfather_name," ",family_name))
                              FROM persons
                              WHERE relative_id = COALESCE(p.relative_id, p.id_num)
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1 OFFSET 2) AS wife3_full_name'),

                    DB::raw('(SELECT id_num
                              FROM persons
                              WHERE relative_id = COALESCE(p.relative_id, p.id_num)
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1 OFFSET 3) AS wife4_id_num'),
                    DB::raw('(SELECT TRIM(CONCAT(first_name," ",father_name," ",grandfather_name," ",family_name))
                              FROM persons
                              WHERE relative_id = COALESCE(p.relative_id, p.id_num)
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1 OFFSET 3) AS wife4_full_name'),

                    DB::raw('COALESCE(h.city, p.city) as city'),
                    DB::raw('COALESCE(h.current_city, p.current_city) as current_city'),
                    DB::raw('COALESCE(h.neighborhood, p.neighborhood) as neighborhood'),
                    DB::raw('COALESCE(h.employment_status, p.employment_status) as employment_status'),
                    DB::raw('COALESCE(h.has_condition, p.has_condition) as has_condition'),
                    DB::raw('COALESCE(h.api_sync_status, p.api_sync_status) as api_sync_status'),
                ]);

            /* منطق الفلاتر */
            $exportSource = $this->request->get('export_source');
            if (!$exportSource) {
                $isView = $this->request->route() &&
                    str_contains($this->request->route()->getName(), 'view');
                $exportSource = $isView ? 'view' : 'index';
            }

            // تطبيق الفلتر الرئيسي حسب نوع الصفحة (سواء كان هناك بحث بالهوية/الملف أو لا)
            if ($exportSource === 'view') {
                // صفحة العرض: يجب أن يكون رب الأسرة مخصصاً لمسؤول منطقة ومندوب
                $query->where(function($q) {
                    $q->where(function($sub) {
                        $sub->whereNull('p.relative_id')
                            ->whereNotNull('p.area_responsible_id')
                            ->whereNotNull('p.block_id');
                    })->orWhere(function($sub) {
                        $sub->whereNotNull('p.relative_id')
                            ->whereNotNull('h.area_responsible_id')
                            ->whereNotNull('h.block_id');
                    });
                });
            } elseif ($exportSource === 'index') {
                // صفحة البداية (غير المخصصين): يجب ألا يكون لرب الأسرة مندوب
                $query->where(function($q) {
                    $q->where(function($sub) {
                        $sub->whereNull('p.relative_id')
                            ->whereNull('p.block_id');
                    })->orWhere(function($sub) {
                        $sub->whereNotNull('p.relative_id')
                            ->whereNull('h.block_id');
                    });
                });
            }

            // إذا لم يكن هناك بحث برقم الهوية أو بالملف، نطبق فلتر رب الأسرة الافتراضي
            if (!$this->request->filled('id_num') && !$this->request->filled('filter_file_key')) {
                $query->whereNull('p.relative_id');
            }

            if ($id = $this->request->get('area_responsible_id')) {
                $query->where(function($q) use ($id) {
                    $q->where('p.area_responsible_id', $id)
                      ->orWhereExists(function($sub) use ($id) {
                          $sub->select(DB::raw(1))
                              ->from('blocks')
                              ->whereColumn('blocks.id', 'p.block_id')
                              ->where('blocks.area_responsible_id', $id);
                      });
                });
            } elseif (auth()->user()?->isSupervisor()) {
                $isView
                    ? $query->where('p.area_responsible_id', auth()->id())
                    : $query->where(function ($q) {
                        $q->where('p.area_responsible_id', auth()->id())
                          ->orWhereNull('p.area_responsible_id')
                          ->orWhereExists(function($sub) {
                              $sub->select(DB::raw(1))
                                  ->from('blocks')
                                  ->whereColumn('blocks.id', 'p.block_id')
                                  ->where('blocks.area_responsible_id', auth()->id());
                          });
                    });
            }

            if ($block = $this->request->get('block_id')) {
                $query->where('p.block_id', $block);
            }

            if ($this->request->has('unserved')) {
                $query->whereNotExists(function($q) {
                    $q->select(DB::raw(1))
                      ->from('project_beneficiaries')
                      ->whereColumn('project_beneficiaries.person_id', 'p.id')
                      ->where('status', 'مستلم');
                });
            }

            $query->latest('p.created_at');

            /* تطبيق بقية الفلاتر */
            $filter = new PersonFilter($query, 'p');
            // تجاوز تحويل الهوية لرب الأسرة لضمان ظهور الشخص الذي تم البحث عنه
            $filter->skipHeadMapping = true;
            return $filter->apply($this->request->all());
        } catch (\Exception $e) {
            logger()->error('Export query failed', [
                'msg'   => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine()
            ]);
            return Person::query()->whereRaw('0 = 1');
        }
    }

    /* =============== العناوين =============== */
    public function headings(): array
    {
        return [
            '#',
            'رقم الهوية (الذي تم البحث عنه)',
            'اسم الشخص الرباعي',
            'هوية رب الأسرة',
            'اسم رب الأسرة الرباعي',
            'اسم رب الأسرة',
            'اسم الأب',
            'اسم الجد',
            'اسم العائلة',
            'الجنس',
            'رقم الهاتف',
            'مسؤول المنطقة',
            'المندوب',
            'الحالة الاجتماعية',
            'تاريخ الميلاد',
            'عدد أفراد الأسرة',
            'هوية الزوجة 1',
            'اسم الزوجة 1 الكامل',
            'هوية الزوجة 2',
            'اسم الزوجة 2 الكامل',
            'هوية الزوجة 3',
            'اسم الزوجة 3 الكامل',
            'هوية الزوجة 4',
            'اسم الزوجة 4 الكامل',
            'المدينة الأصلية',
            'المدينة الحالية',
            'الحي السكني',
            'حالة العمل',
            'لديه حالة صحية',
            'حالة المزامنة',
        ];
    }

    /* =============== تحويل كل صف =============== */
    public function map($p): array
    {
        static $i = 0;
        $i++;

        // تركيب الاسم الرباعي لرب الأسرة
        $headFullName = trim($p->first_name . ' ' . $p->father_name . ' ' . $p->grandfather_name . ' ' . $p->family_name);

        return [
            $i,
            $p->searched_id_num,
            $p->searched_full_name,
            $p->head_id_num,
            $headFullName,
            $p->first_name,
            $p->father_name,
            $p->grandfather_name,
            $p->family_name,
            $p->gender,
            $p->phone,
            $p->area_responsible_name ?? 'غير متوفر',
            $p->block_name ?? 'غير متوفر',
            __($p->social_status),
            $p->dob,
            $p->relatives_count,
            $p->wife1_id_num,
            $p->wife1_full_name,
            $p->wife2_id_num,
            $p->wife2_full_name,
            $p->wife3_id_num,
            $p->wife3_full_name,
            $p->wife4_id_num,
            $p->wife4_full_name,
            __($p->city),
            __($p->current_city),
            __($p->neighborhood),
            $p->employment_status,
            $p->has_condition ? 'نعم' : 'لا',
            $p->api_sync_status,
        ];
    }

    /* =============== تنسيق مبسّط =============== */
    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        $sheet->getStyle('A1:AD1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);
        return [];
    }

    public function chunkSize(): int
    {
        return 500;  // يناسب 1 GB ذاكرة
    }
}
