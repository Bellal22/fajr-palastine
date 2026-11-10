<?php

namespace App\Models;

use App\Support\Traits\Selectable;
use App\Http\Filters\ComplaintFilter;
use App\Http\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Complaint extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = ComplaintFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_num',
        'complaint_type_id',
        'complaint_title',
        'complaint_text',
        'admin_complaint_title',
        'admin_complaint_text',
        'response_title',
        'response',
        'responded_at',
        'responded_by',
        'status',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function complaintType()
    {
        return $this->belongsTo(ComplaintType::class, 'complaint_type_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'id_num', 'id_num');
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}
