<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\App;
use Spatie\TranslationLoader\LanguageLine;

class LanguageTranslation extends LanguageLine
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $table = "language_lines";

    protected $fillable = [
        'group',
        'key',
        'text',

    ];

    public function getLocalLanguageAttribute()
    {
       return $this->getTranslation(App::getLocale());
    }


}
