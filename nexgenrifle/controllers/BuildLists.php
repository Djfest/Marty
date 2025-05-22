<?php namespace Marty\NexGenRifle\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class BuildLists extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'marty.nexgenrifle.manage_builds'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Marty.NexGenRifle', 'nexgenrifle', 'buildlists');
    }

    public function index()
    {
        $this->vars['currentUser'] = \BackendAuth::getUser();
        $this->asExtension('ListController')->index();
    }

    public function create()
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->create();
    }

    public function update($recordId = null)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->update($recordId);
    }

    public function listExtendQuery($query)
    {
        // Add any default sorting or filtering
        $query->orderBy('created_at', 'desc');
    }

    public function formExtendQuery($query)
    {
        // Load any necessary relations
        $query->with(['list_items', 'user']);
    }

    public function formExtendFields($form)
    {
        // Customize fields based on list type
        $listType = post('list_type') ?? $form->model->list_type ?? null;

        if ($listType === 'rifle_build') {
            $form->addFields([
                'config[compatibility]' => [
                    'label' => 'Compatibility Settings',
                    'type' => 'nestedform',
                    'form' => [
                        'fields' => [
                            'platform' => [
                                'label' => 'Platform',
                                'type' => 'dropdown',
                                'options' => [
                                    'ar15' => 'AR-15',
                                    'ar10' => 'AR-10',
                                    'custom' => 'Custom'
                                ]
                            ],
                            'caliber' => [
                                'label' => 'Caliber',
                                'type' => 'text'
                            ]
                        ]
                    ]
                ]
            ]);
        }
    }

    public function onLoadListTypeFields()
    {
        $listType = post('list_type');
        $config = [];

        switch ($listType) {
            case 'rifle_build':
                $config = [
                    'notifications' => [
                        'price_alerts' => true,
                        'availability_alerts' => true,
                        'compatibility_warnings' => true
                    ],
                    'display' => [
                        'show_prices' => true,
                        'show_vendors' => true,
                        'show_compatibility' => true
                    ]
                ];
                break;

            case 'gift_list':
                $config = [
                    'notifications' => [
                        'price_alerts' => true,
                        'approaching_target' => true
                    ],
                    'display' => [
                        'show_prices' => false,
                        'show_progress' => true
                    ]
                ];
                break;

            case 'project_list':
                $config = [
                    'notifications' => [
                        'price_alerts' => true,
                        'stock_alerts' => true
                    ],
                    'sorting' => [
                        'grouping' => 'vendor'
                    ]
                ];
                break;
        }

        $this->vars['config'] = $config;
        return [
            '#Form-field-BuildList-config' => $this->makePartial('config_fields')
        ];
    }

    public function onUpdateTotals()
    {
        $list = $this->formFindModelObject(post('id'));
        $list->updateTotal();

        return [
            '#Form-field-BuildList-current_total' => $list->current_total,
            '#Form-field-BuildList-remaining_budget' => $list->remaining_budget
        ];
    }
}
