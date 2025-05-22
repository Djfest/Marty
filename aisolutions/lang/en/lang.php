<?php return [
        'plugin' => [
                'name' => 'AI Solutions',
                'description' => 'Plugin for integrating AI assistance and code analysis.'
        ],
        'navigation' => [
                'aisolutions' => 'AI Solutions',
                'aisessions' => 'AI Sessions',
                'aimessages' => 'AI Messages',
                'knowledgebase' => 'Knowledge Base', // Label for navigation
                'modelchanges' => 'Model Changes',
        ],
        'permissions' => [
                'tab' => 'AI Solutions',
                'access_sessions' => 'Access AI Sessions',
                'access_messages' => 'Access AI Messages',
                'access_knowledgebase' => 'Access and Manage Knowledge Base', // Label for permission setting
                'access_modelchanges' => 'Access Model Changes',
        ],
        'knowledgebase' => [ // Section for Knowledge Base specific strings
                'menu_label' => 'Knowledge Base',
                'list_title' => 'Manage Knowledge Base',
                'entity_name' => 'Knowledge Entry',
                'create_title' => 'Create Knowledge Base Entry',
                'update_title' => 'Edit Knowledge Base Entry',
                'preview_title' => 'Preview Knowledge Base Entry',
                // Add labels for columns and fields if needed for translation
                'key_name' => 'Key Name',
                'type' => 'Type',
                'model_scope' => 'Model/Scope',
                'profile_type' => 'Profile Type',
                'value_description' => 'Value / Description',
                'searchable_text' => 'Searchable Text',
        ],
        // Add other language strings for sessions, messages etc.
];
