<?php

namespace App\Http\Filters;

use App\Models\Person;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PersonFilter extends BaseFilters
{
    protected $tableAlias = ''; // خاصية جديدة لتخزين اسم الجدول أو الاسم المستعار

    public function __construct(Builder $builder, string $tableAlias = '')
    {
        parent::__construct($builder);
        $this->tableAlias = $tableAlias ? $tableAlias . '.' : ''; // نضيف نقطة لو فيه اسم مستعار
    }

    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = [
        'id_num',
        'selected_id',
        'gender',
        'relatives_count',
        'dob',
        'social_status',
        'current_city',
        'neighborhood',
        'dob_from',
        'dob_to',
        'family_members_min',
        'family_members_max',
        'has_condition',
        'area_responsibles',
        'block',
        'first_name',
        'father_name',
        'grandfather_name',
        'family_name'
    ];

    protected function getColumnName(string $column): string
    {
        return $this->tableAlias . $column;
    }

    protected function idNum($value)
    {
        $notFoundIds = []; // مصفوفة لتخزين الهويات غير الموجودة
        $ids = []; // تعريف المتغير كفارغ في البداية

        if (!empty(trim($value))) {
            $ids = array_filter(
                preg_split("/\r\n|\n|\r/", $value), // تقسيم حسب الأسطر
                fn($id) => !empty(trim($id))
            );

            $familyHeadIds = []; // لتخزين أرقام هويات أرباب الأسر

            // المرور على كل هوية
            foreach ($ids as $id) {
                $trimmedId = trim($id);

                // البحث عن الشخص بالهوية
                $person = Person::where('id_num', $trimmedId)->first();

                if ($person) {
                    // إذا كان الشخص رب أسرة (relative_id = null)
                    if (is_null($person->relative_id)) {
                        $familyHeadIds[] = $trimmedId; // أضف هويته مباشرة
                    } else {
                        // إذا كان تابع، أضف هوية رب الأسرة (القيمة الموجودة في relative_id)
                        $familyHeadIds[] = $person->relative_id;
                    }
                } else {
                    // الهوية غير موجودة
                    $notFoundIds[] = $trimmedId;
                }
            }

            // إزالة التكرارات
            $familyHeadIds = array_unique($familyHeadIds);

            // تخزين الهويات غير الموجودة في الجلسة
            session(['notFoundIds' => $notFoundIds]);

            // تطبيق الفلتر على أرقام هويات أرباب الأسر
            if (!empty($familyHeadIds)) {
                return $this->builder->whereIn($this->getColumnName('id_num'), $familyHeadIds);
            }
        }

        return $this->builder;
    }


    public function selectedId($value)
    {
        if ($value) {
            $this->builder->sortingByIds($value);
        }
        return $this->builder;
    }

    protected function gender($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('gender'), $value);
        }
        return $this->builder;
    }

    protected function firstName(?string $value): Builder
    {
        if (! is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('first_name'), 'like', $value . '%');
        }
        return $this->builder;
    }

    protected function fatherName(?string $value): Builder
    {
        if (! is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('father_name'), 'like', $value . '%');
        }
        return $this->builder;
    }

    protected function grandfatherName(?string $value): Builder
    {
        if (! is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('grandfather_name'), 'like', $value . '%');
        }
        return $this->builder;
    }

    protected function familyName(?string $value): Builder
    {
        if (! is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('family_name'), 'like', $value . '%');
        }
        return $this->builder;
    }

    protected function relativesCount($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('relatives_count'), $value);
        }
        return $this->builder;
    }

    protected function dob($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('dob'), 'like', $value . '%');
        }
        return $this->builder;
    }
    protected function dobFrom($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->whereDate($this->getColumnName('dob'), '>=', $value);
        }
        return $this->builder;
    }
    protected function dobTo($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->whereDate($this->getColumnName('dob'), '<=', $value);
        }
        return $this->builder;
    }

    protected function socialStatus($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('social_status'), $value);
        }
        return $this->builder;
    }

    protected function currentCity($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('current_city'), $value);
        }
        return $this->builder;
    }

    protected function neighborhood($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('neighborhood'), $value);
        }
        return $this->builder;
    }

    protected function areaResponsibles($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('area_responsible_id'), $value);
        }
        return $this->builder;
    }

    protected function block($value): Builder
    {
        if (! is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('block_id'), $value);
        }
        return $this->builder;
    }

    protected function familyMembersMin($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('relatives_count'), '>=', $value);
        }
        return $this->builder;
    }

    protected function familyMembersMax($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('relatives_count'), '<=', $value);
        }
        return $this->builder;
    }
    protected function hasCondition($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where($this->getColumnName('has_condition'), $value);
        }
        return $this->builder;
    }
}