<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleMenu extends Pivot
{
    use SoftDeletes;

    protected $table = 'role_menus';
    
    protected $guarded = ['id'];
}
