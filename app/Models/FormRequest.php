<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormRequest extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'type' => 'array',
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentTypeConfig::class, 'document_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(LogFormRequest::class, 'form_id');
    }
}
