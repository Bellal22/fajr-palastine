<?php

namespace App\Exports;

ini_set('memory_limit', '256M'); // or '512M'

use App\Models\Person;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Http\Filters\PersonFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class PeopleExport implements FromCollection, WithHeadings, WithChunkReading, WithStyles, WithMapping
{
    use Exportable;

    protected array $filters;
    protected $request;

    public function __construct(Request $request, array $filters = [])
    {
        $this->request = $request;
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $query = Person::query()
            ->select([
                'p.id_num',
                'p.first_name',
                'p.father_name',
                'p.grandfather_name',
                'p.family_name',
                'p.gender',
                'p.phone',
                'p.social_status',
                'p.dob',
                'p.relatives_count',
                DB::raw('(SELECT id_num FROM persons WHERE relative_id = p.id_num AND relationship = \'wife\' ORDER BY id_num LIMIT 1) AS wife1_id_num'),
                DB::raw('(SELECT TRIM(CONCAT(first_name, \' \', father_name, \' \', grandfather_name, \' \', family_name)) FROM persons WHERE relative_id = p.id_num AND relationship = \'wife\' ORDER BY id_num LIMIT 1) AS wife1_full_name'),
                DB::raw('(SELECT id_num FROM persons WHERE relative_id = p.id_num AND relationship = \'wife\' ORDER BY id_num LIMIT 1, 1) AS wife2_id_num'),
                DB::raw('(SELECT TRIM(CONCAT(first_name, \' \', father_name, \' \', grandfather_name, \' \', family_name)) FROM persons WHERE relative_id = p.id_num AND relationship = \'wife\' ORDER BY id_num LIMIT 1, 1) AS wife2_full_name'),
                DB::raw('(SELECT id_num FROM persons WHERE relative_id = p.id_num AND relationship = \'wife\' ORDER BY id_num LIMIT 2, 1) AS wife3_id_num'),
                DB::raw('(SELECT TRIM(CONCAT(first_name, \' \', father_name, \' \', grandfather_name, \' \', family_name)) FROM persons WHERE relative_id = p.id_num AND relationship = \'wife\' ORDER BY id_num LIMIT 2, 1) AS wife3_full_name'),
                DB::raw('(SELECT id_num FROM persons WHERE relative_id = p.id_num AND relationship = \'wife\' ORDER BY id_num LIMIT 3, 1) AS wife4_id_num'),
                DB::raw('(SELECT TRIM(CONCAT(first_name, \' \', father_name, \' \', grandfather_name, \' \', family_name)) FROM persons WHERE relative_id = p.id_num AND relationship = \'wife\' ORDER BY id_num LIMIT 3, 1) AS wife4_full_name'),
                'p.city',
                'p.current_city',
                'p.neighborhood',
                'p.employment_status',
                'p.has_condition',
                'p.condition_description',
            ])
            ->from('persons AS p')
            ->whereNull('p.relationship')
            ->latest('p.created_at');

        $filter = new PersonFilter($query, 'p');
        return $filter->apply($this->filters)->get();
    }

    public function headings(): array
    {
        return [
            '#', // الرقم التسلسلي
            'رقم الهوية',
            'الاسم الأول',
            'اسم الأب',
            'اسم الجد',
            'اسم العائلة',
            'الجنس',
            'رقم الهاتف',
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
            'وصف الحالة الصحية',
        ];
    }

    public function map($person): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber, // الرقم التسلسلي
            $person->id_num,
            $person->first_name,
            $person->father_name,
            $person->grandfather_name,
            $person->family_name,
            $person->gender,
            $person->phone,
            __($person->social_status),
            $person->dob,
            $person->relatives_count,
            $person->wife1_id_num,
            $person->wife1_full_name,
            $person->wife2_id_num,
            $person->wife2_full_name,
            $person->wife3_id_num,
            $person->wife3_full_name,
            $person->wife4_id_num,
            $person->wife4_full_name,
            __($person->city),
            __($person->current_city),
            __($person->neighborhood),
            $person->employment_status,
            $person->has_condition == 1 ? 'نعم' : 'لا',
            $person->condition_description,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // تفعيل الاتجاه من اليمين إلى اليسار
        $sheet->setRightToLeft(true);

        // تحديد الأعمدة لتكون عرضها تلقائي بناءً على المحتوى
        foreach (range('A', 'Y') as $columnID) { // تم تعديل 'W' إلى 'X' لتغطية العمود الجديد
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // تغيير ارتفاع الصف الأول
        $sheet->getRowDimension(1)->setRowHeight(30); // زيادة الارتفاع للوضوح

        // تنسيق الخط للصف الأول
        $sheet->getStyle('A1:Y1')->getFont()->setBold(true)->setSize(12); // تم تعديل 'W' إلى 'X'

        // توسيط النص في الصف الأول (العناوين)
        $sheet->getStyle('A1:Y1')->getAlignment() // تم تعديل 'W' إلى 'X'
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // تنسيق عمود الرقم التسلسلي (A)
        $sheet->getStyle('A')->getFont()->setSize(11);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // تنسيق باقي الأعمدة لتكون لليمين (بدءًا من B)
        $sheet->getStyle('B:Y')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // تم تعديل 'W' إلى 'X'

        // إضافة الحدود لجميع الخلايا
        $sheet->getStyle('A1:Y' . $sheet->getHighestRow()) // تم تعديل 'W' إلى 'X'
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // تلوين الصف الأول
        $sheet->getStyle('A1:Y1')->getFill() // تم تعديل 'W' إلى 'X'
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFA500'); // اللون البرتقالي للصف الأول

        // تلوين العمود الأول بالكامل
        $column = 'A';
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("{$column}1:{$column}{$highestRow}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FFA500'); // تلوين العمود الأول باللون البرتقالي

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
