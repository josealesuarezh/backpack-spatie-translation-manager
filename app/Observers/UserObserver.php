<?php

namespace App\Observers;

use App\Models\LanguageTranslation;

class UserObserver
{
    /**
     * Handle the LanguageTranslation "created" event.
     *
     * @param  \App\Models\LanguageTranslation  $languageTranslation
     * @return void
     */
    public function created(LanguageTranslation $languageTranslation)
    {
        $languageTranslation->flushGroupCache();
    }

    /**
     * Handle the LanguageTranslation "updated" event.
     *
     * @param  \App\Models\LanguageTranslation  $languageTranslation
     * @return void
     */
    public function updated(LanguageTranslation $languageTranslation)
    {
        $languageTranslation->flushGroupCache();
    }

    /**
     * Handle the LanguageTranslation "deleted" event.
     *
     * @param  \App\Models\LanguageTranslation  $languageTranslation
     * @return void
     */
    public function deleted(LanguageTranslation $languageTranslation)
    {
        $languageTranslation->flushGroupCache();
    }

    /**
     * Handle the LanguageTranslation "restored" event.
     *
     * @param  \App\Models\LanguageTranslation  $languageTranslation
     * @return void
     */
    public function restored(LanguageTranslation $languageTranslation)
    {
        $languageTranslation->flushGroupCache();
    }

    /**
     * Handle the LanguageTranslation "force deleted" event.
     *
     * @param  \App\Models\LanguageTranslation  $languageTranslation
     * @return void
     */
    public function forceDeleted(LanguageTranslation $languageTranslation)
    {
        $languageTranslation->flushGroupCache();
    }
}
