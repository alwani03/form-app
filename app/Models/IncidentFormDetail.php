<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentFormDetail extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'incident_date' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
