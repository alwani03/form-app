<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterMenu extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function menus()
    {
        return $this->hasMany(Menu::class, 'master_menu_id');
    }
}
