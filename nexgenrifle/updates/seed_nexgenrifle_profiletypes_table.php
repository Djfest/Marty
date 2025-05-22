<?php
namespace Marty\Nexgenrifle\Updates;

use Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
// use Marty\Nexgenrifle\Traits\SmartSeederTrait;

class SeedNexGenRifleProfileTypes extends Seeder
{
    // use SmartSeederTrait;

    protected $dynamicFieldDetection = false;
    protected $enableCSVexport = true;
    protected $enableCustomMigrationPath = true;
    protected $promptUserBeforeSeeding = false;
    protected $interactiveDebug = false;
    protected $migrationPath = 'database/migrations/2023_10_01_create_profile_types_table.php';
    protected $modelName = 'ProfileType';


    // New property to define boolean fields
    protected $booleanFields = ['is_active', 'is_featured'];

    protected $requiredFields = [];
    protected $jsonFields = [];
    protected $additionalFields = [];
    protected $integerFields = []; // ✅ Add this line

protected
$types = [
    'BuildCategory' => [
        'namespace' => 'nexgenrifle',
        'name' => 'BuildCategory',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-folder',
        'description' => 'Build Categories represent classifications for different build configurations.',
        'conversation_analysis' => [
            'goals' => ['Organize builds', 'Classify configurations', 'Enable filtering'],
            'strategies' => ['Categorization', 'Hierarchical organization'],
            'keywords' => ['build', 'category', 'classification', 'organization']
        ],
        'required_fields' => ['name', 'description', 'is_active'],
        'blog_article' => [
            'topics' => ['Organizing Builds with Categories', 'Efficient Build Classification'],
            'target_audience' => ['Developers', 'System administrators'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/build-categories'],
            'example_calls' => [
                'GET /api/v2/build-categories',
                'POST /api/v2/build-categories',
                'PUT /api/v2/build-categories/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [
            'BuildList' => 'Lists associated with this category'
        ],
        'prompt_instructions' => "Document Build Categories focusing on their role in organizing and classifying builds.",
        'fillable_fields' => [
            'name',
            'description',
            'is_active'
        ],
        'content_types' => ["Build classifications", "Category data"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\BuildCategory",
        'api_endpoint' => "/api/v2/build-categories",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'build_category' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'CATEGORY_NOT_FOUND' => "The requested category was not found.",
            'INVALID_CATEGORY' => "The provided category data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [
                'build_list' => 'Has-many relationship with BuildList model'
            ],
            'data_validation' => [
                'name' => 'Required',
                'is_active' => 'Required, must be boolean'
            ],
        ],
        'common' => ["category", "classification", "organization"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/BuildCategory.php",
            "plugins/marty/nexgenrifle/controllers/BuildCategories.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\BuildCategories',
        'fields_path' => 'plugins/marty/nexgenrifle/models/buildcategory/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/buildcategory/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_build_categories_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['active', 'inactive'],
        'knowledgebase' => [
            'documentation' => '/docs/api/build-categories',
            'zipfolder' => '/docs/downloads/build-categories'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'BuildList' => [
        'namespace' => 'nexgenrifle',
        'name' => 'BuildList',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-list',
        'description' => 'Build Lists represent collections of build configurations.',
        'conversation_analysis' => [
            'goals' => ['Organize builds', 'Enable batch operations', 'Facilitate management'],
            'strategies' => ['List-based organization', 'Batch processing'],
            'keywords' => ['build', 'list', 'organization', 'batch']
        ],
        'required_fields' => ['name', 'description', 'is_active'],
        'blog_article' => [
            'topics' => ['Managing Builds with Lists', 'Batch Operations for Build Configurations'],
            'target_audience' => ['Developers', 'System administrators'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/build-lists'],
            'example_calls' => [
                'GET /api/v2/build-lists',
                'POST /api/v2/build-lists',
                'PUT /api/v2/build-lists/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [
            'BuildCategory' => 'Category associated with this list',
            'BuildListItem' => 'Items within this list'
        ],
        'prompt_instructions' => "Document Build Lists focusing on their role in organizing and managing build configurations.",
        'fillable_fields' => [
            'name',
            'description',
            'category_id',
            'is_active'
        ],
        'content_types' => ["Build lists", "Configuration collections"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\BuildList",
        'api_endpoint' => "/api/v2/build-lists",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'build_list' => [
                'id',
                'name',
                'description',
                'category_id',
                'is_active',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'LIST_NOT_FOUND' => "The requested list was not found.",
            'INVALID_LIST' => "The provided list data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [
                'build_category' => 'Belongs-to relationship with BuildCategory model',
                'build_list_item' => 'Has-many relationship with BuildListItem model'
            ],
            'data_validation' => [
                'name' => 'Required',
                'category_id' => 'Required',
                'is_active' => 'Required, must be boolean'
            ],
        ],
        'common' => ["list", "organization", "batch"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/BuildList.php",
            "plugins/marty/nexgenrifle/controllers/BuildLists.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\BuildLists',
        'fields_path' => 'plugins/marty/nexgenrifle/models/buildlist/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/buildlist/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_build_lists_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['active', 'inactive'],
        'knowledgebase' => [
            'documentation' => '/docs/api/build-lists',
            'zipfolder' => '/docs/downloads/build-lists'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'BuildListItem' => [
        'namespace' => 'nexgenrifle',
        'name' => 'BuildListItem',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-list-ul',
        'description' => 'Build List Items represent individual components or products within a build list.',
        'conversation_analysis' => [
            'goals' => ['Track individual items', 'Manage quantities and costs', 'Enable detailed planning'],
            'strategies' => ['Item-level tracking', 'Cost aggregation'],
            'keywords' => ['item', 'product', 'component', 'build']
        ],
        'required_fields' => ['title', 'quantity', 'price'],
        'blog_article' => [
            'topics' => ['Managing Build Items', 'Tracking Costs and Quantities in Build Lists'],
            'target_audience' => ['Developers', 'Project managers'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/build-list-items'],
            'example_calls' => [
                'GET /api/v2/build-list-items',
                'POST /api/v2/build-list-items',
                'PUT /api/v2/build-list-items/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [
            'BuildList' => 'Parent build list',
            'Product' => 'Associated product',
            'Supplier' => 'Supplier of the product'
        ],
        'prompt_instructions' => "Document Build List Items focusing on their role in tracking individual components within a build list.",
        'fillable_fields' => [
            'build_list_id',
            'product_id',
            'supplier_id',
            'title',
            'description',
            'status',
            'price',
            'quantity',
            'priority',
            'target_date',
            'product_url',
            'affiliate_url',
            'metadata',
            'config',
            'is_acquired'
        ],
        'content_types' => ["Build items", "Product details"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\BuildListItem",
        'api_endpoint' => "/api/v2/build-list-items",
        'api_version' => 'v2',
        'query_params' => ["sort" => "priority asc"],
        'response_structure' => [
            'build_list_item' => [
                'id',
                'build_list_id',
                'product_id',
                'supplier_id',
                'title',
                'description',
                'status',
                'price',
                'quantity',
                'priority',
                'target_date',
                'product_url',
                'affiliate_url',
                'metadata',
                'config',
                'is_acquired',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'ITEM_NOT_FOUND' => "The requested item was not found.",
            'INVALID_ITEM' => "The provided item data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [
                'build_list' => 'Belongs-to relationship with BuildList model',
                'product' => 'Belongs-to relationship with Product model',
                'supplier' => 'Belongs-to relationship with Supplier model'
            ],
            'data_validation' => [
                'title' => 'Required',
                'quantity' => 'Required, must be integer',
                'price' => 'Optional, must be numeric'
            ],
        ],
        'common' => ["item", "product", "component"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/BuildListItem.php",
            "plugins/marty/nexgenrifle/controllers/BuildListItems.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\BuildListItems',
        'fields_path' => 'plugins/marty/nexgenrifle/models/buildlistitem/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/buildlistitem/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_build_list_items_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['planned', 'researching', 'selected', 'ordered', 'acquired', 'removed'],
        'knowledgebase' => [
            'documentation' => '/docs/api/build-list-items',
            'zipfolder' => '/docs/downloads/build-list-items'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'Category' => [
        'namespace' => 'nexgenrifle',
        'name' => 'Category',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-folder-open',
        'description' => 'Categories represent classifications for organizing various entities.',
        'conversation_analysis' => [
            'goals' => ['Organize entities', 'Enable filtering', 'Facilitate navigation'],
            'strategies' => ['Categorization', 'Hierarchical organization'],
            'keywords' => ['category', 'classification', 'organization']
        ],
        'required_fields' => ['name', 'description', 'is_active'],
        'blog_article' => [
            'topics' => ['Organizing Entities with Categories', 'Efficient Classification Strategies'],
            'target_audience' => ['Developers', 'System administrators'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/categories'],
            'example_calls' => [
                'GET /api/v2/categories',
                'POST /api/v2/categories',
                'PUT /api/v2/categories/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [],
        'prompt_instructions' => "Document Categories focusing on their role in organizing and classifying entities.",
        'fillable_fields' => [],
        'content_types' => ["Entity classifications", "Category data"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\Category",
        'api_endpoint' => "/api/v2/categories",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'category' => [
                'id',
                'name',
                'description',
                'metadata',
                'settings',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'CATEGORY_NOT_FOUND' => "The requested category was not found.",
            'INVALID_CATEGORY' => "The provided category data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [],
            'data_validation' => []
        ],
        'common' => ["category", "classification", "organization"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/Category.php",
            "plugins/marty/nexgenrifle/controllers/Categories.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\Categories',
        'fields_path' => 'plugins/marty/nexgenrifle/models/category/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/category/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_categories_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => [],
        'knowledgebase' => [
            'documentation' => '/docs/api/categories',
            'zipfolder' => '/docs/downloads/categories'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'Product' => [
        'namespace' => 'nexgenrifle',
        'name' => 'Product',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-box',
        'description' => 'Products represent individual items available in the catalog.',
        'conversation_analysis' => [
            'goals' => ['Manage product details', 'Enable catalog browsing', 'Facilitate purchases'],
            'strategies' => ['Detailed product descriptions', 'Categorization'],
            'keywords' => ['product', 'catalog', 'item', 'details']
        ],
        'required_fields' => ['name', 'price', 'sku', 'status'],
        'blog_article' => [
            'topics' => ['Managing Product Catalogs', 'Optimizing Product Listings'],
            'target_audience' => ['E-commerce managers', 'Developers'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/products'],
            'example_calls' => [
                'GET /api/v2/products',
                'POST /api/v2/products',
                'PUT /api/v2/products/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [
            'Category' => 'Category associated with the product',
            'Image' => 'Attached image for the product'
        ],
        'prompt_instructions' => "Document Products focusing on their role in representing catalog items.",
        'fillable_fields' => [
            'name',
            'slug',
            'description',
            'price',
            'sku',
            'stock',
            'status',
            'metadata',
            'specifications',
            'dimensions',
            'options'
        ],
        'content_types' => ["Product details", "Catalog items"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\Product",
        'api_endpoint' => "/api/v2/products",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'product' => [
                'id',
                'name',
                'slug',
                'description',
                'price',
                'sku',
                'stock',
                'status',
                'metadata',
                'specifications',
                'dimensions',
                'options',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'PRODUCT_NOT_FOUND' => "The requested product was not found.",
            'INVALID_PRODUCT' => "The provided product data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [
                'category' => 'Belongs-to relationship with Category model',
                'image' => 'Attach-one relationship with File model'
            ],
            'data_validation' => [
                'name' => 'Required',
                'price' => 'Optional, must be numeric',
                'stock' => 'Optional, must be integer'
            ],
        ],
        'common' => ["product", "catalog", "item"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/Product.php",
            "plugins/marty/nexgenrifle/controllers/Products.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\Products',
        'fields_path' => 'plugins/marty/nexgenrifle/models/product/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/product/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_products_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['available', 'out_of_stock', 'discontinued'],
        'knowledgebase' => [
            'documentation' => '/docs/api/products',
            'zipfolder' => '/docs/downloads/products'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'ProductCatalog' => [
        'namespace' => 'nexgenrifle',
        'name' => 'ProductCatalog',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-book',
        'description' => 'Product Catalogs represent collections of products available in the system.',
        'conversation_analysis' => [
            'goals' => ['Organize products', 'Enable catalog browsing', 'Facilitate product discovery'],
            'strategies' => ['Categorization', 'Search optimization'],
            'keywords' => ['catalog', 'product', 'collection', 'organization']
        ],
        'required_fields' => ['name', 'description', 'is_active'],
        'blog_article' => [
            'topics' => ['Building Product Catalogs', 'Optimizing Product Discovery'],
            'target_audience' => ['E-commerce managers', 'Developers'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/product-catalogs'],
            'example_calls' => [
                'GET /api/v2/product-catalogs',
                'POST /api/v2/product-catalogs',
                'PUT /api/v2/product-catalogs/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [],
        'prompt_instructions' => "Document Product Catalogs focusing on their role in organizing and managing collections of products.",
        'fillable_fields' => ['name', 'description', 'is_active'],
        'content_types' => ["Catalog data", "Product collections"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\ProductCatalog",
        'api_endpoint' => "/api/v2/product-catalogs",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'product_catalog' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'CATALOG_NOT_FOUND' => "The requested catalog was not found.",
            'INVALID_CATALOG' => "The provided catalog data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [],
            'data_validation' => [
                'name' => 'Required',
                'description' => 'Optional',
                'is_active' => 'Required, must be boolean'
            ],
        ],
        'common' => ["catalog", "product", "collection"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/ProductCatalog.php",
            "plugins/marty/nexgenrifle/controllers/ProductCatalogs.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\ProductCatalogs',
        'fields_path' => 'plugins/marty/nexgenrifle/models/productcatalog/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/productcatalog/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_product_catalogs_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['active', 'inactive'],
        'knowledgebase' => [
            'documentation' => '/docs/api/product-catalogs',
            'zipfolder' => '/docs/downloads/product-catalogs'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'ProductCategory' => [
        'namespace' => 'nexgenrifle',
        'name' => 'ProductCategory',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-tags',
        'description' => 'Product Categories represent classifications for organizing products.',
        'conversation_analysis' => [
            'goals' => ['Classify products', 'Enable filtering', 'Facilitate navigation'],
            'strategies' => ['Categorization', 'Hierarchical organization'],
            'keywords' => ['category', 'product', 'classification', 'organization']
        ],
        'required_fields' => ['name', 'description', 'is_active'],
        'blog_article' => [
            'topics' => ['Organizing Products with Categories', 'Efficient Classification Strategies'],
            'target_audience' => ['E-commerce managers', 'Developers'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/product-categories'],
            'example_calls' => [
                'GET /api/v2/product-categories',
                'POST /api/v2/product-categories',
                'PUT /api/v2/product-categories/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [],
        'prompt_instructions' => "Document Product Categories focusing on their role in organizing and classifying products.",
        'fillable_fields' => ['name', 'description', 'is_active'],
        'content_types' => ["Category data", "Product classifications"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\ProductCategory",
        'api_endpoint' => "/api/v2/product-categories",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'product_category' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'CATEGORY_NOT_FOUND' => "The requested category was not found.",
            'INVALID_CATEGORY' => "The provided category data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [],
            'data_validation' => [
                'name' => 'Required',
                'description' => 'Optional',
                'is_active' => 'Required, must be boolean'
            ],
        ],
        'common' => ["category", "product", "classification"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/ProductCategory.php",
            "plugins/marty/nexgenrifle/controllers/ProductCategories.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\ProductCategories',
        'fields_path' => 'plugins/marty/nexgenrifle/models/productcategory/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/productcategory/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_product_categories_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['active', 'inactive'],
        'knowledgebase' => [
            'documentation' => '/docs/api/product-categories',
            'zipfolder' => '/docs/downloads/product-categories'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'ProductItem' => [
        'namespace' => 'nexgenrifle',
        'name' => 'ProductItem',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-cube',
        'description' => 'Product Items represent individual products within a catalog.',
        'conversation_analysis' => [
            'goals' => ['Manage product details', 'Enable catalog browsing', 'Facilitate purchases'],
            'strategies' => ['Detailed product descriptions', 'Categorization'],
            'keywords' => ['product', 'item', 'catalog', 'details']
        ],
        'required_fields' => ['name', 'price', 'sku', 'is_active'],
        'blog_article' => [
            'topics' => ['Managing Product Items', 'Optimizing Product Listings'],
            'target_audience' => ['E-commerce managers', 'Developers'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/product-items'],
            'example_calls' => [
                'GET /api/v2/product-items',
                'POST /api/v2/product-items',
                'PUT /api/v2/product-items/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [],
        'prompt_instructions' => "Document Product Items focusing on their role in representing individual products within a catalog.",
        'fillable_fields' => ['name', 'price', 'sku', 'is_active'],
        'content_types' => ["Product details", "Catalog items"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\ProductItem",
        'api_endpoint' => "/api/v2/product-items",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'product_item' => [
                'id',
                'name',
                'price',
                'sku',
                'is_active',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'ITEM_NOT_FOUND' => "The requested item was not found.",
            'INVALID_ITEM' => "The provided item data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [],
            'data_validation' => [
                'name' => 'Required',
                'price' => 'Required, must be numeric',
                'sku' => 'Required',
                'is_active' => 'Required, must be boolean'
            ],
        ],
        'common' => ["product", "item", "catalog"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/ProductItem.php",
            "plugins/marty/nexgenrifle/controllers/ProductItems.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\ProductItems',
        'fields_path' => 'plugins/marty/nexgenrifle/models/productitem/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/productitem/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_product_items_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['available', 'out_of_stock', 'discontinued'],
        'knowledgebase' => [
            'documentation' => '/docs/api/product-items',
            'zipfolder' => '/docs/downloads/product-items'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'RifleBuild' => [
        'namespace' => 'nexgenrifle',
        'name' => 'RifleBuild',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-wrench',
        'description' => 'Rifle Builds represent configurations for assembling rifles.',
        'conversation_analysis' => [
            'goals' => ['Design rifle configurations', 'Track build progress', 'Facilitate assembly'],
            'strategies' => ['Component selection', 'Cost estimation'],
            'keywords' => ['rifle', 'build', 'configuration', 'assembly']
        ],
        'required_fields' => ['name', 'description', 'is_active'],
        'blog_article' => [
            'topics' => ['Designing Rifle Builds', 'Optimizing Rifle Configurations'],
            'target_audience' => ['Gun enthusiasts', 'Manufacturers'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/rifle-builds'],
            'example_calls' => [
                'GET /api/v2/rifle-builds',
                'POST /api/v2/rifle-builds',
                'PUT /api/v2/rifle-builds/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [],
        'prompt_instructions' => "Document Rifle Builds focusing on their role in designing and managing rifle configurations.",
        'fillable_fields' => ['name', 'description', 'is_active'],
        'content_types' => ["Build configurations", "Assembly data"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\RifleBuild",
        'api_endpoint' => "/api/v2/rifle-builds",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'rifle_build' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'BUILD_NOT_FOUND' => "The requested build was not found.",
            'INVALID_BUILD' => "The provided build data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [],
            'data_validation' => [
                'name' => 'Required',
                'description' => 'Optional',
                'is_active' => 'Required, must be boolean'
            ],
        ],
        'common' => ["rifle", "build", "configuration"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/RifleBuild.php",
            "plugins/marty/nexgenrifle/controllers/RifleBuilds.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\RifleBuilds',
        'fields_path' => 'plugins/marty/nexgenrifle/models/riflebuild/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/riflebuild/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_rifle_builds_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['active', 'inactive'],
        'knowledgebase' => [
            'documentation' => '/docs/api/rifle-builds',
            'zipfolder' => '/docs/downloads/rifle-builds'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'RifleItem' => [
        'namespace' => 'nexgenrifle',
        'name' => 'RifleItem',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-cogs',
        'description' => 'Rifle Items represent individual components within a rifle build.',
        'conversation_analysis' => [
            'goals' => ['Manage rifle components', 'Track inventory', 'Facilitate assembly'],
            'strategies' => ['Component tracking', 'Cost estimation'],
            'keywords' => ['rifle', 'item', 'component', 'assembly']
        ],
        'required_fields' => ['name', 'description', 'is_active'],
        'blog_article' => [
            'topics' => ['Managing Rifle Components', 'Optimizing Rifle Assembly'],
            'target_audience' => ['Gun enthusiasts', 'Manufacturers'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/rifle-items'],
            'example_calls' => [
                'GET /api/v2/rifle-items',
                'POST /api/v2/rifle-items',
                'PUT /api/v2/rifle-items/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [],
        'prompt_instructions' => "Document Rifle Items focusing on their role in representing individual components within a rifle build.",
        'fillable_fields' => ['name', 'description', 'is_active'],
        'content_types' => ["Component data", "Assembly items"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\RifleItem",
        'api_endpoint' => "/api/v2/rifle-items",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'rifle_item' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'ITEM_NOT_FOUND' => "The requested item was not found.",
            'INVALID_ITEM' => "The provided item data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [],
            'data_validation' => [
                'name' => 'Required',
                'description' => 'Optional',
                'is_active' => 'Required, must be boolean'
            ],
        ],
        'common' => ["rifle", "item", "component"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/RifleItem.php",
            "plugins/marty/nexgenrifle/controllers/RifleItems.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\RifleItems',
        'fields_path' => 'plugins/marty/nexgenrifle/models/rifleitem/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/rifleitem/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_rifle_items_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['available', 'out_of_stock', 'discontinued'],
        'knowledgebase' => [
            'documentation' => '/docs/api/rifle-items',
            'zipfolder' => '/docs/downloads/rifle-items'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'Settings' => [
        'namespace' => 'nexgenrifle',
        'name' => 'Settings',
        'type' => 'system',
        'access' => 'restricted',
        'icon' => 'icon-cog',
        'description' => 'Settings represent configuration options for the application.',
        'conversation_analysis' => [
            'goals' => ['Manage application settings', 'Enable customization', 'Facilitate configuration'],
            'strategies' => ['Option tracking', 'User preferences'],
            'keywords' => ['settings', 'configuration', 'options', 'preferences']
        ],
        'required_fields' => ['name', 'description', 'is_active'],
        'blog_article' => [
            'topics' => ['Managing Application Settings', 'Optimizing User Preferences'],
            'target_audience' => ['System administrators', 'Developers'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/settings'],
            'example_calls' => [
                'GET /api/v2/settings',
                'POST /api/v2/settings',
                'PUT /api/v2/settings/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [],
        'prompt_instructions' => "Document Settings focusing on their role in managing application configuration options.",
        'fillable_fields' => ['name', 'description', 'is_active'],
        'content_types' => ["Configuration data", "Application settings"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\Settings",
        'api_endpoint' => "/api/v2/settings",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'settings' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'SETTINGS_NOT_FOUND' => "The requested settings were not found.",
            'INVALID_SETTINGS' => "The provided settings data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [],
            'data_validation' => [
                'name' => 'Required',
                'description' => 'Optional',
                'is_active' => 'Required, must be boolean'
            ],
        ],
        'common' => ["settings", "configuration", "options"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/Settings.php",
            "plugins/marty/nexgenrifle/controllers/Settings.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\Settings',
        'fields_path' => 'plugins/marty/nexgenrifle/models/settings/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/settings/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_settings_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['active', 'inactive'],
        'knowledgebase' => [
            'documentation' => '/docs/api/settings',
            'zipfolder' => '/docs/downloads/settings'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'SocialConnection' => [
        'namespace' => 'nexgenrifle',
        'name' => 'SocialConnection',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-share-alt',
        'description' => 'Social Connections represent links between users and social platforms.',
        'conversation_analysis' => [
            'goals' => ['Connect users to platforms', 'Enable social sharing', 'Track interactions'],
            'strategies' => ['Platform integration', 'User engagement'],
            'keywords' => ['social', 'connection', 'platform', 'sharing']
        ],
        'required_fields' => ['name', 'description', 'is_active'],
        'blog_article' => [
            'topics' => ['Integrating Social Platforms', 'Enhancing User Engagement'],
            'target_audience' => ['Developers', 'Marketers'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/social-connections'],
            'example_calls' => [
                'GET /api/v2/social-connections',
                'POST /api/v2/social-connections',
                'PUT /api/v2/social-connections/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [],
        'prompt_instructions' => "Document Social Connections focusing on their role in linking users to social platforms.",
        'fillable_fields' => ['name', 'description', 'is_active'],
        'content_types' => ["Connection data", "Platform links"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\SocialConnection",
        'api_endpoint' => "/api/v2/social-connections",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'social_connection' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'CONNECTION_NOT_FOUND' => "The requested connection was not found.",
            'INVALID_CONNECTION' => "The provided connection data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [],
            'data_validation' => [
                'name' => 'Required',
                'description' => 'Optional',
                'is_active' => 'Required, must be boolean'
            ],
        ],
        'common' => ["social", "connection", "platform"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/SocialConnection.php",
            "plugins/marty/nexgenrifle/controllers/SocialConnections.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\SocialConnections',
        'fields_path' => 'plugins/marty/nexgenrifle/models/socialconnection/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/socialconnection/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_social_connections_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['active', 'inactive'],
        'knowledgebase' => [
            'documentation' => '/docs/api/social-connections',
            'zipfolder' => '/docs/downloads/social-connections'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'Supplier' => [
        'namespace' => 'nexgenrifle',
        'name' => 'Supplier',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-truck',
        'description' => 'Suppliers represent entities that provide products to the system.',
        'conversation_analysis' => [
            'goals' => ['Manage supplier data', 'Track product sources', 'Facilitate procurement'],
            'strategies' => ['Supplier tracking', 'Inventory management'],
            'keywords' => ['supplier', 'products', 'procurement', 'inventory']
        ],
        'required_fields' => ['name', 'description', 'is_active'],
        'blog_article' => [
            'topics' => ['Managing Suppliers', 'Optimizing Procurement Processes'],
            'target_audience' => ['Procurement managers', 'Developers'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/suppliers'],
            'example_calls' => [
                'GET /api/v2/suppliers',
                'POST /api/v2/suppliers',
                'PUT /api/v2/suppliers/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [],
        'prompt_instructions' => "Document Suppliers focusing on their role in providing products to the system.",
        'fillable_fields' => ['name', 'description', 'is_active'],
        'content_types' => ["Supplier data", "Procurement records"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\Supplier",
        'api_endpoint' => "/api/v2/suppliers",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'supplier' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'SUPPLIER_NOT_FOUND' => "The requested supplier was not found.",
            'INVALID_SUPPLIER' => "The provided supplier data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [],
            'data_validation' => [
                'name' => 'Required',
                'description' => 'Optional',
                'is_active' => 'Required, must be boolean'
            ],
        ],
        'common' => ["supplier", "products", "procurement"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/Supplier.php",
            "plugins/marty/nexgenrifle/controllers/Suppliers.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\Suppliers',
        'fields_path' => 'plugins/marty/nexgenrifle/models/supplier/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/supplier/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_suppliers_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['active', 'inactive'],
        'knowledgebase' => [
            'documentation' => '/docs/api/suppliers',
            'zipfolder' => '/docs/downloads/suppliers'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'UserProfile' => [
        'namespace' => 'nexgenrifle',
        'name' => 'UserProfile',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-user',
        'description' => 'User Profiles represent individual user accounts and their associated data.',
        'conversation_analysis' => [
            'goals' => ['Manage user data', 'Enable personalization', 'Facilitate user interactions'],
            'strategies' => ['Profile management', 'Data security'],
            'keywords' => ['user', 'profile', 'account', 'data']
        ],
        'required_fields' => ['name', 'email', 'is_active'],
        'blog_article' => [
            'topics' => ['Managing User Profiles', 'Enhancing User Personalization'],
            'target_audience' => ['Developers', 'System administrators'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/user-profiles'],
            'example_calls' => [
                'GET /api/v2/user-profiles',
                'POST /api/v2/user-profiles',
                'PUT /api/v2/user-profiles/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [],
        'prompt_instructions' => "Document User Profiles focusing on their role in managing individual user accounts.",
        'fillable_fields' => ['name', 'email', 'is_active'],
        'content_types' => ["User data", "Account information"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\UserProfile",
        'api_endpoint' => "/api/v2/user-profiles",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'user_profile' => [
                'id',
                'name',
                'email',
                'is_active',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'PROFILE_NOT_FOUND' => "The requested profile was not found.",
            'INVALID_PROFILE' => "The provided profile data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [],
            'data_validation' => [
                'name' => 'Required',
                'email' => 'Required, must be a valid email',
                'is_active' => 'Required, must be boolean'
            ],
        ],
        'common' => ["user", "profile", "account"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/UserProfile.php",
            "plugins/marty/nexgenrifle/controllers/UserProfiles.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\UserProfiles',
        'fields_path' => 'plugins/marty/nexgenrifle/models/userprofile/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/userprofile/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_user_profiles_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['active', 'inactive'],
        'knowledgebase' => [
            'documentation' => '/docs/api/user-profiles',
            'zipfolder' => '/docs/downloads/user-profiles'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
    'Vendor' => [
        'namespace' => 'nexgenrifle',
        'name' => 'Vendor',
        'type' => 'resource',
        'access' => 'restricted',
        'icon' => 'icon-store',
        'description' => 'Vendors represent entities that sell products within the system.',
        'conversation_analysis' => [
            'goals' => ['Manage vendor data', 'Track sales', 'Facilitate transactions'],
            'strategies' => ['Vendor tracking', 'Sales management'],
            'keywords' => ['vendor', 'sales', 'products', 'transactions']
        ],
        'required_fields' => ['name', 'description', 'is_active'],
        'blog_article' => [
            'topics' => ['Managing Vendors', 'Optimizing Sales Processes'],
            'target_audience' => ['Sales managers', 'Developers'],
            'writing_style' => 'Technical, structured, with examples'
        ],
        'api_help' => [
            'documentation_links' => ['/api/v2/vendors'],
            'example_calls' => [
                'GET /api/v2/vendors',
                'POST /api/v2/vendors',
                'PUT /api/v2/vendors/{id}'
            ],
            'common_errors' => ['404 Not Found', '401 Unauthorized']
        ],
        'tone' => 'Technical, systematic, informative',
        'relationships' => [],
        'prompt_instructions' => "Document Vendors focusing on their role in selling products within the system.",
        'fillable_fields' => ['name', 'description', 'is_active'],
        'content_types' => ["Vendor data", "Sales records"],
        'model_class' => "Marty\\Nexgenrifle\\Models\\Vendor",
        'api_endpoint' => "/api/v2/vendors",
        'api_version' => 'v2',
        'query_params' => ["sort" => "name asc"],
        'response_structure' => [
            'vendor' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at'
            ]
        ],
        'error_guidance' => [
            'VENDOR_NOT_FOUND' => "The requested vendor was not found.",
            'INVALID_VENDOR' => "The provided vendor data is invalid."
        ],
        'logging_details' => [
            'key_relationships' => [],
            'data_validation' => [
                'name' => 'Required',
                'description' => 'Optional',
                'is_active' => 'Required, must be boolean'
            ],
        ],
        'common' => ["vendor", "sales", "products"],
        'related_files' => [
            "plugins/marty/nexgenrifle/models/Vendor.php",
            "plugins/marty/nexgenrifle/controllers/Vendors.php"
        ],
        'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\Vendors',
        'fields_path' => 'plugins/marty/nexgenrifle/models/vendor/fields.yaml',
        'columns_path' => 'plugins/marty/nexgenrifle/models/vendor/columns.yaml',
        'migration_path' => 'plugins/marty/nexgenrifle/updates/create_vendors_table.php',
        'api_methods' => ['get', 'post', 'put'],
        'status_groups' => ['active', 'inactive'],
        'knowledgebase' => [
            'documentation' => '/docs/api/vendors',
            'zipfolder' => '/docs/downloads/vendors'
        ],
        'is_active' => true,
        'is_featured' => false,
        'is_default' => false,
        'ai_enabled' => false
    ],
];
    
    protected function detectModelFields($modelInstance)
    {
        $table = $modelInstance->getTable();
        $columns = Schema::getColumnListing($table);

        $jsonFields = [];
        $requiredFields = [];
        $additionalFields = [];
        $integerFields = []; // ✅ New array for integer fields
        $booleanFields = []; // ✅ Already exists

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
                    $integerFields[] = $column; // ✅ Correctly detect integer fields
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
            'integer' => $integerFields, // ✅ Ensure this key is always returned
            'boolean' => $booleanFields, // ✅ Already existing
        ];
    }


    public function run()
    {
        $errors = [];
        $validatedTypes = [];
        $DEBUG_MODE = "errors";

        if ($this->promptUserBeforeSeeding) {
            echo "\n\e[1;34mNexgenrifle.app:\e[0m You are about to seed data into the model: \e[1;33m{$this->modelName}\e[0m. Proceed? (y/n): ";
            $confirmation = trim(fgets(STDIN));
            if (strtolower($confirmation) !== 'y') {
                Log::warning("Seeding aborted by user for model: {$this->modelName}.");
                echo "\e[1;31mSeeding aborted. No changes made.\e[0m\n";
                return;
            }
        }

        $jsonKeys = json_encode(array_keys($this->types));
        if ($this->promptUserBeforeSeeding) {
            echo "\n\e[1;34mNexgenrifle.app:\e[0m You are about to seed the following Models:  \e[1;33m{$jsonKeys}\e[0m. Proceed? (y/n): ";
            $confirmation = trim(fgets(STDIN));
            if (strtolower($confirmation) !== 'y') {
                Log::warning("🫷🛑Seeding aborted by user for model: {$this->modelName}.");
                echo "\e[1;31mSeeding aborted. No changes made.\e[0m\n";
                return;
            }
        }

        // Get namespace from the type array OR default to Marty\Nexgenrifle\Models
        $namespace = !empty($this->types['namespace']) ? $this->types['namespace'] : "Marty\\Nexgenrifle";
        echo (Log::info("Using namespace: $namespace For the following model: {$this->modelName}"));
        // Construct the full model class
        $modelClass = "{$namespace}\\Models\\{$this->modelName}";

        if (!class_exists($modelClass)) {
            Log::warning("Model class '$modelClass' not found. Checking if a custom model class is provided.");

            // Check if a fully qualified model_class is set inside the type array
            if (!empty($this->types['model_class']) && class_exists($this->types['model_class'])) {
                $modelClass = $this->types['model_class'];
            } else {
                Log::error("No valid model class found for '{$this->modelName}'. Seeder aborted.");
                return;
            }
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
            $statusMessages = [];

            if ($this->promptUserBeforeSeeding) {
                // echo "\n\e[1;34mNexgenrifle.app:\e[0m You are about to seed the following Models:  \e[1;33m{$jsonKeys}\e[0m. Proceed? (y/n): ";
                $confirmation = 'y'; // trim(fgets(STDIN));
                if (strtolower($confirmation) !== 'y') {
                    Log::warning("🫷🛑Seeding aborted by user for model: {$this->modelName}.");
                    echo "\e[1;31mSeeding aborted. No changes made.\e[0m\n";
                    return;
                } else {
                    //echo the name of the model and it's required fields and contine to the next model automatically
                    echo "\n\e[1;34mNexgenrifle.app:\e[0m You are about to seed the following Model:  \e[1;33m{$key}\e[0m. Proceeding to the next model automatically: ";

                }
            }

            foreach ($this->requiredFields as $field) {
                if (!array_key_exists($field, $type)) {
                    $type[$field] = $this->getDefaultValueForField($field);
                }
            }
            $type['ai_enabled'] = $type['ai_enabled'] ?? false;

            $recordsReady++;
            $validatedTypes[$key] = $type;
        }

        if ($recordsReady === 0) {
            Log::warning("No valid records were found. Seeder aborted. Type Array could be empty.");
            echo "\e[1;33m⚠️ No valid records found. Seeder aborted  Type Array could be empty..\e[0m\n";
            return;
        }


        if ($this->promptUserBeforeSeeding) {
            echo "\n\e[1;34mNexgenrifle.app:\e[0m want to inspect this data?  \e[1;33m{$jsonKeys}\e[0m. Proceed? (y/n): ";
            $confirmation = 'y'; // trim(fgets(STDIN));
            if (strtolower($confirmation) !== 'y') {
                // Log::warning("🫷🛑Seeding aborted by user for model: {$this->modelName}.");
                echo "\e[1;31mModel inspection Skipped.\e[0m\n";
            } else {
                foreach ($validatedTypes as $type) {
                    // echo "\n\e[1;34mNexgenrifle.app:\e[0m Inspecting the following Model:  \e[1;33m{$type}\e[0m. Proceeding to the next model automatically: ";

                }


            }
        }



        try {
            DB::transaction(function () use ($validatedTypes, $modelClass) {
                $recordsSaved = 0;
                // echo the validatedTypes
                foreach ($validatedTypes as $type) {
                    Log::info("Attempting to insert/update: {$type['name']}");

                    $record = $modelClass::where('name', $type['name'])->first();
                    if ($record) {
                        Log::info("Skipping {$type['name']} - Already Exists");
                        echo "\e[1;33m⏭️Skipping record: {$type['name']} - Already Exists\e[0m\n";
                    } else {
                        Log::info("Creating 🆕 record for: {$type['name']} (UUID will be auto-generated by model)");
                        $record = new $modelClass;
                    }

                    // Dynamically handle JSON and boolean fields
                    foreach ($type as $field => &$value) { // Pass $value by reference
                        if (in_array($field, $this->jsonFields) && is_string($value)) {
                            $value = json_decode($value, true) ?? []; // Decode JSON or default to empty array
                        } elseif (in_array($field, $this->booleanFields)) {
                            $value = (bool) $value; // Cast to boolean
                        }
                        // Add more conditions here for other data types if needed
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
                        echo "\e[1;31m❌ Error saving record '{$type['name']}']: " . $e->getMessage() . "\e[0m\n";
                    }
                }

                if ($recordsSaved > 0) {
                    Log::info("Seeder completed successfully for model: {$this->modelName}.");
                    echo "\e[1;32m🎉 Nexgenrifle.app: Seeder completed successfully for model: {$this->modelName}.\e[0m\n";
                } else {
                    Log::warning("No records were inserted or updated for model: {$this->modelName}.");
                    echo "\e[1;33m⚠️ Nexgenrifle.app: No records were inserted or updated for model: {$this->modelName}.\e[0m\n";
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
            return 0; // Boolean default
        } elseif ($field === 'order') {
            return 0; // Default integer value for order
        } elseif (in_array($field, $this->requiredFields)) {
            return ""; // Fallback for required string fields
        }
        return null;
    }

    protected function normalizeFieldValues(array &$data)
    {
        foreach ($data as $key => &$value) {
            // Ensure integer fields are correctly cast to integers.
            if (in_array($key, $this->integerFields)) {
                // If a value is an empty string, (int)"" becomes 0.
                // If null is intended for nullable integer fields, it should be explicitly set as null
                // in the $types array data.
                if ($value !== null) { // Avoid casting an explicit null to 0
                    $value = (int) $value;
                }
            }
            // Note: JSON string to array and boolean casting are largely handled
            // by the loop that runs just before this normalizeFieldValues method is called.
            // Additional or stricter type checks could be added here if needed.
        }
    }
}
