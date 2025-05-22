<?php namespace Marty\NexGenRifle\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Profiles extends Controller
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

    /**
     * @var array Required permissions for this controller.
     */
    public $requiredPermissions = [
        'marty.nexgenrifle.manage_profiles' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Marty.NexGenRifle', 'nexgenrifle', 'profiles');
    }
}
