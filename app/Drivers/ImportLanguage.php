<?php


namespace App\Drivers;


use App\Models\Language;
use App\Models\Translation;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class ImportLanguage
{
    private $disk;
    private $languageFilesPath;
    private $sourceLanguage;

    public function __construct()
    {
        $this->disk = new Filesystem();
        $this->languageFilesPath = resource_path('lang');
        $this->sourceLanguage = App::getLocale();
    }

    public  function importLanguagesFromFiles(){
        $languages = $this->allLanguages();
        foreach ($languages as $language){
            Language::firstOrCreate([
                'language' => $language,
            ]);
        }
    }
    public function importTranslationsFromFiles(){
        $languageTranslations = $this->allTranslations();
        foreach ($languageTranslations as $language => $groups){
            $languageId = Language::where('language',$language)->first()->id;
            foreach ($groups['group'] as $group => $translation){
                foreach ($translation as $key => $value){

                    try{
                        Translation::firstOrCreate([
                            'language_id' => $languageId,
                            'group'  => $group,
                            'key' => $key,
                            'value' => is_array($value) ? implode(',',$value):$value
                        ]);
                    }catch (\ErrorException $e){
                        dd($languageId,$group,$key,$value);
                    }

                }
            }
        }
    }
    public function allTranslations()
    {
        return $this->allLanguages()->mapWithKeys(function ($language) {
            return [$language => $this->allTranslationsFor($language)];
        });
    }
    public function filterTranslationsFor($language, $filter)
    {
        $allTranslations = $this->getSourceLanguageTranslationsWith(($language));

        if (! $filter) {
            return $allTranslations;
        }

        return $allTranslations->map(function ($groups, $type) use ($language, $filter) {
            return $groups->map(function ($keys, $group) use ($language, $filter, $type) {
                return collect($keys)->filter(function ($translations, $key) use ($group, $language, $filter, $type) {
                    return strs_contain([$group, $key, $translations[$language], $translations[$this->sourceLanguage]], $filter);
                });
            })->filter(function ($keys) {
                return $keys->isNotEmpty();
            });
        });
    }
    public function getSourceLanguageTranslationsWith($language)
    {
        $sourceTranslations = $this->allTranslationsFor($this->sourceLanguage);

        $languageTranslations = $this->allTranslationsFor($language);

        return $sourceTranslations->map(function ($groups, $type) use ($language, $languageTranslations) {
            return $groups->map(function ($translations, $group) use ($type, $language, $languageTranslations) {
                $translations = $translations->toArray();
                array_walk($translations, function (&$value, &$key) use ($type, $group, $language, $languageTranslations) {
                    $value = [
                        $this->sourceLanguage => $value,
                        $language => $languageTranslations->get($type, collect())->get($group, collect())->get($key),
                    ];
                });

                return $translations;
            });
        });
    }
    public function allTranslationsFor($language)
    {
        return Collection::make([
            'group' => $this->getGroupTranslationsFor($language),
        ]);
    }
    public function getGroupTranslationsFor($language)
    {
        return $this->getGroupFilesFor($language)->mapWithKeys(function ($group) {
            // here we check if the path contains 'vendor' as these will be the
            // files which need namespacing
            if (Str::contains($group->getPathname(), 'vendor')) {
                $vendor = Str::before(Str::after($group->getPathname(), 'vendor'.DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);

                return ["{$vendor}::{$group->getBasename('.php')}" => new Collection(Arr::dot($this->disk->getRequire($group->getPathname())))];
            }
            return [$group->getBasename('.php') => new Collection(Arr::dot($this->disk->getRequire($group->getPathname())))];

        });
    }
    public function getGroupFilesFor($language)
    {
        $groups = new Collection($this->disk->allFiles("{$this->languageFilesPath}".DIRECTORY_SEPARATOR."{$language}"));
        // namespaced files reside in the vendor directory so we'll grab these
        // the `getVendorGroupFileFor` method
        $groups = $groups->merge($this->getVendorGroupFilesFor($language));

        return $groups;
    }
    public function getVendorGroupFilesFor($language)
    {
        if (! $this->disk->exists("{$this->languageFilesPath}".DIRECTORY_SEPARATOR.'vendor')) {
            return;
        }

        $vendorGroups = [];
        foreach ($this->disk->directories("{$this->languageFilesPath}".DIRECTORY_SEPARATOR.'vendor') as $vendor) {
            $vendor = Arr::last(explode(DIRECTORY_SEPARATOR, $vendor));
            if (! $this->disk->exists("{$this->languageFilesPath}".DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR."{$vendor}".DIRECTORY_SEPARATOR."{$language}")) {
                array_push($vendorGroups, []);
            } else {
                array_push($vendorGroups, $this->disk->allFiles("{$this->languageFilesPath}".DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR."{$vendor}".DIRECTORY_SEPARATOR."{$language}"));
            }
        }

        return new Collection(Arr::flatten($vendorGroups));
    }

    public function allLanguages(){
        $disk = new Filesystem();
        // As per the docs, there should be a subdirectory within the
        // languages path so we can return these directory names as a collection
        $directories = Collection::make($disk->directories(resource_path('lang')));

        return $directories->mapWithKeys(function ($directory) {
            $language = basename($directory);
            return [$language => $language];
        })->filter(function ($language) {
            // at the moemnt, we're not supporting vendor specific translations
            return $language != 'vendor';
        });
    }
}
