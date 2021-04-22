<?php

namespace Database\Seeders;

use App\Models\LanguageTranslation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        LanguageTranslation::create([
            'group' => 'test',
            'key' => 'required',
            'text' => ['en' => 'This is a required field', 'nl' => 'Dit is een verplicht veld'],
        ]);
        // \App\Models\User::factory(10)->create();
    }
}
