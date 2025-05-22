<?php namespace Marty\NexGenRifle\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class RifleBuilds extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\RelationController::class
    ];

    /**
     * @var string Configuration file for the form behavior.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the list behavior.
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var string Configuration file for the relation behavior.
     */
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Marty.NexGenRifle', 'nexgenrifle', 'riflebuilds');
    }

    /**
     * Extend the query used for finding the form model.
     */
    public function formExtendQuery($query)
    {
        return $query->with(['rifle_items', 'rifle_items.product']);
    }

    /**
     * Recalculate the total cost when rifle items are updated
     */
    public function onUpdateRifleItems()
    {
        $build = $this->formFindModelObject();
        $build->calculateTotalCost();
        $build->save();

        return $this->refreshRifleItems();
    }

    /**
     * Refresh the rifle items relation partial
     */
    public function refreshRifleItems()
    {
        return [
            '#rifleItemsContainer' => $this->makePartial('rifle_items_container', [
                'formModel' => $this->formGetModel()
            ])
        ];
    }
}
