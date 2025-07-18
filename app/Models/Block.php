<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\BlockFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blocks'; // تحديد اسم الجدول صراحةً

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = BlockFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'area_responsible_id',
        'title',
        'phone',
        'limit_num',
        'lan',
        'lat',
        'note',
    ];

    /**
     * Get the area responsible that owns the block.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function areaResponsible()
    {
        return $this->belongsTo(Supervisor::class,'area_responsible_id');
    }
}
