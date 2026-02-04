<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menus', 'menu_id', 'role_id');
    }
}
