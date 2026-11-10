<?php

namespace App\Http\Filters;

use App\Models\Person;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PersonFilter extends BaseFilters
{
    protected $tableAlias = ''; // خاصية جديدة لتخزين اسم الجدول أو الاسم المستعار
    public $skipHeadMapping = false; // خاصية جديدة لتخطي تحويل الرقم للهيكل الأبوي

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
        'child_dob_from',
        'child_dob_to',
        'child_dob',
        'child_age_months_from',
        'child_age_months_to',
        'child_id_num',
        'family_members_min',
        'family_members_max',
        'has_condition',
        'area_responsibles',
        'block',
        'first_name',
        'father_name',
        'grandfather_name',
        'family_name',
        'filter_file_key'
    ];

    protected function getColumnName(string $column): string
    {
        return $this->tableAlias . $column;
    }

    protected function filterFileKey($value)
    {
        $ids = \Illuminate\Support\Facades\Cache::get($value);

        if ($ids && is_array($ids)) {
             // Reuse the logic from idNum for filtering
             return $this->applyIdFilter($ids);
        }

        return $this->builder;
    }

    protected function applyIdFilter(array $ids)
    {
        $notFoundIds = [];
        $familyHeadIds = [];

        $cleanedIds = [];
        foreach ($ids as $id) {
            $trimmedId = trim($id);
            if (!empty($trimmedId)) {
                $cleanedIds[] = $trimmedId;
            }
        }
        $cleanedIds = array_unique($cleanedIds);

        if (empty($cleanedIds)) {
            return $this->builder;
        }

        if ($this->skipHeadMapping) {
            $familyHeadIds = $cleanedIds;
        } else {
            // Fetch all matching persons in one query to avoid N+1 queries loop
            $persons = Person::whereIn('id_num', $cleanedIds)->get(['id_num', 'relative_id']);
            $foundIds = $persons->pluck('id_num')->toArray();

            foreach ($persons as $person) {
                if (is_null($person->relative_id)) {
                    $familyHeadIds[] = $person->id_num;
                } else {
                    $familyHeadIds[] = $person->relative_id;
                }
            }
            $notFoundIds = array_values(array_diff($cleanedIds, $foundIds));
        }

        $familyHeadIds = array_unique($familyHeadIds);

        // Append to existing session notFoundIds if any
        $existingNotFound = session('notFoundIds', []);
        session(['notFoundIds' => array_merge($existingNotFound, $notFoundIds)]);

        if (!empty($familyHeadIds)) {
             // Use whereIn with existing constraints if builder already has some
             // Warning: multiple whereIn calls are ANDed.
             // But valid use case: filtering by ID list usually implies narrowing down.
            return $this->builder->whereIn($this->getColumnName('id_num'), $familyHeadIds);
        }
        
        // If IDs were provided but none were found in Person table, 
        // return an empty query (so results from incomplete_people can show up cleanly below)
        return $this->builder->whereRaw('0 = 1');
    }

    protected function idNum($value)
    {
        if (!empty(trim($value))) {
            $ids = array_filter(
                preg_split("/\r\n|\n|\r/", $value), 
                fn($id) => !empty(trim($id))
            );
            return $this->applyIdFilter($ids);
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

    /**
     * فلترة حسب تاريخ ميلاد الطفل (بداية)
     */
    protected function childDobFrom($value)
    {
        if (!empty($value)) {
            return $this->builder->whereHas('familyMembers', function ($query) use ($value) {
                $query->whereDate('dob', '>=', $value);
            });
        }
        return $this->builder;
    }

    /**
     * فلترة حسب تاريخ ميلاد الطفل (نهاية)
     */
    protected function childDobTo($value)
    {
        if (!empty($value)) {
            return $this->builder->whereHas('familyMembers', function ($query) use ($value) {
                $query->whereDate('dob', '<=', $value);
            });
        }
        return $this->builder;
    }

    /**
     * فلترة حسب تاريخ ميلاد الطفل (مطابق)
     */
    protected function childDob($value)
    {
        if (!empty($value)) {
            return $this->builder->whereHas('familyMembers', function ($query) use ($value) {
                $query->where('dob', 'like', $value . '%');
            });
        }
        return $this->builder;
    }

    /**
     * فلترة حسب عمر الطفل بالأشهر (من)
     */
    protected function childAgeMonthsFrom($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->whereHas('familyMembers', function ($query) use ($value) {
                $query->whereRaw('TIMESTAMPDIFF(MONTH, dob, CURDATE()) >= ?', [$value]);
            });
        }
        return $this->builder;
    }

    /**
     * فلترة حسب عمر الطفل بالأشهر (إلى)
     */
    protected function childAgeMonthsTo($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->whereHas('familyMembers', function ($query) use ($value) {
                $query->whereRaw('TIMESTAMPDIFF(MONTH, dob, CURDATE()) <= ?', [$value]);
            });
        }
        return $this->builder;
    }

    /**
     * فلترة حسب رقم هوية الطفل
     */
    protected function childIdNum($value)
    {
        if (!empty($value)) {
            return $this->builder->whereHas('familyMembers', function ($query) use ($value) {
                $query->where('id_num', 'like', "%{$value}%");
            });
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