<?php
namespace Marty\AiSolutions;

use Backend;
use System\Classes\PluginBase;
use Event;
use Marty\Nexgenrifle\Models\ProfileType;
use Marty\AiSolutions\Models\AiKnowledgeBase;

/**
 * AiSolutions Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     */
    public function pluginDetails()
    {
        return [
            'name' => 'AI Solutions',
            'description' => 'Central hub for managing AI-based communication across your application',
            'author' => 'Marty',
            'icon' => 'icon-brain'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
    }

    /**
     * Registers backend permissions for this plugin.
     */
    public function registerPermissions()
    {
        return [
            'marty.aisolutions.access_sessions' => [
                'label' => 'View and manage AI sessions',
                'tab' => 'AI Solutions',
                'roles' => ['developer']
            ],
            'marty.aisolutions.access_messages' => [
                'label' => 'View and post AI messages',
                'tab' => 'AI Solutions',
                'roles' => ['developer']
            ],
            'marty.aisolutions.access_modelchanges' => [
                'label' => 'View model changes',
                'tab' => 'AI Solutions',
                'roles' => ['developer']
            ],
            'marty.aisolutions.access_modelgraphs' => [
                'label' => 'View model graphs',
                'tab' => 'AI Solutions',
                'roles' => ['developer']
            ],
            'marty.aisolutions.access_profiletypes' => [
                'label' => 'Manage profile type extensions',
                'tab' => 'AI Solutions',
                'roles' => ['developer']
            ],
            'marty.aisolutions.access_sync' => [
                'label' => 'Access model sync tools',
                'tab' => 'AI Solutions',
                'roles' => ['developer']
            ],
            'marty.aisolutions.access_knowledgebase' => [
                'label' => 'Access knowledge base',
                'tab' => 'AI Solutions',
                'roles' => ['developer']
            ]
        ];
    }

    /**
     * Registers any frontend components implemented in this plugin.
     */
    public function registerComponents()
    {
        return [
            'Marty\AiSolutions\Components\AiConsole' => 'aiConsole'
        ];
    }

    /**
     * Boot method, called right before the request route.
     */
    public function boot()
    {
        // Extend ProfileType model
        ProfileType::extend(function ($model) {
            // Initialize metadata with ai_enabled if not set
            $initAiEnabled = function () use ($model) {
                if (!isset($model->metadata['ai_enabled'])) {
                    $metadata = $model->metadata ?? [];
                    $metadata['ai_enabled'] = false;
                    $model->metadata = $metadata;
                }
            };

            // Ensure ai_enabled exists for both new and existing records
            $model->bindEvent('model.beforeCreate', $initAiEnabled);
            $model->bindEvent('model.beforeFetch', $initAiEnabled);

            // Add dynamic method for ai_enabled scope
            $model->addDynamicMethod('scopeAiEnabled', function ($query) {
                return $query->whereJsonContains('metadata->ai_enabled', true);
            });
        });

        // Extend ProfileType form
        Event::listen('backend.form.extendFields', function ($widget) {
            if (!$widget->getController() instanceof \Marty\Nexgenrifle\Controllers\ProfileTypes) {
                return;
            }

            if (!$widget->model instanceof \Marty\Nexgenrifle\Models\ProfileType) {
                return;
            }

            $widget->addTabFields([
                'metadata[ai_enabled]' => [
                    'label' => 'AI Enabled',
                    'tab' => 'AI',
                    'type' => 'switch',
                    'span' => 'auto',
                    'default' => false,
                    'comment' => 'Enable this model for AI analysis and interaction'
                ]
            ]);
        });
        // Add the profile_type relation to the AiKnowledgeBase model
        AiKnowledgeBase::extend(function ($model) {
            $model->belongsTo['profile_type'] = ['Marty\Nexgenrifle\Models\ProfileType'];
        });

    }

    /**
     * Registers backend navigation items for this plugin.
     */
    public function registerNavigation()
    {
        return [
            'aisolutions' => [
                'label' => 'AI Solutions',
                'url' => Backend::url('marty/aisolutions/sessions'),
                'icon' => 'icon-brain',
                'permissions' => ['marty.aisolutions.access_sessions'],
                'order' => 500,
                'sideMenu' => [
                    'sessions' => [
                        'label' => 'Sessions',
                        'url' => Backend::url('marty/aisolutions/sessions'),
                        'icon' => 'icon-comments',
                        'permissions' => ['marty.aisolutions.access_sessions']
                    ],
                    'messages' => [
                        'label' => 'Messages',
                        'url' => Backend::url('marty/aisolutions/messages'),
                        'icon' => 'icon-comment',
                        'permissions' => ['marty.aisolutions.access_messages']
                    ],
                    'modelchanges' => [
                        'label' => 'Model Changes',
                        'url' => Backend::url('marty/aisolutions/modelchanges'),
                        'icon' => 'icon-history',
                        'permissions' => ['marty.aisolutions.access_modelchanges']
                    ],
                    'modelgraphs' => [
                        'label' => 'Model Graphs',
                        'url' => Backend::url('marty/aisolutions/modelgraphs'),
                        'icon' => 'icon-sitemap',
                        'permissions' => ['marty.aisolutions.access_modelgraphs']
                    ],
                    'profiletypes' => [
                        'label' => 'Profile Extensions',
                        'url' => Backend::url('marty/aisolutions/profiletypeextensions'),
                        'icon' => 'icon-user',
                        'permissions' => ['marty.aisolutions.access_profiletypes']
                    ],
                    'knowledgebase' => [
                        'label' => 'Knowledge Base',
                        'icon' => 'icon-book',
                        'url' => Backend::url('marty/aisolutions/aiknowledgebases'),
                        'permissions' => ['marty.aisolutions.access_knowledgebase']
                    ],
                    'sync' => [
                        'label' => 'Model Sync',
                        'url' => Backend::url('marty/aisolutions/sync'),
                        'icon' => 'icon-refresh',
                        'permissions' => ['marty.aisolutions.access_sync']
                    ]
                ]
            ],
        ];
    }
}
