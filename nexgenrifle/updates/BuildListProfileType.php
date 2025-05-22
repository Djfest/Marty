<?php namespace Marty\NexGenRifle\Updates;
use Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Marty\NexGenRifle\Models\ProfileType;
use Marty\NexGenRifle\Models\BuildList;
use Marty\NexGenRifle\Models\BuildListItem;

class BuildListProfileType extends Seeder
{
    public function run()
    {
        // Create BuildList profile type
        ProfileType::create([
            'name' => 'Build List',
            'model_class' => BuildList::class,
            'type' => 'collection',
            'description' => 'Flexible list for tracking items, builds, or gift collections',
            'required_fields' => [
                'name',
                'user_id',
                'list_type'
            ],
            'fillable_fields' => [
                'name',
                'description',
                'list_type',
                'user_id',
                'status',
                'target_date',
                'total_budget',
                'current_total',
                'metadata',
                'config'
            ],
            'api_methods' => [
                'GET' => 'Retrieve list details',
                'POST' => 'Create new list',
                'PUT' => 'Update list information',
                'DELETE' => 'Archive list', 
                'BATCH' => [
                    'description' => 'Process multiple list operations',
                    'operations' => [
                        'add_items' => 'Add items to list',
                        'remove_items' => 'Remove items from list',
                        'update_items' => 'Update items in list',
                        'reorder_items' => 'Change item positions',
                        'update_totals' => 'Recalculate list totals'
                    ]
                ]
            ],
            'response_structure' => [
                'id' => 'integer',
                'uuid' => 'string',
                'name' => 'string',
                'description' => 'string|null',
                'status' => 'string',
                'status_label' => 'string',
                'target_date' => 'datetime|null',
                'formatted_target_date' => 'string|null',
                'total_budget' => 'decimal|null',
                'current_total' => 'decimal',
                'remaining_budget' => 'decimal|null',
                'percentage_complete' => 'integer',
                'days_until_target' => 'integer|null',
                'metadata' => 'json',
                'config' => 'json',
                'created_at' => 'datetime',
                'updated_at' => 'datetime',
                'user' => [
                    'id' => 'integer',
                    'name' => 'string'
                ],
                'list_items' => [
                    'type' => 'array',
                    'items' => [
                        'id' => 'integer',
                        'title' => 'string',
                        'status' => 'string',
                        'price' => 'decimal|null',
                        'quantity' => 'integer',
                        'total_price' => 'decimal|null',
                        'priority' => 'integer',
                        'is_acquired' => 'boolean'
                    ]
                ]
            ],
            'relationships' => [
                'user' => [
                    'type' => 'belongsTo',
                    'model' => 'Backend\Models\User',
                    'description' => 'User who owns this list'
                ],
                'list_items' => [
                    'type' => 'hasMany',
                    'model' => BuildListItem::class,
                    'description' => 'Items in this list'
                ]
            ],
            'status_groups' => [
                'planning' => [
                    'label' => 'Planning',
                    'color' => '#3498db',
                    'description' => 'Initial list planning'
                ],
                'in_progress' => [
                    'label' => 'In Progress',
                    'color' => '#f1c40f',
                    'description' => 'Actively collecting items'
                ],
                'completed' => [
                    'label' => 'Completed',
                    'color' => '#27ae60',
                    'description' => 'All items acquired/complete'
                ],
                'on_hold' => [
                    'label' => 'On Hold',
                    'color' => '#e67e22',
                    'description' => 'List temporarily paused'
                ],
                'archived' => [
                    'label' => 'Archived',
                    'color' => '#95a5a6',
                    'description' => 'List no longer active'
                ]
            ],
            'conversation_analysis' => [
                'goals' => [
                    'Track items to acquire',
                    'Manage budget and costs',
                    'Monitor deadlines',
                    'Organize items by priority'
                ],
                'keywords' => [
                    'list', 'items', 'budget', 'deadline',
                    'priority', 'status', 'progress', 'target'
                ],
                'common_queries' => [
                    'Creating a new list',
                    'Adding items to list',
                    'Checking total cost',
                    'Updating item status',
                    'Setting priorities',
                    'Managing deadlines'
                ]
            ],
            'api_help' => [
                'example_calls' => [
                    [
                        'method' => 'POST',
                        'endpoint' => '/api/build-lists',
                        'description' => 'Create new list',
                        'payload' => [
                            'name' => 'Christmas 2025',
                            'description' => 'Gift list for family',
                            'list_type' => 'gift_list',
                            'target_date' => '2025-12-25',
                            'total_budget' => 1000.00
                        ]
                    ],
                    [
                        'method' => 'POST',
                        'endpoint' => '/api/batch',
                        'description' => 'Add items to list',
                        'payload' => [
                            'operations' => [
                                [
                                    'method' => 'POST',
                                    'resource' => 'build-list-items',
                                    'data' => [
                                        'build_list_id' => '{list_id}',
                                        'title' => 'Gift Item',
                                        'price' => 49.99,
                                        'priority' => 1
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'error_guidance' => [
                'list_type_required' => 'Please specify the type of list (e.g., gift_list, rifle_build)',
                'invalid_date' => 'Target date must be in the future',
                'budget_exceeded' => 'Total cost exceeds budget limit',
                'duplicate_item' => 'This item is already in the list'
            ]
        ]);
    }
}
