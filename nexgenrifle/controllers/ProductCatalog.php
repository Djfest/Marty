<?php namespace NexgenRifle\NexgenRifle\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Product Catalog Backend Controller
 *
 * @link https://docs.octobercms.com/3.x/extend/system/controllers.html
 */
class ProductCatalog extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var array required permissions
     */
    public $requiredPermissions = ['nexgenrifle.nexgenrifle.productcatalog'];

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('NexgenRifle.NexgenRifle', 'nexgenrifle', 'productcatalog');
    }
}
