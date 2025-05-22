# NexGenRifle API Guide for ChatGPT Integration

## Understanding Model Dependencies

The NexGenRifle API uses a structured data model with dependencies between resources. When creating certain resources, you need to ensure that related resources exist first.

### AiMessage Creation Example

The `aimessage` resource requires a parent session. Here's the proper flow:

1. First, create an AiSession:
   ```json
   POST /resources/aisession
   {
       "title": "New Conversation Session",
       "code_session_id": "unique-session-identifier",
       "status": "open"
   }
   ```

2. Then create an AiMessage using the session_id from the previous response:
   ```json
   POST /resources/aimessage
   {
       "session_id": 123,  // Use the actual ID returned from step 1
       "content": "This is the message content",
       "is_from_ai": true  // true if from AI, false if from user
   }
   ```

### Required Fields

Always check the validation rules for each model. The common required fields for key models are:

- **AiSession**:
  - `code_session_id` (string, unique)
  - `title` (string)
  
- **AiMessage**:
  - `session_id` (integer, references sessions but can be nullable)
  - `content` (string)
  - `is_from_ai` (boolean)

- **AiSolution**:
  - `name` (string, unique): A unique name for the solution or function.
  - `description` (string): A human-readable description of what the solution does.
  - `schema` (json): The schema defining parameters or the action itself (e.g., for function calling with Gemini or commands for the VSCode agent via MCP servers).
  - `type` (string): Type of solution, e.g., "function_call", "mcp_command".

### Database Design Best Practices

When designing tables with foreign keys:
- Always make foreign key fields nullable in the database schema (e.g., `$table->unsignedInteger('session_id')->nullable()`)
- Use application-level validation when specific relationships are required rather than database constraints
- This approach provides more flexibility for data migration, test data creation, and avoiding circular dependency issues
- Always use the proper delete cascade or set null behavior that makes sense for your data relationships

## Using the Generic API Endpoints

The API provides generic endpoints that work with all resources:

- `GET /resources/{resource}` - List resources
- `GET /resources/{resource}/{id}` - Get specific resource
- `POST /resources/{resource}` - Create resource
- `PUT /resources/{resource}/{id}` - Update resource
- `DELETE /resources/{resource}/{id}` - Delete resource

Additionally, there are special endpoints:

- `POST /batch` - Execute multiple operations in a batch
- `POST /process` - Process data with custom actions
- `GET /profile-types` - Get all profile type definitions

## Authentication

Include the API key in all requests using one of these methods:

1. Header: `X-API-Key: YOUR_API_KEY`
2. Bearer token: `Authorization: Bearer YOUR_API_KEY`
3. Query parameter: `?api_key=YOUR_API_KEY`

## Error Handling

Pay attention to validation errors (HTTP 422). The response will include specific field errors:

```json
{
  "error": "Validation error",
  "messages": {
    "session_id": {
      "errors": ["The session id field is required."]
    },
    "content": {
      "errors": ["The content field is required."]
    }
  }
}
```

Use the error information to correct the request parameters.

## Dependencies between Tables

Many resources have foreign key relationships. Always ensure the related records exist before creating dependent records. The common dependencies are:

- AiMessage → requires existing AiSession
- ModelChange → requires existing AiSession
- BuildListItem → requires existing BuildList
