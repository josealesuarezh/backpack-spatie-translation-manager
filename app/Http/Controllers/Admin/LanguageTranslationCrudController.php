<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LanguageTranslationCreate;
use App\Http\Requests\LanguageTranslationRequest;
use App\Http\Requests\LanguageTranslationUpdate;
use App\Models\Language;
use App\Models\LanguageTranslation;
use App\Models\Translation;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Spatie\TranslationLoader\LanguageLine;

/**
 * Class LanguageTranslationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LanguageTranslationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\LanguageTranslation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/languagetranslation');
        CRUD::setEntityNameStrings('Language Translations', 'Language Translations');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumn('group');
        $this->crud->addColumn('key');
        $this->crud->addColumn([
            'name' => 'localLanguage', // the db column name (attribute name)
            'label' => "Local Translation", // the human-readable label for it
            'type' => 'text'
        ]);
        $group = LanguageTranslation::pluck('group')->unique()->values()->toArray();
        $group = array_combine($group,array_map('ucwords', $group));
        $this->crud->addFilter([
            'name'  => 'group',
            'type'  => 'dropdown',
            'label' => 'Group'
        ],
        $group,
        function($value) { // if the filter is active
            $this->crud->addClause('where', 'group', $value);
        });

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->crud->addField('group');
        $this->crud->addField('key');
        $languages = Language::pluck('language')->toArray();
        $languages = array_combine($languages,$languages);
        $this->crud->addField([   // select2_from_array
            'name'        => 'language',
            'label'       => "Language",
            'type'        => 'select2_from_array',
            'options'     => $languages,
            'allows_null' => false,
            'default' => app()->getLocale()
        ]);
        $this->crud->addField([
            'name'  => 'text',
            'type'  => 'text',
            'label' => 'Translation',
        ]);
        CRUD::setValidation(LanguageTranslationCreate::class);



        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handleTextInput($this->crud->getRequest()));
        $this->crud->unsetValidation();
        return $this->traitStore();
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->crud->addField('group');
        $this->crud->addField('key');
        $this->crud->addField([   // Text
            'name'  => 'localLanguage',
            'label' => "Local Translation",
            'type'  => 'text',
            'attributes' => [
                'readonly'  => 'readonly',
                'disabled'  => 'disabled',
            ]
        ]);
        $languages = Language::pluck('language')->toArray();
        $languages = array_combine($languages,$languages);
        $this->crud->addField([   // select2_from_array
            'name'        => 'language',
            'label'       => "Language",
            'type'        => 'select2_from_array',
            'options'     => $languages,
            'allows_null' => false,
            'default' => app()->getLocale()
        ]);

        $this->crud->addField(
            [
                'name'  => 'text',
                'label' => 'Language Translation',
                'type'  => 'custom_ajax_input',
            ]
        );
        CRUD::setValidation(LanguageTranslationUpdate::class);
    }

    public function update()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handleTextInput($this->crud->getRequest()));
        $this->crud->unsetValidation();
        $response = $this->traitUpdate();
        return $this->traitUpdate();
    }

    public function getTranslation($id,$lan){
        $translation = LanguageTranslation::find($id);
        if (! isset($translation->text[$lan]))
        {
            return response()->json('');
        }
        return response()->json($translation->text[$lan]);
    }

    protected function handleTextInput($request)
    {
        if ($request->has('id')){
            $text = LanguageTranslation::find($request->id)->text;
            $request->request->set('text', array_merge($text,[$request['language'] =>$request['text']]));
        }else{
            $request->request->set('text', [$request['language'] =>$request['text'] ]);
        }
        $request->request->remove('language');
        return $request;
    }
}
