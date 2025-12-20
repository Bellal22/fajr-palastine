<?php

namespace App\Exports;

use App\Models\Person;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Http\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class FamilyChildrenExport implements FromQuery, WithHeadings, WithChunkReading, WithStyles, WithMapping, ShouldQueue
{
    use Exportable;

    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * مصدر البيانات: استعلام معقد لجلب بيانات الأسر مع الأطفال
     */
    public function query()
    {
        $query = DB::table('persons AS child')
            ->select([
                // بيانات رب الأسرة
                'parent.id as parent_id',
                'parent.id_num as parent_id_num',
                DB::raw("CONCAT_WS(' ', parent.first_name, parent.father_name, parent.grandfather_name, parent.family_name) as parent_full_name"),
                'parent.gender as parent_gender',
                'parent.phone as parent_phone',
                'parent.dob as parent_dob',
                'parent.social_status as parent_social_status',
                'parent.city as parent_city',
                'parent.current_city as parent_current_city',
                'parent.neighborhood as parent_neighborhood',
                'parent.employment_status as parent_employment_status',
                'parent.housing_type as parent_housing_type',
                'parent.housing_damage_status as parent_housing_damage_status',
                'parent.relative_id as family_relative_id',
                'parent.block_id as parent_block_id',
                'parent.area_responsible_id as parent_area_responsible_id',

                // اسم مسؤول المنطقة والبلوك
                'area.name as area_responsible_name',
                'block.name as block_name',

                // ✅ جلب جميع الزوجات (سيتم تقسيمهم لاحقاً)
                DB::raw("GROUP_CONCAT(DISTINCT CASE WHEN wife.relationship = 'wife' THEN wife.id_num END ORDER BY wife.id SEPARATOR '|||') as wives_id_nums"),
                DB::raw("GROUP_CONCAT(DISTINCT CASE WHEN wife.relationship = 'wife' THEN CONCAT_WS(' ', wife.first_name, wife.father_name, wife.grandfather_name, wife.family_name) END ORDER BY wife.id SEPARATOR '|||') as wives_full_names"),
                DB::raw("GROUP_CONCAT(DISTINCT CASE WHEN wife.relationship = 'wife' THEN wife.phone END ORDER BY wife.id SEPARATOR '|||') as wives_phones"),

                // بيانات الطفل
                'child.id as child_id',
                'child.id_num as child_id_num',
                DB::raw("CONCAT_WS(' ', child.first_name, child.father_name, child.grandfather_name, child.family_name) as child_full_name"),
                'child.gender as child_gender',
                'child.dob as child_dob',
                'child.relationship as child_relationship',
                'child.has_condition as child_has_condition',
                'child.condition_description as child_condition_description',
                'child.block_id as child_block_id',

                // حسابات العمر
                DB::raw('TIMESTAMPDIFF(YEAR, child.dob, CURDATE()) as child_age_years'),
                DB::raw('TIMESTAMPDIFF(MONTH, child.dob, CURDATE()) as child_age_months'),
                DB::raw('TIMESTAMPDIFF(DAY, child.dob, CURDATE()) as child_age_days'),
            ])
            ->join('persons AS parent', 'child.relative_id', '=', 'parent.id_num')
            ->leftJoin('persons AS wife', function ($join) {
                $join->on('wife.relative_id', '=', 'parent.id_num')
                    ->where('wife.relationship', '=', 'wife');
            })
            ->leftJoin('area_responsibles AS area', 'parent.area_responsible_id', '=', 'area.id')
            ->leftJoin('blocks AS block', 'parent.block_id', '=', 'block.id')
            ->whereNotNull('child.relative_id')
            ->whereNull('parent.relationship')
            ->whereNotNull('parent.phone')
            ->whereNotNull('parent.block_id')
            ->whereNotIn('child.relationship', ['wife', 'father', 'mother'])
            // ✅ Group by الطفل (كل طفل صف منفصل)
            ->groupBy(
                'child.id',
                'child.id_num',
                'child.first_name',
                'child.father_name',
                'child.grandfather_name',
                'child.family_name',
                'child.gender',
                'child.dob',
                'child.relationship',
                'child.has_condition',
                'child.condition_description',
                'child.block_id',
                'child.relative_id',
                'parent.id',
                'parent.id_num',
                'parent.first_name',
                'parent.father_name',
                'parent.grandfather_name',
                'parent.family_name',
                'parent.gender',
                'parent.phone',
                'parent.dob',
                'parent.social_status',
                'parent.city',
                'parent.current_city',
                'parent.neighborhood',
                'parent.employment_status',
                'parent.housing_type',
                'parent.housing_damage_status',
                'parent.relative_id',
                'parent.block_id',
                'parent.area_responsible_id',
                'area.name',
                'block.name'
            )
            ->orderBy('child.relative_id')
            ->orderBy('parent.dob', 'asc')
            ->orderBy('child.dob', 'desc');

        // ==========================================
        // تطبيق فلاتر رب الأسرة
        // ==========================================

        if ($areaId = $this->request->get('area_responsible_id')) {
            $query->where('parent.area_responsible_id', $areaId);
        } elseif (auth()->user()?->isSupervisor()) {
            $query->where('parent.area_responsible_id', auth()->id());
        }

        if ($blockId = $this->request->get('block_id')) {
            $query->where('parent.block_id', $blockId);
        }

        if ($name = $this->request->get('name')) {
            $query->where(function ($q) use ($name) {
                $q->where('parent.first_name', 'like', "%{$name}%")
                    ->orWhere('parent.father_name', 'like', "%{$name}%")
                    ->orWhere('parent.grandfather_name', 'like', "%{$name}%")
                    ->orWhere('parent.family_name', 'like', "%{$name}%")
                    ->orWhereRaw("CONCAT_WS(' ', parent.first_name, parent.father_name, parent.grandfather_name, parent.family_name) LIKE ?", ["%{$name}%"]);
            });
        }

        if ($idNum = $this->request->get('id_num')) {
            $query->where('parent.id_num', 'like', "%{$idNum}%");
        }

        if ($phone = $this->request->get('phone')) {
            $query->where('parent.phone', 'like', "%{$phone}%");
        }

        if ($gender = $this->request->get('gender')) {
            $query->where('parent.gender', $gender);
        }

        if ($dobFrom = $this->request->get('dob_from')) {
            $query->whereDate('parent.dob', '>=', $dobFrom);
        }

        if ($dobTo = $this->request->get('dob_to')) {
            $query->whereDate('parent.dob', '<=', $dobTo);
        }

        if ($socialStatus = $this->request->get('social_status')) {
            $query->where('parent.social_status', $socialStatus);
        }

        if ($city = $this->request->get('city')) {
            $query->where('parent.city', 'like', "%{$city}%");
        }

        if ($currentCity = $this->request->get('current_city')) {
            $query->where('parent.current_city', 'like', "%{$currentCity}%");
        }

        if ($neighborhood = $this->request->get('neighborhood')) {
            $query->where('parent.neighborhood', 'like', "%{$neighborhood}%");
        }

        if ($employmentStatus = $this->request->get('employment_status')) {
            $query->where('parent.employment_status', $employmentStatus);
        }

        if ($housingType = $this->request->get('housing_type')) {
            $query->where('parent.housing_type', $housingType);
        }

        if ($housingDamageStatus = $this->request->get('housing_damage_status')) {
            $query->where('parent.housing_damage_status', $housingDamageStatus);
        }

        // ==========================================
        // تطبيق فلاتر الأطفال
        // ==========================================

        if ($childIdNum = $this->request->get('child_id_num')) {
            $query->where('child.id_num', 'like', "%{$childIdNum}%");
        }

        if ($childGender = $this->request->get('child_gender')) {
            $query->where('child.gender', $childGender);
        }

        if ($childDobFrom = $this->request->get('child_dob_from')) {
            $query->whereDate('child.dob', '>=', $childDobFrom);
        }

        if ($childDobTo = $this->request->get('child_dob_to')) {
            $query->whereDate('child.dob', '<=', $childDobTo);
        }

        if ($childDob = $this->request->get('child_dob')) {
            $query->where('child.dob', 'like', $childDob . '%');
        }

        if ($this->request->filled('child_age_months_from')) {
            $ageFrom = (int) $this->request->get('child_age_months_from');
            $query->havingRaw('child_age_months >= ?', [$ageFrom]);
        }

        if ($this->request->filled('child_age_months_to')) {
            $ageTo = (int) $this->request->get('child_age_months_to');
            $query->havingRaw('child_age_months <= ?', [$ageTo]);
        }

        if ($childRelationship = $this->request->get('child_relationship')) {
            $query->where('child.relationship', $childRelationship);
        }

        if ($this->request->filled('child_has_condition')) {
            $hasCondition = $this->request->get('child_has_condition');
            $query->where('child.has_condition', $hasCondition);
        }

        if ($conditionDesc = $this->request->get('child_condition_description')) {
            $query->where('child.condition_description', 'like', "%{$conditionDesc}%");
        }

        if ($childPersonStatus = $this->request->get('child_person_status')) {
            $query->where('child.person_status', $childPersonStatus);
        }

        return $query;
    }

    /**
     * عناوين الأعمدة في ملف Excel
     */
    public function headings(): array
    {
        return [
            '#',
            'رقم هوية رب الأسرة',
            'اسم رب الأسرة الكامل',
            'جنس رب الأسرة',
            'هاتف رب الأسرة',
            'تاريخ ميلاد رب الأسرة',
            'الحالة الاجتماعية',
            'المدينة الأصلية',
            'المدينة الحالية',
            'الحي السكني',
            'حالة العمل',
            'نوع السكن',
            'حالة تلف السكن',
            'مسؤول المنطقة',
            'البلوك',
            // ✅ الزوجات (حتى 4 زوجات)
            'هوية الزوجة 1',
            'اسم الزوجة 1',
            'هاتف الزوجة 1',
            'هوية الزوجة 2',
            'اسم الزوجة 2',
            'هاتف الزوجة 2',
            'هوية الزوجة 3',
            'اسم الزوجة 3',
            'هاتف الزوجة 3',
            'هوية الزوجة 4',
            'اسم الزوجة 4',
            'هاتف الزوجة 4',
            // بيانات الطفل
            'رقم هوية الطفل',
            'اسم الطفل الكامل',
            'جنس الطفل',
            'تاريخ ميلاد الطفل',
            'عمر الطفل (بالسنوات)',
            'عمر الطفل (بالشهور)',
            'عمر الطفل (بالأيام)',
            'صلة القرابة',
            'لديه حالة صحية؟',
            'وصف الحالة الصحية',
        ];
    }

    /**
     * تحويل كل صف من البيانات إلى مصفوفة للتصدير
     */
    public function map($row): array
    {
        static $i = 0;
        $i++;

        // ✅ تقسيم بيانات الزوجات
        $wivesIdNums = !empty($row->wives_id_nums) ? explode('|||', $row->wives_id_nums) : [];
        $wivesFullNames = !empty($row->wives_full_names) ? explode('|||', $row->wives_full_names) : [];
        $wivesPhones = !empty($row->wives_phones) ? explode('|||', $row->wives_phones) : [];

        // ملء بيانات الزوجات (حتى 4 زوجات)
        $wivesData = [];
        for ($w = 0; $w < 4; $w++) {
            $wivesData[] = $wivesIdNums[$w] ?? '';
            $wivesData[] = $wivesFullNames[$w] ?? '';
            $wivesData[] = $wivesPhones[$w] ?? '';
        }

        return array_merge(
            [
                $i,
                $row->parent_id_num,
                $row->parent_full_name,
                $row->parent_gender,
                $row->parent_phone,
                $row->parent_dob,
                __($row->parent_social_status),
                __($row->parent_city),
                __($row->parent_current_city),
                __($row->parent_neighborhood),
                __($row->parent_employment_status),
                __($row->parent_housing_type),
                __($row->parent_housing_damage_status),
                $row->area_responsible_name ?? 'غير محدد',
                $row->block_name ?? 'غير محدد',
            ],
            $wivesData, // ✅ بيانات الزوجات
            [
                $row->child_id_num,
                $row->child_full_name,
                $row->child_gender,
                $row->child_dob,
                $row->child_age_years,
                $row->child_age_months,
                $row->child_age_days,
                __($row->child_relationship),
                $row->child_has_condition ? 'نعم' : 'لا',
                $row->child_condition_description ?? '',
            ]
        );
    }

    /**
     * تنسيق شكل ملف Excel
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        // تحديد عرض الأعمدة
        $columns = array_merge(range('A', 'Z'), ['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM']);
        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setWidth(18);
        }
        $sheet->getColumnDimension('AM')->setWidth(30); // عمود الوصف

        // تنسيق رأس الجدول
        $sheet->getStyle('A1:AM1')->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Arial', 'size' => 11],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);

        // تنسيق باقي الصفوف
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('A2:AM' . $highestRow)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D3D3D3']
                    ]
                ],
            ]);
        }

        return [];
    }

    /**
     * حجم الدفعة لمعالجة البيانات
     */
    public function chunkSize(): int
    {
        return 500;
    }
}