<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogFormRequest extends Model
{
    protected $guarded = ['id'];

    public function formRequest()
    {
        return $this->belongsTo(FormRequest::class, 'form_id');
    }
}
