<?php namespace Marty\NexGenRifle\Middleware;  // Fixed namespace capitalization

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next)
    {
        // Get configured API key(s)
        $systemApiKey = env('NEXGENRIFLE_SYSTEM_API_KEY');
        $devApiKey = env('NEXGENRIFLE_DEV_API_KEY', 'development-key');
        
        // Allow access in development/debug mode
        $isDebugMode = env('APP_DEBUG', false);
        
        // Get provided key from various places and track source
        list($providedApiKey, $keySource) = $this->getProvidedApiKey($request);

        // Log the API request with masked key
        $this->logApiRequest($request, $providedApiKey, $keySource);

        // Always allow access in APP_DEBUG mode, with a warning
        if (empty($systemApiKey) && $isDebugMode) {
            Log::warning('NEXGENRIFLE_SYSTEM_API_KEY is not set in the .env file. Using debug mode fallback for authentication.');
            
            // In debug mode, if no key provided or doesn't match dev key, warn but allow access
            if (empty($providedApiKey) || (!hash_equals($providedApiKey, $devApiKey) && !empty($devApiKey))) {
                Log::warning('Authentication bypassed in debug mode. For proper security, set NEXGENRIFLE_SYSTEM_API_KEY in production.');
            }
            
            // Mark as developer access
            $request->attributes->set('is_dev_access', true);
            return $next($request);
        }
        
        // Production checks - only reach here if systemApiKey is set or not in debug mode
        if (empty($systemApiKey)) {
            // This is a server configuration issue
            Log::error('NEXGENRIFLE_SYSTEM_API_KEY is not set in the .env file and APP_DEBUG is off.');
            return response()->json([
                'error' => 'API authentication is not configured correctly.',
                'debug_info' => $isDebugMode ? [
                    'message' => 'Please set NEXGENRIFLE_SYSTEM_API_KEY in your .env file.',
                    'env_file_path' => base_path('.env')
                ] : null
            ], 500);
        }
        
        // No API key provided in the request
        if (empty($providedApiKey)) {
            return response()->json([
                'error' => 'Unauthorized. API key is missing.',
                'message' => 'Please provide a valid API key using one of the following methods:',
                'options' => [
                    'header' => 'X-API-Key: your-api-key',
                    'bearer' => 'Authorization: Bearer your-api-key',
                    'parameter' => '?api_key=your-api-key'
                ]
            ], 401);
        }
        
        // Check against system key
        if (hash_equals((string) $systemApiKey, (string) $providedApiKey)) {
            $request->attributes->set('is_system_api_key_used', true);
            Log::info('API access granted using system API key');
            return $next($request);
        }
        
        // In debug mode, also check against the dev key
        if ($isDebugMode && hash_equals((string) $devApiKey, (string) $providedApiKey)) {
            $request->attributes->set('is_dev_access', true);
            Log::warning('API access granted using development API key - not secure for production use');
            return $next($request);
        }
        
        // API key provided but invalid
        return response()->json([
            'error' => 'Forbidden. Invalid API key.',
            'message' => 'The provided API key is incorrect.'
        ], 403);
    }
    
    /**
     * Get the API key from the request using various methods
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array [string|null, string] - [API key, source of the key]
     */
    protected function getProvidedApiKey($request)
    {
        // Check in X-API-Key header (preferred)
        $key = $request->header('X-API-Key');
        if (!empty($key)) {
            return [$key, 'X-API-Key header'];
        }
        
        // Check in Authorization header as Bearer token
        $bearerToken = $request->bearerToken();
        if (!empty($bearerToken)) {
            return [$bearerToken, 'Bearer token'];
        }
        
        // Check in query parameter - look for both api_key and key formats
        $queryKey = $request->query('api_key');
        if (!empty($queryKey)) {
            return [$queryKey, 'api_key query parameter'];
        }
        
        $altQueryKey = $request->query('key');
        if (!empty($altQueryKey)) {
            return [$altQueryKey, 'key query parameter'];
        }
        
        // Also check for API key in request body for POST/PUT requests
        if (in_array($request->method(), ['POST', 'PUT'])) {
            if ($request->has('api_key')) {
                return [$request->input('api_key'), 'api_key in request body'];
            }
        }
        
        // No key found
        return [null, 'none'];
    }

    /**
     * Log API request details including masked API key
     *
     * @param Request $request
     * @param string|null $apiKey
     * @param string $keySource
     * @return void
     */
    protected function logApiRequest(Request $request, $apiKey, $keySource)
    {
        $method = $request->method();
        $path = $request->path();
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $queryParams = json_encode($request->query());
        $contentType = $request->header('Content-Type');
        
        // Mask API key for security (show first 4 and last 4 characters)
        $maskedKey = 'none provided';
        if (!empty($apiKey)) {
            $keyLength = strlen($apiKey);
            if ($keyLength > 8) {
                $maskedKey = substr($apiKey, 0, 4) . '...' . substr($apiKey, -4);
            } else {
                $maskedKey = '****'; // For very short keys, just mask entirely
            }
        }
        
        // Create detailed log entry
        $logData = [
            'timestamp' => now()->toDateTimeString(),
            'method' => $method,
            'path' => $path,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'query_params' => $queryParams,
            'content_type' => $contentType,
            'auth' => [
                'key_source' => $keySource,
                'key_masked' => $maskedKey
            ]
        ];
        
        // Create a separate log file for API requests
        $logPath = storage_path('logs/api_requests.log');
        
        // Append to the log file
        file_put_contents(
            $logPath, 
            json_encode($logData, JSON_PRETTY_PRINT) . "\n---\n", 
            FILE_APPEND
        );
        
        // Also log a summary to the main application log
        Log::info("API Request: {$method} {$path} | Key Source: {$keySource} | IP: {$ip}");
    }
    
    /**
     * Log authentication details for diagnostics
     * 
     * @param Request $request
     * @param string $message
     * @return void
     */
    protected function logAuthDetails(Request $request, $message)
    {
        if (env('APP_DEBUG', false)) {
            $method = $request->method();
            $path = $request->path();
            $ip = $request->ip();
            $userAgent = $request->header('User-Agent');
            
            Log::info("API Auth: {$message} | {$method} {$path} | IP: {$ip} | UA: {$userAgent}");
        }
    }
}
