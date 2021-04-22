<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    protected $fillable = [
        'language',
        'name'
    ];


}
