<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Http\Filters\PersonFilter;
use App\Support\Traits\Selectable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';
    use HasFactory;
    use Filterable;
    use Selectable;

    protected $guarded = [];

    protected $casts = [
        'dob' => 'date', // أو 'datetime' إذا كنت تخزن الوقت أيضًا
    ];

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = PersonFilter::class;

    // علاقة "شخص" إلى "أفراد الأسرة" (علاقة "hasMany")
    public function familyMembers()
    {
        return $this->hasMany(Person::class, 'relative_id', 'id_num');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function areaResponsible()
    {
        return $this->belongsTo(User::class, 'area_responsible_id','id');
    }
}
