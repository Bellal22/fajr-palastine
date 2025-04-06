<?php

namespace App\Exports;

use App\Models\Person;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeopleExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;
    protected $index = 1;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Person::query();

        if (!empty($this->filters['selected_ids'])) {
            $ids = explode(',', $this->filters['selected_ids']);
            $query->whereIn('id', $ids);
        } else {
            if (!empty($this->filters['id_num'])) {
                $query->where('id_num', 'like', '%' . $this->filters['id_num'] . '%');
            }

            if (!empty($this->filters['gender'])) {
                $query->where('gender', $this->filters['gender']);
            }

            if (!empty($this->filters['social_status'])) {
                $query->where('social_status', $this->filters['social_status']);
            }

            if (!empty($this->filters['relatives_count'] ?? null)) {
                $query->where('relatives_count', $this->filters['relatives_count']);
            }

            if (!empty($this->filters['dob'] ?? null)) {
                $query->where('dob', 'like', $this->filters['dob'] . '%');
            }

            if (!empty($this->filters['current_city'] ?? null)) {
                $query->where('current_city', $this->filters['current_city']);
            }
        }

        return $query->get([
            'id_num',
            'first_name',
            'father_name',
            'grandfather_name',
            'family_name',
            'gender',
            'phone',
            'dob',
            'social_status',
            'city',
            'current_city',
            'neighborhood',
            'landmark',
            'housing_type',
            'housing_damage_status',
            'employment_status',
            'person_status',
            'relatives_count',
            'relative_id',
            'relationship',
            'has_condition',
            'condition_description',
        ]);
    }

    public function map($person): array
    {
        return [
            $this->index++,
            $person->id_num,
            $person->first_name,
            $person->father_name,
            $person->grandfather_name,
            $person->family_name,
            $person->gender,
            $person->phone,
            $person->dob,
            $person->social_status,
            $person->city,
            $person->current_city,
            $person->neighborhood,
            $person->landmark,
            $person->housing_type,
            $person->housing_damage_status,
            $person->employment_status,
            $person->person_status,
            $person->relatives_count,
            $person->has_condition,
            $person->condition_description,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'رقم الهوية',
            'الاسم الأول',
            'اسم الأب',
            'اسم الجد',
            'اسم العائلة',
            'الجنس',
            'رقم الجوال',
            'تاريخ الميلاد',
            'الحالة الاجتماعية',
            'المدينة',
            'المحافظة الحالية',
            'الحي السكني',
            'أقرب معلم',
            'نوع السكن',
            'حالة السكن',
            'حالة العمل',
            'حالة الشخص',
            'عدد الأفراد',
            'يعاني من حالة صحية',
            'وصف الحالة الصحية',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // تفعيل الاتجاه من اليمين إلى اليسار
        $sheet->setRightToLeft(true);

        // تحديد الأعمدة لتكون عرضها تلقائي بناءً على المحتوى
        foreach (range('A', 'Z') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // تحديد صف العناوين ليكون بالخط العريض وبحجم أكبر
        $sheet->getStyle('A1:Z1')->getFont()->setBold(true);
        $sheet->getStyle('A1:Z1')->getFont()->setSize(12); // تغيير حجم الخط للعناوين

        // تنسيق عمود الرقم ليكون أكبر (على سبيل المثال العمود الأول A)
        $sheet->getStyle('A')->getFont()->setSize(14); // تغيير حجم الخط لعمود الرقم
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // توسيط عمود الرقم

        // تنسيق باقي الأعمدة لتكون لليمين
        $sheet->getStyle('B1:Z1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT); // توسيط الأعمدة الأخرى لليمين

        // إضافة الحدود لجميع الخلايا
        $sheet->getStyle('A1:Z' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // تلوين الصف الأول والعمود الأول باللون البرتقالي
        $sheet->getStyle('A1:Z1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFA500'); // تلوين الصف الأول
        $sheet->getStyle('A')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFA500'); // تلوين العمود الأول

        return [
            1 => ['font' => ['bold' => true]], // الصف الأول يكون عريض
        ];
    }
}