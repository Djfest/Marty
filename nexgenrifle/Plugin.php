<?php namespace Marty\NexGenRifle;

use Backend;
use System\Classes\PluginBase;
use RainLab\User\Models\User as UserModel;
use Marty\NexGenRifle\Models\UserProfile as UserProfileModel; // Your UserProfile model

class Plugin extends PluginBase
{
    public $require = ['RainLab.User'];
    public function pluginDetails()
    {
        return [
            'name'        => 'NexGen Rifle Builder',
            'description' => 'Advanced rifle customization and building platform',
            'author'      => 'Marty',
            'icon'        => 'icon-wrench'
        ];
    }

    public function registerComponents()
    {
        return [
            'Marty\NexGenRifle\Components\BuilderUI' => 'builderUI'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
        // Extend RainLab User model to include relationship to profile
        UserModel::extend(function($model) {
            $model->hasOne['profile'] = [
                'Marty\NexGenRifle\Models\UserProfile',
                'key' => 'user_id',
                'delete' => true
            ];
            
            // Add event handler to create profile when new user is created
            $model->bindEvent('model.afterCreate', function() use ($model) {
                $profile = new UserProfile();
                $profile->user_id = $model->id;
                $profile->save();
            });
        });
        
        // Register middleware with correct namespace capitalization
        $this->app['router']->aliasMiddleware('nexgenrifle.auth', \Marty\NexGenRifle\Middleware\Authenticate::class);
    }

    /**
     * Boot method, called right before the request route.
     */
    public function boot()
    {
        // Extend RainLab User Model
        UserModel::extend(function($model) {
            $model->hasOne['userprofile'] = [
                UserProfileModel::class,
                'key'      => 'user_id', // Foreign key in your marty_nexgenrifle_user_profiles table
                'otherKey' => 'id',      // Primary key in the users table
                // 'delete'   => true   // Optional: if you want to delete the profile when the user is deleted
            ];

            // You can also add a scope for convenience if needed
            // $model->addDynamicMethod('scopeWithProfile', function ($query) {
            //     return $query->with('userprofile');
            // });
        });

        // Optional: Automatically create a profile when a new user is created
        UserModel::created(function ($user) {
            if (!$user->userprofile) {
                UserProfileModel::create(['user_id' => $user->id]);
            }
        });

        // Extend RainLab User Controller Forms
        \RainLab\User\Controllers\Users::extendFormFields(function($form, $model, $context) {
            // Ensure this is applied only to the User model and not other models
            if (!$model instanceof UserModel) {
                return;
            }

            // Ensure the userprofile relation exists, especially for existing users
            // The UserModel::created event above handles new users.
            if ($context === 'update' && !$model->userprofile) {
                 UserProfileModel::create(['user_id' => $model->id]);
                 $model->reload(); // Reload to get the newly created userprofile relation
            }

            // Add fields to a new tab or an existing one
            // Replace 'your_profile_field_1', etc., with actual field names from your UserProfile model
            // and ensure they are defined in UserProfile's fields.yaml if you use complex types.
            $form->addTabFields([
                'userprofile[some_profile_text_field]' => [
                    'label'   => 'Some Profile Text',
                    'tab'     => 'Profile Details', // Name of the new or existing tab
                    'type'    => 'text', // Or any other valid form field type
                    // 'disabled' => !$model->userprofile, // Could be useful if profile creation is not automatic
                ],
                'userprofile[another_profile_setting]' => [
                    'label'   => 'Another Profile Setting',
                    'tab'     => 'Profile Details',
                    'type'    => 'switch',
                ],
                // ... add more fields from your UserProfile model as needed
                // Example: 'userprofile[bio]' => ['label' => 'Biography', 'tab' => 'Profile Details', 'type' => 'textarea'],
            ]);
        });

        \Route::group([
            'prefix' => 'api/v1',  // Update prefix to match the OpenAPI spec
            'middleware' => [
                'web',
                'api',
                \Marty\NexGenRifle\Middleware\Authenticate::class
            ]
        ], function () {
            \Route::post('batch', 'Marty\NexGenRifle\Controllers\Api@batch');
            \Route::get('build-lists', 'Marty\NexGenRifle\Controllers\Api@indexBuildLists');
            \Route::get('build-lists/{id}', 'Marty\NexGenRifle\Controllers\Api@showBuildList');
            \Route::post('build-lists', 'Marty\NexGenRifle\Controllers\Api@storeBuildList');
            \Route::put('build-lists/{id}', 'Marty\NexGenRifle\Controllers\Api@updateBuildList');
            \Route::delete('build-lists/{id}', 'Marty\NexGenRifle\Controllers\Api@destroyBuildList');
            
            // Profile type metadata routes
            \Route::get('profile-types', 'Marty\NexGenRifle\Controllers\Api@getProfileTypes');
            \Route::get('profile-types/{resource}', 'Marty\NexGenRifle\Controllers\Api@getProfileType');
            
            // General resource routes
            \Route::get('{resource}', 'Marty\NexGenRifle\Controllers\Api@index');
            \Route::get('{resource}/{id}', 'Marty\NexGenRifle\Controllers\Api@show');
            \Route::post('{resource}', 'Marty\NexGenRifle\Controllers\Api@store');
            \Route::put('{resource}/{id}', 'Marty\NexGenRifle\Controllers\Api@update');
            \Route::delete('{resource}/{id}', 'Marty\NexGenRifle\Controllers\Api@destroy');
        });

        // Register middleware aliases
        $this->app['router']->aliasMiddleware('auth.nexgen', \Marty\NexGenRifle\Middleware\Authenticate::class);
    }

    public function registerPermissions()
    {
        return [
            'marty.nexgenrifle.manage_builds' => [
                'label' => 'Manage Rifle Builds',
                'tab' => 'NexGen Rifle Builder'
            ],
            'marty.nexgenrifle.manage_catalog' => [
                'label' => 'Manage Product Catalog',
                'tab' => 'NexGen Rifle Builder'
            ],
            'marty.nexgenrifle.manage_profiles' => [
                'label' => 'Manage User Profiles',
                'tab' => 'NexGen Rifle Builder'
            ],
            'marty.nexgenrifle.manage_suppliers' => [
                'label' => 'Manage Suppliers',
                'tab' => 'NexGen Rifle Builder'
            ],
            'marty.nexgenrifle.manage_categories' => [
                'label' => 'Manage Categories',
                'tab' => 'NexGen Rifle Builder'
            ],
            'marty.nexgenrifle.access_settings' => [
                'label' => 'Manage Plugin Settings',
                'tab' => 'NexGen Rifle Builder'
            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'NexGen Rifle Builder',
                'description' => 'Manage builder configuration and settings.',
                'category'    => 'NexGen Rifle Builder',
                'icon'        => 'icon-wrench',
                'class'       => 'Marty\NexGenRifle\Models\Settings',
                'order'       => 500,
                'keywords'    => 'rifle builder custom configuration',
                'permissions' => ['marty.nexgenrifle.access_settings']
            ]
        ];
    }

    public function registerNavigation()
    {
        return [
            'nexgenrifle' => [
                'label'       => 'Rifle Builder',
                'url'         => Backend::url('marty/nexgenrifle/riflebuilds'),
                'icon'        => 'icon-wrench',
                'permissions' => ['marty.nexgenrifle.*'],
                'order'       => 500,

                'sideMenu' => [
                    'buildlists' => [
                        'label'       => 'Build Lists',
                        'icon'        => 'icon-list',
                        'url'         => Backend::url('marty/nexgenrifle/buildlists'),
                        'permissions' => ['marty.nexgenrifle.manage_builds']
                    ],
                    'riflebuilds' => [
                        'label'       => 'Rifle Builds',
                        'icon'        => 'icon-crosshairs',
                        'url'         => Backend::url('marty/nexgenrifle/riflebuilds'),
                        'permissions' => ['marty.nexgenrifle.manage_builds']
                    ],
                    'products' => [
                        'label'       => 'Products',
                        'icon'        => 'icon-cube',
                        'url'         => Backend::url('marty/nexgenrifle/products'),
                        'permissions' => ['marty.nexgenrifle.manage_catalog']
                    ],
                    'categories' => [
                        'label'       => 'Categories',
                        'icon'        => 'icon-sitemap',
                        'url'         => Backend::url('marty/nexgenrifle/categories'),
                        'permissions' => ['marty.nexgenrifle.manage_categories']
                    ],
                    'suppliers' => [
                        'label'       => 'Suppliers',
                        'icon'        => 'icon-truck',
                        'url'         => Backend::url('marty/nexgenrifle/suppliers'),
                        'permissions' => ['marty.nexgenrifle.manage_suppliers']
                    ],
                    'profiles' => [
                        'label'       => 'User Profiles',
                        'icon'        => 'icon-users',
                        'url'         => Backend::url('marty/nexgenrifle/profiles'),
                        'permissions' => ['marty.nexgenrifle.manage_profiles']
                    ]
                ]
            ]
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'marty.nexgenrifle::mail.build_saved' => 'Notification when a build is saved',
            'marty.nexgenrifle::mail.build_completed' => 'Notification when a build is completed',
            'marty.nexgenrifle::mail.build_status_changed' => 'Notification when build status changes'
        ];
    }

}
