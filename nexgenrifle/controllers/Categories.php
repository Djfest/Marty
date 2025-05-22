<?php namespace Marty\NexGenRifle\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Behaviors\RelationController;

class Categories extends Controller
{
    /**
     * @var array Behaviors implemented by this controller
     */
    public $implement = [
        FormController::class,
        ListController::class,
        RelationController::class
    ];

    /**
     * @var string Configuration file for the form behavior
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the list behavior
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var string Configuration file for the relation behavior
     */
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Marty.NexGenRifle', 'nexgenrifle', 'categories');
    }

    public function preview($recordId = null)
    {
        return $this->asExtension('FormController')->preview($recordId);
    }
}
