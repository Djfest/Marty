# NexGen Rifle Builder API Guide for LLMs

This guide explains how to interact with the NexGen Rifle Builder API effectively as an LLM assistant.

## Understanding ProfileTypes

ProfileTypes serve as metadata repositories that describe how to interact with each model in the system. Before performing operations, query the ProfileType to understand:

1. Required fields
2. Available relationships
3. Response structure
4. Permitted operations

Example ProfileType query:
```http
GET /api/v1/profile-types/vendor
```

### Required Fields

Required fields are a critical part of the ProfileType metadata. When creating or updating records, always check the `required_fields` property to ensure you have all necessary data:

```http
GET /api/v1/profile-types/products
```

Response example:
```json
{
  "data": {
    "id": 3,
    "name": "Product",
    "model_class": "Marty\\NexGenRifle\\Models\\Product",
    "required_fields": ["name", "price", "category_id"],
    "fillable_fields": ["name", "description", "price", "sku", "category_id", "vendor_id", "is_active"],
    ...
  }
}
```

Always ensure you have values for all required fields when creating or updating records.

## Batch Operations

The API supports batch operations for creating related records in a single transaction. Use the batch endpoint when you need to:

1. Create interdependent records
2. Ensure data consistency
3. Reference IDs from previous operations

### Batch Request Format

```json
{
    "operations": [
        {
            "method": "POST|GET|PUT|DELETE",
            "resource": "resource-name",
            "data": {},
            "id": "optional-id"
        }
    ],
    "use_transaction": true
}
```

### Dynamic ID References

In batch operations, you can reference IDs from previous operations using the format:
```
{response.N.id}
```
Where N is the zero-based index of the previous operation.

## Best Practices for LLMs

1. Always validate required fields:
   ```http
   GET /api/v1/profile-types/{resource}
   ```
   Then check the `required_fields` property in the response to ensure you have all necessary data.

2. Use enhanced API metadata:
   - Every API response includes `_meta` with useful information
   - Check `_meta.model_info` for model class and description
   - Use `_meta.api_help` for examples and common errors

3. Handle nested data:
   - Use `config` fields for flexible metadata
   - Follow ProfileType-defined structure in `response_structure`

4. Error handling:
   - Check for validation errors (422)
   - Handle not found errors (404)
   - Respect permission errors (403)
   - Use `error_guidance` from ProfileType for specific error guidance

## Example Workflows

### Adding a New Product with Vendor

1. Check vendor exists:
```http
GET /api/vendors?name=example-vendor
```

2. If not exists, create both in batch:
```json
{
    "operations": [
        {
            "method": "POST",
            "resource": "vendors",
            "data": {
                "name": "Example Vendor",
                "slug": "example-vendor"
            }
        },
        {
            "method": "POST",
            "resource": "products",
            "data": {
                "vendor_id": "{response.0.id}",
                "title": "Example Product"
            }
        }
    ]
}
```

### Updating Related Resources

When updating related resources, use batch operations with transactions:

```json
{
    "operations": [
        {
            "method": "PUT",
            "resource": "products",
            "id": 123,
            "data": {
                "price": 199.99
            }
        },
        {
            "method": "PUT",
            "resource": "product-items",
            "id": 456,
            "data": {
                "inventory_status": "in_stock"
            }
        }
    ],
    "use_transaction": true
}
```

## Response Format

All responses follow HATEOAS principles and include:

1. Resource data
2. Navigation links
3. Related resource links
4. Operation metadata

Example response:
```json
{
    "data": {
        "id": 123,
        "attribute": "value"
    },
    "_links": {
        "self": {"href": "/api/resource/123"},
        "collection": {"href": "/api/resource"},
        "related": {"href": "/api/other-resource?resource_id=123"}
    }
}
```

## Common Tasks

1. Finding products by category:
```http
GET /api/products?category=upper-receiver
```

2. Getting vendor details with products:
```http
GET /api/vendors/123?include=products
```

3. Creating a rifle build:
```http
POST /api/batch
{
    "operations": [
        {
            "method": "POST",
            "resource": "rifle-builds",
            "data": {
                "title": "Custom Build",
                "category_id": 1
            }
        },
        {
            "method": "POST",
            "resource": "rifle-items",
            "data": {
                "rifle_build_id": "{response.0.id}",
                "product_id": 456
            }
        }
    ]
}
```

## Error Handling

Always check for:

1. Validation errors (422):
```json
{
    "error": "Validation failed",
    "fields": {
        "name": "The name field is required"
    }
}
```

2. Permission errors (403):
```json
{
    "error": "Access denied"
}
```

3. Not found errors (404):
```json
{
    "error": "Resource not found"
}
```

## Resource References

Keep track of common resources and their relationships:

- Vendors → Products
- Products → Product Items
- Products → Categories
- Rifle Builds → Rifle Items
- Rifle Items → Products

Use these relationships when constructing queries and batch operations.
