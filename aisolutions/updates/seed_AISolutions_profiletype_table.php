<?php
namespace Marty\AiSolutions\Updates;

use Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedAISolutionsProfileTypes extends Seeder
{
    protected $dynamicFieldDetection = false;
    protected $enableCSVexport = true;
    protected $enableCustomMigrationPath = true;
    protected $promptUserBeforeSeeding = false;
    protected $interactiveDebug = false;
    protected $modelName = 'ProfileType';
    protected $migrationPath = 'database/migrations/2023_10_01_create_profile_types_table.php';
    
    // Define field types
    protected $booleanFields = ['is_active', 'is_featured', 'is_default', 'ai_enabled'];
    protected $requiredFields = [];
    protected $jsonFields = [
        'api_help', 'blog_article', 'common', 'content_types', 'conversation_analysis',
        'error_guidance', 'fillable_fields', 'sensitive_fields', 'logging_details',
        'query_params', 'related_files', 'relationships', 'response_structure',
        'status_groups', 'knowledgebase', 'api_methods', 'methods', 'metadata'
    ];
    protected $additionalFields = [];
    protected $integerFields = ['order', 'user_id'];

    protected $types = [
        'aisessions' => [
            'namespace' => 'aisolutions',
            'name' => 'AiSession',
            'type' => 'resource',
            'access' => 'restricted',
            'icon' => 'icon-comments',
            'description' => 'AI Sessions represent conversation contexts between users and AI assistants, tracking messages and maintaining state throughout interactions.',
            'conversation_analysis' => [
                'goals' => ['Track conversation history', 'Maintain context', 'Organize interactions', 'Enable continued conversations'],
                'strategies' => ['Sequential message storage', 'Context preservation', 'Status management', 'Entity association'],
                'keywords' => ['session', 'conversation', 'chat', 'history', 'context', 'interaction', 'dialogue']
            ],
            'blog_article' => [
                'topics' => ['Managing AI Conversations with Session State', 'Building Stateful AI Interactions', 'Context Management in AI Systems'],
                'target_audience' => ['Developers', 'System administrators', 'UX designers'],
                'writing_style' => 'Technical, process-oriented, with implementation examples'
            ],
            'api_help' => [
                'documentation_links' => ['/api/v2/ai-sessions'],
                'example_calls' => [
                    'GET /api/v2/ai-sessions',
                    'GET /api/v2/ai-sessions/{id}',
                    'POST /api/v2/ai-sessions',
                    'PUT /api/v2/ai-sessions/{id}'
                ],
                'common_errors' => ['404 Not Found', '401 Unauthorized', '403 Forbidden']
            ],
            'tone' => 'Technical, systematic, informative',
            'relationships' => [
                'User' => 'Creator of or participant in the session',
                'ProfileType' => 'Type of entity associated with the session',
                'AiMessage' => 'Messages within the session'
            ],
            'prompt_instructions' => "Document AI sessions focusing on their role in maintaining conversation context and state. Include details about session lifecycle, status transitions, and relationships with messages and entities. Emphasize the importance of session management for continuous, context-aware interactions.",
            'fillable_fields' => [
                'title',
                'description',
                'profile_type_id',
                'entity_id',
                'user_id',
                'external_id',
                'status'
            ],
            'content_types' => ["Session records", "Conversation contexts", "Interaction history"],
            'model_class' => "Marty\\AiSolutions\\Models\\AiSession",
            'api_endpoint' => "/api/v2/ai-sessions",
            'api_version' => 'v2',
            'query_params' => ["sort" => "created_at desc", "with" => ["messages", "user"]],
            'response_structure' => [
                'ai_session' => [
                    'id',
                    'title',
                    'description',
                    'profile_type_id',
                    'entity_id',
                    'user_id',
                    'external_id',
                    'status',
                    'created_at',
                    'updated_at',
                    'messages' => []
                ]
            ],
            'error_guidance' => [
                'SESSION_NOT_FOUND' => "The requested AI session was not found. Please check the session ID.",
                'SESSION_LOCKED' => "This session is locked and cannot be modified.",
                'INVALID_STATUS' => "The provided status is not valid for an AI session."
            ],
            'logging_details' => [
                'key_relationships' => [
                    'user' => 'Belongs-to relationship with User model',
                    'profile_type' => 'Belongs-to relationship with ProfileType model',
                    'messages' => 'Has-many relationship with AiMessage model',
                ],
                'data_validation' => [
                    'title' => 'Required',
                    'status' => 'Required, must be one of: active, paused, completed, archived',
                ],
            ],
            'common' => ["session", "conversation", "chat", "dialogue", "interaction", "exchange"],
            'related_files' => [
                "plugins/marty/aisolutions/updates/create_ai_sessions_table.php",
                "plugins/marty/aisolutions/models/AiSession.php",
                "plugins/marty/aisolutions/controllers/AiSessions.php",
                "plugins/marty/aisolutions/models/aisession/fields.yaml",
                "plugins/marty/aisolutions/models/aisession/columns.yaml"
            ],
            'controller_path' => 'Marty\\AiSolutions\\Controllers\\AiSessions',
            'fields_path' => 'plugins/marty/aisolutions/models/aisession/fields.yaml',
            'columns_path' => 'plugins/marty/aisolutions/models/aisession/columns.yaml',
            'migration_path' => 'plugins/marty/aisolutions/updates/create_ai_sessions_table.php',
            'api_methods' => ['get', 'post', 'put', 'delete'],
            'status_groups' => ['active', 'paused', 'completed', 'archived'],
            'knowledgebase' => [
                'documentation' => '/docs/api/ai-sessions',
                'zipfolder' => '/docs/downloads/ai-sessions'
            ],
            'is_active' => true,
            'is_featured' => false,
            'is_default' => false,
            'ai_enabled' => true
        ],

        'aimessages' => [
            'namespace' => 'aisolutions',
            'name' => 'AiMessage',
            'type' => 'resource',
            'access' => 'restricted',
            'icon' => 'icon-comment',
            'description' => 'AI Messages are individual communications exchanged between users and AI assistants within a session, containing the content and metadata of each interaction.',
            'conversation_analysis' => [
                'goals' => ['Record individual exchanges', 'Store message content', 'Track message source', 'Capture message metadata'],
                'strategies' => ['Content preservation', 'Source identification', 'Metadata collection', 'Sequential organization'],
                'keywords' => ['message', 'content', 'exchange', 'text', 'response', 'query', 'prompt']
            ],
            'blog_article' => [
                'topics' => ['Structured AI Communication', 'Message Analysis in AI Systems', 'Building Intelligent Response Systems'],
                'target_audience' => ['Developers', 'Data scientists', 'Conversational AI specialists'],
                'writing_style' => 'Technical, analytical, with practical applications'
            ],
            'api_help' => [
                'documentation_links' => ['/api/v2/ai-messages'],
                'example_calls' => [
                    'GET /api/v2/ai-messages',
                    'GET /api/v2/ai-messages/{id}',
                    'POST /api/v2/ai-messages',
                    'GET /api/v2/ai-sessions/{session_id}/messages'
                ],
                'common_errors' => ['404 Not Found', '401 Unauthorized', '422 Validation Error']
            ],
            'tone' => 'Precise, technical, structured',
            'relationships' => [
                'AiSession' => 'Session containing the message',
                'User' => 'User who sent or received the message'
            ],
            'prompt_instructions' => "Document AI messages with attention to their content, source (user or AI), relationship to sessions, and any associated metadata. Include information about message ordering, content formatting, and the significance of metadata in tracking message processing and generation.",
            'fillable_fields' => [
                'session_id',
                'content',
                'is_from_ai',
                'user_id',
                'external_id',
                'metadata'
            ],
            'sensitive_fields' => ['content', 'metadata'],
            'content_types' => ["Message content", "Interaction records", "Communication data"],
            'model_class' => "Marty\\AiSolutions\\Models\\AiMessage",
            'api_endpoint' => "/api/v2/ai-messages",
            'api_version' => 'v2',
            'query_params' => ["sort" => "created_at asc", "with" => ["session"]],
            'response_structure' => [
                'ai_message' => [
                    'id',
                    'session_id',
                    'content',
                    'is_from_ai',
                    'user_id',
                    'external_id',
                    'metadata',
                    'created_at',
                    'updated_at'
                ]
            ],
            'error_guidance' => [
                'MESSAGE_NOT_FOUND' => "The requested AI message was not found. Please check the message ID.",
                'INVALID_CONTENT' => "The message content is missing or invalid.",
                'SESSION_REQUIRED' => "A valid session ID is required for this message."
            ],
            'logging_details' => [
                'key_relationships' => [
                    'session' => 'Belongs-to relationship with AiSession model',
                    'user' => 'Belongs-to relationship with User model',
                ],
                'data_validation' => [
                    'session_id' => 'Required, must reference a valid session',
                    'content' => 'Required',
                ],
            ],
            'common' => ["message", "chat message", "conversation entry", "exchange", "prompt", "response", "reply"],
            'related_files' => [
                "plugins/marty/aisolutions/updates/create_ai_messages_table.php",
                "plugins/marty/aisolutions/models/AiMessage.php",
                "plugins/marty/aisolutions/controllers/AiMessages.php",
                "plugins/marty/aisolutions/models/aimessage/fields.yaml",
                "plugins/marty/aisolutions/models/aimessage/columns.yaml"
            ],
            'controller_path' => 'Marty\\AiSolutions\\Controllers\\AiMessages',
            'fields_path' => 'plugins/marty/aisolutions/models/aimessage/fields.yaml',
            'columns_path' => 'plugins/marty/aisolutions/models/aimessage/columns.yaml',
            'migration_path' => 'plugins/marty/aisolutions/updates/create_ai_messages_table.php',
            'api_methods' => ['get', 'post'],
            'status_groups' => ['sent', 'received', 'processed', 'failed'],
            'knowledgebase' => [
                'documentation' => '/docs/api/ai-messages',
                'zipfolder' => '/docs/downloads/ai-messages'
            ],
            'is_active' => true,
            'is_featured' => false,
            'is_default' => false,
            'ai_enabled' => true
        ],
        
        'modelchanges' => [
            'namespace' => 'aisolutions',
            'name' => 'ModelChange',
            'type' => 'system',
            'access' => 'restricted',
            'icon' => 'icon-history',
            'description' => 'Model Changes track modifications made to system models through AI interactions, providing an audit trail of field changes with previous and new values.',
            'conversation_analysis' => [
                'goals' => ['Track model modifications', 'Audit AI-driven changes', 'Record field alterations', 'Maintain change history'],
                'strategies' => ['Change tracking', 'Field comparison', 'User attribution', 'Session correlation'],
                'keywords' => ['change', 'modification', 'audit', 'tracking', 'history', 'field', 'value']
            ],
            'blog_article' => [
                'topics' => ['Auditing AI-Driven Changes in Systems', 'Tracking Model Modifications', 'Building Accountable AI Systems'],
                'target_audience' => ['Developers', 'System administrators', 'Compliance officers', 'AI governance specialists'],
                'writing_style' => 'Technical, audit-focused, compliance-oriented'
            ],
            'api_help' => [
                'documentation_links' => ['/api/v2/model-changes'],
                'example_calls' => [
                    'GET /api/v2/model-changes',
                    'GET /api/v2/model-changes/{id}',
                    'GET /api/v2/model-changes/by-model/{model_type}/{model_id}'
                ],
                'common_errors' => ['404 Not Found', '401 Unauthorized', '403 Forbidden']
            ],
            'tone' => 'Technical, audit-focused, precise',
            'relationships' => [
                'User' => 'User who initiated the change',
                'AiSession' => 'Session during which the change occurred',
                'AiMessage' => 'Message that triggered the change'
            ],
            'prompt_instructions' => "Document model changes with emphasis on their role in tracking and auditing modifications to system data. Include details about storing previous and new values, identifying the source of changes, and the importance of maintaining a reliable audit trail for compliance and accountability purposes.",
            'fillable_fields' => [
                'model_type',
                'model_id',
                'field',
                'old_value',
                'new_value',
                'user_id',
                'session_id',
                'message_id'
            ],
            'content_types' => ["Change records", "Audit entries", "Modification history"],
            'model_class' => "Marty\\AiSolutions\\Models\\ModelChange",
            'api_endpoint' => "/api/v2/model-changes",
            'api_version' => 'v2',
            'query_params' => ["sort" => "created_at desc", "with" => ["user", "session"]],
            'response_structure' => [
                'model_change' => [
                    'id',
                    'model_type',
                    'model_id',
                    'field',
                    'old_value',
                    'new_value',
                    'user_id',
                    'session_id',
                    'message_id',
                    'created_at',
                    'updated_at'
                ]
            ],
            'error_guidance' => [
                'CHANGE_NOT_FOUND' => "The requested model change was not found. Please check the change ID.",
                'MODEL_NOT_FOUND' => "The referenced model does not exist.",
                'ACCESS_DENIED' => "You do not have permission to view these change records."
            ],
            'logging_details' => [
                'key_relationships' => [
                    'user' => 'Belongs-to relationship with User model',
                    'session' => 'Belongs-to relationship with AiSession model',
                    'message' => 'Belongs-to relationship with AiMessage model',
                ],
                'data_validation' => [
                    'model_type' => 'Required, must be a valid model class',
                    'model_id' => 'Required, must reference an existing model',
                    'field' => 'Required',
                ],
            ],
            'common' => ["change", "modification", "audit", "history", "tracking", "revision", "edit"],
            'related_files' => [
                "plugins/marty/aisolutions/updates/create_model_changes_table.php",
                "plugins/marty/aisolutions/models/ModelChange.php",
                "plugins/marty/aisolutions/controllers/ModelChanges.php",
                "plugins/marty/aisolutions/models/modelchange/fields.yaml",
                "plugins/marty/aisolutions/models/modelchange/columns.yaml"
            ],
            'controller_path' => 'Marty\\AiSolutions\\Controllers\\ModelChanges',
            'fields_path' => 'plugins/marty/aisolutions/models/modelchange/fields.yaml',
            'columns_path' => 'plugins/marty/aisolutions/models/modelchange/columns.yaml',
            'migration_path' => 'plugins/marty/aisolutions/updates/create_model_changes_table.php',
            'api_methods' => ['get'],
            'status_groups' => ['created', 'updated', 'deleted'],
            'knowledgebase' => [
                'documentation' => '/docs/api/model-changes',
                'zipfolder' => '/docs/downloads/model-changes'
            ],
            'is_active' => true,
            'is_featured' => false,
            'is_default' => false,
            'ai_enabled' => true
        ],

        'aiknowledgebases' => [
            'namespace' => 'aisolutions',
            'name' => 'AiKnowledgeBase',
            'type' => 'resource',
            'access' => 'restricted',
            'icon' => 'icon-book',
            'description' => 'AI Knowledge Bases store specialized information and documents that AI systems can reference to provide more accurate, domain-specific responses.',
            'conversation_analysis' => [
                'goals' => ['Information retrieval', 'Domain-specific knowledge', 'Contextual understanding', 'Accurate responses'],
                'strategies' => ['Knowledge organization', 'Document indexing', 'Semantic search', 'Contextual matching'],
                'keywords' => ['knowledge', 'information', 'documents', 'retrieval', 'context', 'domain', 'expertise']
            ],
            'blog_article' => [
                'topics' => ['Building Effective AI Knowledge Bases', 'Enhancing AI with Domain-Specific Knowledge', 'Knowledge Management for AI Systems'],
                'target_audience' => ['Knowledge engineers', 'Content managers', 'AI specialists', 'Domain experts'],
                'writing_style' => 'Instructional, technical, with practical guidelines'
            ],
            'api_help' => [
                'documentation_links' => ['/api/v2/ai-knowledge-bases'],
                'example_calls' => [
                    'GET /api/v2/ai-knowledge-bases',
                    'GET /api/v2/ai-knowledge-bases/{id}',
                    'POST /api/v2/ai-knowledge-bases',
                    'PUT /api/v2/ai-knowledge-bases/{id}'
                ],
                'common_errors' => ['404 Not Found', '401 Unauthorized', '403 Forbidden']
            ],
            'tone' => 'Informative, educational, systematic',
            'relationships' => [
                'User' => 'Creator or manager of the knowledge base',
                'Documents' => 'Content contained in the knowledge base'
            ],
            'prompt_instructions' => "Document AI knowledge bases with focus on their structure, content organization, and purpose in supporting AI systems. Include details about knowledge domains, document types, and how information is retrieved and used in AI interactions.",
            'fillable_fields' => [
                'name',
                'description',
                'domain',
                'status',
                'embedding_model',
                'configuration',
                'user_id',
                'is_active'
            ],
            'content_types' => ["Knowledge base entries", "Document collections", "Domain knowledge"],
            'model_class' => "Marty\\AiSolutions\\Models\\AiKnowledgeBase",
            'api_endpoint' => "/api/v2/ai-knowledge-bases",
            'api_version' => 'v2',
            'query_params' => ["sort" => "created_at desc", "with" => ["documents"]],
            'response_structure' => [
                'ai_knowledge_base' => [
                    'id',
                    'name',
                    'description',
                    'domain',
                    'status',
                    'embedding_model',
                    'configuration',
                    'user_id',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'documents' => []
                ]
            ],
            'error_guidance' => [
                'KB_NOT_FOUND' => "The requested knowledge base was not found. Please check the ID.",
                'KB_EMPTY' => "This knowledge base contains no documents yet.",
                'INVALID_DOMAIN' => "The specified domain is not recognized."
            ],
            'logging_details' => [
                'key_relationships' => [
                    'user' => 'Belongs-to relationship with User model',
                    'documents' => 'Has-many relationship with Document model',
                ],
                'data_validation' => [
                    'name' => 'Required, must be unique',
                    'embedding_model' => 'Required, must reference a valid embedding model',
                ],
            ],
            'common' => ["kb", "knowledge base", "information repository", "document store", "reference library", "information source"],
            'related_files' => [
                "plugins/marty/aisolutions/updates/create_ai_knowledge_bases_table.php",
                "plugins/marty/aisolutions/models/AiKnowledgeBase.php",
                "plugins/marty/aisolutions/controllers/AiKnowledgeBases.php",
                "plugins/marty/aisolutions/models/aiknowledgebase/fields.yaml",
                "plugins/marty/aisolutions/models/aiknowledgebase/columns.yaml"
            ],
            'controller_path' => 'Marty\\AiSolutions\\Controllers\\AiKnowledgeBases',
            'fields_path' => 'plugins/marty/aisolutions/models/aiknowledgebase/fields.yaml',
            'columns_path' => 'plugins/marty/aisolutions/models/aiknowledgebase/columns.yaml',
            'migration_path' => 'plugins/marty/aisolutions/updates/create_ai_knowledge_bases_table.php',
            'api_methods' => ['get', 'post', 'put', 'delete'],
            'status_groups' => ['active', 'building', 'updating', 'archived'],
            'knowledgebase' => [
                'documentation' => '/docs/api/ai-knowledge-bases',
                'zipfolder' => '/docs/downloads/ai-knowledge-bases'
            ],
            'is_active' => true,
            'is_featured' => false,
            'is_default' => false,
            'ai_enabled' => true
        ],

        'modelgraphs' => [
            'namespace' => 'aisolutions',
            'name' => 'ModelGraph',
            'type' => 'system',
            'access' => 'restricted',
            'icon' => 'icon-sitemap',
            'description' => 'Model Graphs represent relationship networks between different entities in the system, enabling AI to understand connections and context across data models.',
            'conversation_analysis' => [
                'goals' => ['Understand entity relationships', 'Map data connections', 'Visualize model interdependencies', 'Enable contextual navigation'],
                'strategies' => ['Graph-based representation', 'Relationship mapping', 'Connection strength analysis', 'Contextual traversal'],
                'keywords' => ['graph', 'relationship', 'connection', 'network', 'node', 'edge', 'mapping']
            ],
            'blog_article' => [
                'topics' => ['Understanding Data Relationships with Model Graphs', 'Graph-Based AI for Contextual Understanding', 'Mapping Complex System Relationships'],
                'target_audience' => ['System architects', 'Data scientists', 'AI developers', 'Database specialists'],
                'writing_style' => 'Technical, analytical, with visualization examples'
            ],
            'api_help' => [
                'documentation_links' => ['/api/v2/model-graphs'],
                'example_calls' => [
                    'GET /api/v2/model-graphs',
                    'GET /api/v2/model-graphs/{id}',
                    'GET /api/v2/model-graphs/for-model/{model_type}/{model_id}'
                ],
                'common_errors' => ['404 Not Found', '401 Unauthorized', '403 Forbidden']
            ],
            'tone' => 'Technical, analytical, structured',
            'relationships' => [
                'Models' => 'The entities represented in the graph',
                'User' => 'Creator or administrator of the graph'
            ],
            'prompt_instructions' => "Document model graphs with emphasis on their role in representing relationships between system entities. Include information about graph structure, node types, edge connections, and how graphs help AI systems understand contextual relationships across different data models.",
            'fillable_fields' => [
                'name',
                'description',
                'graph_data',
                'model_type',
                'model_id',
                'configuration',
                'user_id',
                'is_active'
            ],
            'content_types' => ["Relationship graphs", "Connection maps", "Entity networks"],
            'model_class' => "Marty\\AiSolutions\\Models\\ModelGraph",
            'api_endpoint' => "/api/v2/model-graphs",
            'api_version' => 'v2',
            'query_params' => ["sort" => "created_at desc"],
            'response_structure' => [
                'model_graph' => [
                    'id',
                    'name',
                    'description',
                    'graph_data',
                    'model_type',
                    'model_id',
                    'configuration',
                    'user_id',
                    'is_active',
                    'created_at',
                    'updated_at'
                ]
            ],
            'error_guidance' => [
                'GRAPH_NOT_FOUND' => "The requested model graph was not found. Please check the ID.",
                'INVALID_GRAPH_DATA' => "The graph data structure is invalid or malformed.",
                'MODEL_NOT_FOUND' => "The referenced model does not exist."
            ],
            'logging_details' => [
                'key_relationships' => [
                    'user' => 'Belongs-to relationship with User model',
                ],
                'data_validation' => [
                    'name' => 'Required',
                    'graph_data' => 'Required, must be valid JSON representing graph structure',
                ],
            ],
            'common' => ["graph", "model graph", "relationship map", "connection network", "entity graph", "data network"],
            'related_files' => [
                "plugins/marty/aisolutions/updates/create_model_graphs_table.php",
                "plugins/marty/aisolutions/models/ModelGraph.php",
                "plugins/marty/aisolutions/controllers/ModelGraphs.php",
                "plugins/marty/aisolutions/models/modelgraph/fields.yaml",
                "plugins/marty/aisolutions/models/modelgraph/columns.yaml"
            ],
            'controller_path' => 'Marty\\AiSolutions\\Controllers\\ModelGraphs',
            'fields_path' => 'plugins/marty/aisolutions/models/modelgraph/fields.yaml',
            'columns_path' => 'plugins/marty/aisolutions/models/modelgraph/columns.yaml',
            'migration_path' => 'plugins/marty/aisolutions/updates/create_model_graphs_table.php',
            'api_methods' => ['get'],
            'status_groups' => ['active', 'building', 'outdated', 'archived'],
            'knowledgebase' => [
                'documentation' => '/docs/api/model-graphs',
                'zipfolder' => '/docs/downloads/model-graphs'
            ],
            'is_active' => true,
            'is_featured' => false,
            'is_default' => false,
            'ai_enabled' => true
        ],

        'profiletypeextensions' => [
            'namespace' => 'aisolutions',
            'name' => 'ProfileTypeExtension',
            'type' => 'system',
            'access' => 'restricted',
            'icon' => 'icon-puzzle-piece',
            'description' => 'Profile Type Extensions add AI-specific capabilities and metadata to existing profile types, enhancing them with AI functionality and integration points.',
            'conversation_analysis' => [
                'goals' => ['Extend model capabilities', 'Add AI integration points', 'Define AI-specific behavior', 'Enhance model metadata'],
                'strategies' => ['Capability mapping', 'Integration configuration', 'Behavior definition', 'Metadata enhancement'],
                'keywords' => ['extension', 'enhancement', 'capability', 'integration', 'behavior', 'metadata', 'profile']
            ],
            'blog_article' => [
                'topics' => ['Extending System Models with AI Capabilities', 'Building AI-Ready Data Models', 'Integrating AI with Existing Systems'],
                'target_audience' => ['System architects', 'AI developers', 'Integration specialists', 'Model designers'],
                'writing_style' => 'Technical, implementation-focused, with practical examples'
            ],
            'api_help' => [
                'documentation_links' => ['/api/v2/profile-type-extensions'],
                'example_calls' => [
                    'GET /api/v2/profile-type-extensions',
                    'GET /api/v2/profile-type-extensions/{id}',
                    'GET /api/v2/profile-type-extensions/for-profile/{profile_type_id}'
                ],
                'common_errors' => ['404 Not Found', '401 Unauthorized', '403 Forbidden']
            ],
            'tone' => 'Technical, system-oriented, implementation-focused',
            'relationships' => [
                'ProfileType' => 'The profile type being extended',
                'User' => 'Creator or administrator of the extension'
            ],
            'prompt_instructions' => "Document profile type extensions with focus on how they enhance existing models with AI capabilities. Include information about integration points, added functionality, and configuration options. Emphasize the role of extensions in adapting existing system models for AI interaction without modifying their core structure.",
            'fillable_fields' => [
                'profile_type_id',
                'capabilities',
                'configuration',
                'ai_behavior',
                'integration_points',
                'metadata_schema',
                'is_active',
                'user_id'
            ],
            'content_types' => ["Extension configurations", "Capability definitions", "Integration specifications"],
            'model_class' => "Marty\\AiSolutions\\Models\\ProfileTypeExtension",
            'api_endpoint' => "/api/v2/profile-type-extensions",
            'api_version' => 'v2',
            'query_params' => ["with" => ["profile_type"]],
            'response_structure' => [
                'profile_type_extension' => [
                    'id',
                    'profile_type_id',
                    'capabilities',
                    'configuration',
                    'ai_behavior',
                    'integration_points',
                    'metadata_schema',
                    'is_active',
                    'user_id',
                    'created_at',
                    'updated_at',
                    'profile_type' => []
                ]
            ],
            'error_guidance' => [
                'EXTENSION_NOT_FOUND' => "The requested profile type extension was not found. Please check the ID.",
                'PROFILE_TYPE_NOT_FOUND' => "The referenced profile type does not exist.",
                'DUPLICATE_EXTENSION' => "An extension for this profile type already exists."
            ],
            'logging_details' => [
                'key_relationships' => [
                    'profile_type' => 'Belongs-to relationship with ProfileType model',
                    'user' => 'Belongs-to relationship with User model',
                ],
                'data_validation' => [
                    'profile_type_id' => 'Required, must reference a valid profile type',
                    'capabilities' => 'Required, must be valid JSON listing capabilities',
                ],
            ],
            'common' => ["extension", "profile extension", "capability extension", "ai integration", "model enhancement"],
            'related_files' => [
                "plugins/marty/aisolutions/updates/create_profile_type_extensions_table.php",
                "plugins/marty/aisolutions/models/ProfileTypeExtension.php",
                "plugins/marty/aisolutions/controllers/ProfileTypeExtensions.php",
                "plugins/marty/aisolutions/models/profiletypeextension/fields.yaml",
                "plugins/marty/aisolutions/models/profiletypeextension/columns.yaml"
            ],
            'controller_path' => 'Marty\\AiSolutions\\Controllers\\ProfileTypeExtensions',
            'fields_path' => 'plugins/marty/aisolutions/models/profiletypeextension/fields.yaml',
            'columns_path' => 'plugins/marty/aisolutions/models/profiletypeextension/columns.yaml',
            'migration_path' => 'plugins/marty/aisolutions/updates/create_profile_type_extensions_table.php',
            'api_methods' => ['get'],
            'status_groups' => ['active', 'inactive', 'development'],
            'knowledgebase' => [
                'documentation' => '/docs/api/profile-type-extensions',
                'zipfolder' => '/docs/downloads/profile-type-extensions'
            ],
            'is_active' => true,
            'is_featured' => false,
            'is_default' => false,
            'ai_enabled' => true
        ],

        'syncs' => [
            'namespace' => 'aisolutions',
            'name' => 'Sync',
            'type' => 'system',
            'access' => 'restricted',
            'icon' => 'icon-refresh',
            'description' => 'Sync records track synchronization status between AI systems and external systems or data sources, managing data flow and consistency.',
            'conversation_analysis' => [
                'goals' => ['Track data synchronization', 'Monitor system integration', 'Manage data flow', 'Ensure consistency'],
                'strategies' => ['Status tracking', 'Error handling', 'Synchronization logging', 'Progress monitoring'],
                'keywords' => ['sync', 'synchronization', 'integration', 'data flow', 'consistency', 'external system']
            ],
            'blog_article' => [
                'topics' => ['Managing AI Data Synchronization', 'Building Robust AI Integration Systems', 'Maintaining Data Consistency Across Systems'],
                'target_audience' => ['System integrators', 'Data engineers', 'IT operations', 'DevOps professionals'],
                'writing_style' => 'Technical, process-oriented, with monitoring and troubleshooting focus'
            ],
            'api_help' => [
                'documentation_links' => ['/api/v2/syncs'],
                'example_calls' => [
                    'GET /api/v2/syncs',
                    'GET /api/v2/syncs/{id}',
                    'POST /api/v2/syncs/trigger/{sync_type}'
                ],
                'common_errors' => ['404 Not Found', '401 Unauthorized', '500 Internal Server Error']
            ],
            'tone' => 'Technical, operational, monitoring-focused',
            'relationships' => [
                'User' => 'User who initiated or manages the sync',
                'External systems' => 'Systems being synchronized with'
            ],
            'prompt_instructions' => "Document sync operations with focus on their role in maintaining data consistency between AI systems and external data sources. Include information about sync types, status monitoring, error handling, and implementation details for different synchronization scenarios.",
            'fillable_fields' => [
                'sync_type',
                'status',
                'source',
                'target',
                'progress',
                'last_run_at',
                'next_run_at',
                'configuration',
                'results',
                'user_id'
            ],
            'content_types' => ["Sync records", "Integration logs", "Data flow tracking"],
            'model_class' => "Marty\\AiSolutions\\Models\\Sync",
            'api_endpoint' => "/api/v2/syncs",
            'api_version' => 'v2',
            'query_params' => ["sort" => "last_run_at desc"],
            'response_structure' => [
                'sync' => [
                    'id',
                    'sync_type',
                    'status',
                    'source',
                    'target',
                    'progress',
                    'last_run_at',
                    'next_run_at',
                    'configuration',
                    'results',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ],
            'error_guidance' => [
                'SYNC_NOT_FOUND' => "The requested sync record was not found. Please check the ID.",
                'SYNC_IN_PROGRESS' => "A sync operation is already in progress for this type.",
                'SYNC_FAILED' => "The sync operation failed. Check the results for details."
            ],
            'logging_details' => [
                'key_relationships' => [
                    'user' => 'Belongs-to relationship with User model',
                ],
                'data_validation' => [
                    'sync_type' => 'Required, must be a valid sync type',
                    'status' => 'Required, must be a valid status value',
                ],
            ],
            'common' => ["sync", "synchronization", "data sync", "integration", "data flow", "external system", "consistency"],
            'related_files' => [
                "plugins/marty/aisolutions/updates/create_syncs_table.php",
                "plugins/marty/aisolutions/models/Sync.php",
                "plugins/marty/aisolutions/controllers/Syncs.php",
                "plugins/marty/aisolutions/models/sync/fields.yaml",
                "plugins/marty/aisolutions/models/sync/columns.yaml"
            ],
            'controller_path' => 'Marty\\AiSolutions\\Controllers\\Syncs',
            'fields_path' => 'plugins/marty/aisolutions/models/sync/fields.yaml',
            'columns_path' => 'plugins/marty/aisolutions/models/sync/columns.yaml',
            'migration_path' => 'plugins/marty/aisolutions/updates/create_syncs_table.php',
            'api_methods' => ['get', 'post'],
            'status_groups' => ['pending', 'in_progress', 'completed', 'failed', 'scheduled'],
            'knowledgebase' => [
                'documentation' => '/docs/api/syncs',
                'zipfolder' => '/docs/downloads/syncs'
            ],
            'is_active' => true,
            'is_featured' => false,
            'is_default' => false,
            'ai_enabled' => true
        ]
    ];

    protected function detectModelFields($modelInstance)
    {
        $table = $modelInstance->getTable();
        $columns = Schema::getColumnListing($table);

        $jsonFields = [];
        $requiredFields = [];
        $additionalFields = [];
        $integerFields = [];
        $booleanFields = [];

        foreach ($columns as $column) {
            if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at', 'uuid'])) {
                continue;
            }
            try {
                $type = Schema::getColumnType($table, $column);

                if ($type === 'json') {
                    $jsonFields[] = $column;
                } elseif ($type === 'boolean' || str_starts_with($column, 'is_')) {
                    $booleanFields[] = $column;
                } elseif (in_array($type, ['integer', 'bigint', 'smallint', 'mediumint', 'tinyint'])) {
                    $integerFields[] = $column;
                }

                if (Schema::getConnection()->getDoctrineColumn($table, $column)->getNotnull()) {
                    $requiredFields[] = $column;
                } else {
                    $additionalFields[] = $column;
                }
            } catch (\Exception $e) {
                Log::error("Error detecting field '$column' in '$table': " . $e->getMessage());
            }
        }

        return [
            'required' => $requiredFields,
            'json' => $jsonFields,
            'additional' => $additionalFields,
            'integer' => $integerFields,
            'boolean' => $booleanFields,
        ];
    }

    public function run()
    {
        $errors = [];
        $validatedTypes = [];
        $DEBUG_MODE = "errors";

        if ($this->promptUserBeforeSeeding) {
            echo "\n\e[1;34mAiSolutions:\e[0m You are about to seed data into the model: \e[1;33m{$this->modelName}\e[0m. Proceed? (y/n): ";
            $confirmation = trim(fgets(STDIN));
            if (strtolower($confirmation) !== 'y') {
                Log::warning("Seeding aborted by user for model: {$this->modelName}.");
                echo "\e[1;31mSeeding aborted. No changes made.\e[0m\n";
                return;
            }
        }

        $jsonKeys = json_encode(array_keys($this->types));
        if ($this->promptUserBeforeSeeding) {
            echo "\n\e[1;34mAiSolutions:\e[0m You are about to seed the following Models:  \e[1;33m{$jsonKeys}\e[0m. Proceed? (y/n): ";
            $confirmation = trim(fgets(STDIN));
            if (strtolower($confirmation) !== 'y') {
                Log::warning("🛑 Seeding aborted by user for model: {$this->modelName}.");
                echo "\e[1;31mSeeding aborted. No changes made.\e[0m\n";
                return;
            }
        }

        // Get namespace for the ProfileType model
        $namespace = "Marty\\Nexgenrifle";
        $modelClass = "{$namespace}\\Models\\{$this->modelName}";

        if (!class_exists($modelClass)) {
            Log::warning("Model class '$modelClass' not found. Checking if a custom model class is provided.");
            
            Log::error("No valid model class found for '{$this->modelName}'. Seeder aborted.");
            return;
        }

        $modelInstance = new $modelClass;
        $fieldDetection = $this->detectModelFields($modelInstance);
        $this->requiredFields = $fieldDetection['required'];
        $this->jsonFields = $fieldDetection['json'];
        $this->additionalFields = $fieldDetection['additional'];
        $this->integerFields = array_unique(array_merge($this->integerFields, $fieldDetection['integer']));
        $this->booleanFields = array_unique(array_merge($this->booleanFields, $fieldDetection['boolean']));

        $recordsReady = 0;
        foreach ($this->types as $key => $type) {
            foreach ($this->requiredFields as $field) {
                if (!array_key_exists($field, $type)) {
                    $type[$field] = $this->getDefaultValueForField($field);
                }
            }
            
            // Ensure critical boolean fields are set
            $type['is_active'] = $type['is_active'] ?? true;
            $type['is_featured'] = $type['is_featured'] ?? false;
            $type['is_default'] = $type['is_default'] ?? false;
            $type['ai_enabled'] = $type['ai_enabled'] ?? true;
            
            $recordsReady++;
            $validatedTypes[$key] = $type;
        }

        if ($recordsReady === 0) {
            Log::warning("No valid records were found. Seeder aborted. Type Array could be empty.");
            echo "\e[1;33m⚠️ No valid records found. Seeder aborted. Type Array could be empty.\e[0m\n";
            return;
        }

        try {
            DB::transaction(function () use ($validatedTypes, $modelClass) {
                $recordsSaved = 0;
                
                foreach ($validatedTypes as $type) {
                    Log::info("Attempting to insert/update: {$type['name']}");

                    $record = $modelClass::where('name', $type['name'])->first();
                    if ($record) {
                        Log::info("Updating existing record: {$type['name']}");
                        echo "\e[1;33m🔄 Updating existing record: {$type['name']}\e[0m\n";
                    } else {
                        Log::info("Creating 🆕 record for: {$type['name']} (UUID will be auto-generated by model)");
                        $record = new $modelClass;
                    }

                    // Dynamically handle JSON and boolean fields
                    foreach ($type as $field => &$value) {
                        if (in_array($field, $this->jsonFields) && is_string($value)) {
                            $value = json_decode($value, true) ?? [];
                        } elseif (in_array($field, $this->booleanFields)) {
                            $value = (bool) $value;
                        }
                    }
                    $this->normalizeFieldValues($type);

                    $record->fill($type);
                    $record->type = $type['type']; // Ensure the type field is set
                    try {
                        $record->save();
                        Log::info("✅ Successfully saved model '{$type['name']}' in {$this->modelName}.");
                        echo "\e[1;32m✅ Successfully saved record: {$type['name']}\e[0m\n";
                        $recordsSaved++;
                    } catch (\Exception $e) {
                        Log::error("Error saving record '{$type['name']}': " . $e->getMessage());
                        echo "\e[1;31m❌ Error saving record '{$type['name']}': " . $e->getMessage() . "\e[0m\n";
                    }
                }

                if ($recordsSaved > 0) {
                    Log::info("Seeder completed successfully for model: {$this->modelName}.");
                    echo "\e[1;32m🎉 AiSolutions: Seeder completed successfully for model: {$this->modelName}.\e[0m\n";
                } else {
                    Log::warning("No records were inserted or updated for model: {$this->modelName}.");
                    echo "\e[1;33m⚠️ AiSolutions: No records were inserted or updated for model: {$this->modelName}.\e[0m\n";
                }
            });
        } catch (\Exception $e) {
            Log::error("Seeder Error: Error seeding {$this->modelName}: " . $e->getMessage());
            echo "\e[1;31m❌ Seeder Error: Error seeding {$this->modelName}: " . $e->getMessage() . "\e[0m\n";
            throw $e;
        }
    }

    private function getDefaultValueForField($field)
    {
        if (in_array($field, $this->jsonFields)) {
            return [];
        } elseif (in_array($field, $this->booleanFields)) {
            return false;
        } elseif ($field === 'order') {
            return 0;
        } elseif (in_array($field, $this->requiredFields)) {
            return "";
        }
        return null;
    }

    protected function normalizeFieldValues(array &$data)
    {
        foreach ($data as $key => &$value) {
            // Ensure integer fields are correctly cast to integers
            if (in_array($key, $this->integerFields)) {
                if ($value !== null) {
                    $value = (int) $value;
                }
            }
        }
    }
}
