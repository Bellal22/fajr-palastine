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

            // تحقق مما إذا كانت الهوية موجودة في قاعدة البيانات
            foreach ($ids as $id) {
                if (!Person::where('id_num', trim($id))->exists()) {
                    $notFoundIds[] = trim($id); // أضف الهوية إلى المصفوفة إذا لم توجد
                }
            }

            // إذا كانت هناك هويات غير موجودة، مررها إلى الـ View
            session(['notFoundIds' => $notFoundIds]); // تخزين الهويات غير الموجودة في الجلسة
        }

        // تأكد من أن الفلتر لا يعطل الفلاتر الأخرى
        if (!empty($ids)) {
            return $this->builder->whereIn($this->getColumnName('id_num'), $ids);
        }

        return $this->builder; // إذا لم يكن هناك هويات، أعد الـ builder كما هو
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
