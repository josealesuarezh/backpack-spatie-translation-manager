<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TranslationRequest;
use App\Models\Language;
use App\Models\Translation;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TranslationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TranslationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Translation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/translation');
        CRUD::setEntityNameStrings('translation', 'translations');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        $this->crud->addColumn(
            [
                'name'         => 'language', // name of relationship method in the model
                'type'         => 'relationship',
                'label'        => 'Language',
                'attribute'    => 'language'
            ]
        );
        $group = Translation::pluck('group')->unique()->values()->toArray();
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

        CRUD::column('group');
        CRUD::column('key');
        CRUD::column('value');
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
        CRUD::setValidation(TranslationRequest::class);

        $this->crud->addField(
            [
                'label'     => "Language",
                'type'      => 'select',
                'name'      => 'language_id',
                'entity'    => 'language',
                'attribute' => 'language',
            ]
        );
        CRUD::field('group');
        CRUD::field('key');
        CRUD::field('value');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
