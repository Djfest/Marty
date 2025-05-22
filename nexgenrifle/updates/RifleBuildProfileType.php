<?php namespace Marty\NexGenRifle\Updates;
use Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Marty\NexGenRifle\Models\ProfileType;
use Marty\NexGenRifle\Models\RifleBuild;
use Marty\NexGenRifle\Models\RifleItem;
use Marty\NexGenRifle\Models\BuildCategory; // Keep this if BuildCategory is used directly in this file


class RifleBuildProfileType extends Seeder
{
    public function run()
    {
        ProfileType::create([
            'name' => 'Rifle Build',
            'model_class' => RifleBuild::class,
            'type' => 'build',
            'description' => 'Custom rifle build configuration with parts and pricing',
            'required_fields' => json_encode([
                'title',
                'user_id',
                'build_category_id'
            ]),
            'fillable_fields' => json_encode([
                'title',
                'status',
                'user_id',
                'build_category_id',
                'total_cost',
                'notes',
                'metadata',
                'uuid',
                'purchase_state',
                'stock_state',
                'config'
            ]),
            'api_methods' => json_encode([
                'GET' => 'Retrieve build details',
                'POST' => 'Create new build',
                'PUT' => 'Update build information',
                'DELETE' => 'Archive or delete build',
                'BATCH' => [
                    'description' => 'Process multiple build operations',
                    'operations' => [
                        'add_items' => 'Add parts to build',
                        'remove_items' => 'Remove parts from build',
                        'update_items' => 'Update parts in build',
                        'calculate_total' => 'Recalculate build total'
                    ]
                ]
            ]),
            'response_structure' => json_encode([
                'id' => 'integer',
                'uuid' => 'string',
                'title' => 'string',
                'status' => 'string',
                'status_label' => 'string',
                'total_cost' => 'float',
                'total_items' => 'integer',
                'purchase_state' => 'string',
                'stock_state' => 'string',
                'notes' => 'string|null',
                'config' => 'json',
                'metadata' => 'json',
                'created_at' => 'datetime',
                'updated_at' => 'datetime',
                'formatted_created_at' => 'string',
                'formatted_updated_at' => 'string',
                'user' => [
                    'id' => 'integer',
                    'name' => 'string',
                    'email' => 'string'
                ],
                'build_category' => [
                    'id' => 'integer',
                    'name' => 'string',
                    'description' => 'string'
                ],
                'rifle_items' => [
                    'type' => 'array',
                    'items' => [
                        'id' => 'integer',
                        'product' => [
                            'id' => 'integer',
                            'title' => 'string',
                            'price' => 'decimal'
                        ],
                        'quantity' => 'integer',
                        'position' => 'integer'
                    ]
                ]
            ]),
            'relationships' => json_encode([
                'user' => [
                    'type' => 'belongsTo',
                    'model' => 'Backend\Models\User',
                    'description' => 'User who owns this build'
                ],
                'build_category' => [
                    'type' => 'belongsTo',
                    'model' => BuildCategory::class,
                    'description' => 'Category/type of build'
                ],
                'rifle_items' => [
                    'type' => 'hasMany',
                    'model' => RifleItem::class,
                    'description' => 'Parts/products in this build'
                ]
            ]),
            'status_groups' => json_encode([
                'draft' => [
                    'label' => 'Draft',
                    'color' => '#666666',
                    'description' => 'Initial build state'
                ],
                'in_progress' => [
                    'label' => 'In Progress',
                    'color' => '#3498db',
                    'description' => 'Build is being worked on'
                ],
                'completed' => [
                    'label' => 'Completed',
                    'color' => '#27ae60',
                    'description' => 'Build is complete'
                ],
                'archived' => [
                    'label' => 'Archived',
                    'color' => '#95a5a6',
                    'description' => 'Build has been archived'
                ]
            ]),
            'api_help' => json_encode([
                'example_calls' => [
                    [
                        'method' => 'POST',
                        'endpoint' => '/api/rifle-builds',
                        'description' => 'Create new build',
                        'payload' => [
                            'title' => 'Custom AR-15 Build',
                            'build_category_id' => 1,
                            'user_id' => 1,
                            'notes' => 'New precision build project'
                        ]
                    ],
                    [
                        'method' => 'POST',
                        'endpoint' => '/api/batch',
                        'description' => 'Add items to build',
                        'payload' => [
                            'operations' => [
                                [
                                    'method' => 'POST',
                                    'resource' => 'rifle-items',
                                    'data' => [
                                        'rifle_build_id' => 123,
                                        'product_id' => 456,
                                        'quantity' => 1,
                                        'position' => 1
                                    ]
                                ]
                            ],
                            'use_transaction' => true
                        ]
                    ]
                ]
            ]),
            'error_guidance' => json_encode([
                'title_required' => 'Please provide a title for the build',
                'invalid_category' => 'Please select a valid build category',
                'invalid_product' => 'The selected product is not compatible with this build type',
                'duplicate_product' => 'This product is already in the build'
            ]),
            'conversation_analysis' => json_encode([
                'goals' => [
                    'Help users create and manage rifle builds',
                    'Track parts and costs',
                    'Ensure build compatibility'
                ],
                'keywords' => [
                    'build', 'rifle', 'parts', 'cost',
                    'category', 'compatibility', 'status'
                ],
                'common_queries' => [
                    'How to start a new build',
                    'Adding parts to build',
                    'Calculating total cost',
                    'Checking compatibility'
                ]
            ])
        ]);
    }
}
