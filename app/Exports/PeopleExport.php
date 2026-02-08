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
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PeopleExport implements FromQuery, WithHeadings, WithChunkReading, WithStyles, WithMapping, ShouldQueue, ShouldAutoSize
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
                ->select([
                    'p.id_num',
                    'p.first_name',
                    'p.father_name',
                    'p.grandfather_name',
                    'p.family_name',
                    'p.gender',
                    'p.phone',
                    'p.area_responsible_id',
                    'p.block_id',
                    'p.social_status',
                    'p.dob',
                    'p.relatives_count',

                    // الزوجة 1
                    DB::raw('(SELECT id_num
                              FROM persons
                              WHERE relative_id = p.id_num
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1) AS wife1_id_num'),
                    DB::raw('(SELECT TRIM(CONCAT(first_name," ",father_name," ",grandfather_name," ",family_name))
                              FROM persons
                              WHERE relative_id = p.id_num
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1) AS wife1_full_name'),

                    // الزوجة 2
                    DB::raw('(SELECT id_num
                              FROM persons
                              WHERE relative_id = p.id_num
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1 OFFSET 1) AS wife2_id_num'),
                    DB::raw('(SELECT TRIM(CONCAT(first_name," ",father_name," ",grandfather_name," ",family_name))
                              FROM persons
                              WHERE relative_id = p.id_num
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1 OFFSET 1) AS wife2_full_name'),

                    // الزوجة 3
                    DB::raw('(SELECT id_num
                              FROM persons
                              WHERE relative_id = p.id_num
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1 OFFSET 2) AS wife3_id_num'),
                    DB::raw('(SELECT TRIM(CONCAT(first_name," ",father_name," ",grandfather_name," ",family_name))
                              FROM persons
                              WHERE relative_id = p.id_num
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1 OFFSET 2) AS wife3_full_name'),

                    // الزوجة 4
                    DB::raw('(SELECT id_num
                              FROM persons
                              WHERE relative_id = p.id_num
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1 OFFSET 3) AS wife4_id_num'),
                    DB::raw('(SELECT TRIM(CONCAT(first_name," ",father_name," ",grandfather_name," ",family_name))
                              FROM persons
                              WHERE relative_id = p.id_num
                                    AND relationship = "زوجة"
                              ORDER BY id_num
                              LIMIT 1 OFFSET 3) AS wife4_full_name'),

                    'p.city',
                    'p.current_city',
                    'p.neighborhood',
                    'p.employment_status',
                    'p.has_condition',
                    'p.api_sync_status',
                ])
                ->from('persons AS p');

            /* منطق الفلاتر كما هو */
            $isView = $this->request->route() &&
                str_contains($this->request->route()->getName(), 'view');

            if ($isView) {
                // صفحة view: شرط وجود area_responsible_id و block_id
                $query->whereNotNull('p.area_responsible_id')
                    ->whereNotNull('p.block_id');
            } else {
                // صفحة index: شرط عدم وجود relationship و عدم وجود block_id
                $query->whereNull('p.relationship')
                    ->whereNull('p.block_id');
            }

            if ($id = $this->request->get('area_responsible_id')) {
                $query->where('p.area_responsible_id', $id);
            } elseif (auth()->user()?->isSupervisor()) {
                $isView
                    ? $query->where('p.area_responsible_id', auth()->id())
                    : $query->where(function ($q) {
                        $q->where('p.area_responsible_id', auth()->id())
                            ->orWhereNull('p.area_responsible_id');
                    });
            }

            if ($block = $this->request->get('block_id')) {
                $query->where('p.block_id', $block);
            }

            $query->latest('p.created_at');

            /* تطبيق بقية الفلاتر */
            $filter = new PersonFilter($query, 'p');
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
            'رقم الهوية',
            'الاسم الرباعي',
            'الاسم الأول',
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

        // تركيب الاسم الرباعي
        $fullName = trim($p->first_name . ' ' . $p->father_name . ' ' . $p->grandfather_name . ' ' . $p->family_name);

        return [
            $i,
            $p->id_num,
            $fullName,
            $p->first_name,
            $p->father_name,
            $p->grandfather_name,
            $p->family_name,
            $p->gender,
            $p->phone,
            $p->areaResponsible?->name ?? 'غير متوفر',
            $p->block?->name ?? 'غير متوفر',
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

        $sheet->getStyle('A1:X1')->applyFromArray([
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
