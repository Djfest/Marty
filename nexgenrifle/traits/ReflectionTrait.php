<?php namespace Marty\NexGenRifle\Traits;

use ReflectionClass;
use ReflectionProperty;
use ReflectionMethod;
use Exception;

trait ReflectionTrait
{
    /**
     * Cache for reflection data
     */
    protected static $reflectionCache = [];

    /**
     * Get the class reflection instance
     * 
     * @return ReflectionClass
     */
    public function getReflection()
    {
        $className = get_class($this);
        
        if (!isset(static::$reflectionCache[$className])) {
            static::$reflectionCache[$className] = new ReflectionClass($className);
        }
        
        return static::$reflectionCache[$className];
    }
    
    /**
     * Get all properties with their metadata
     * 
     * @param int $filter Optional filter (ReflectionProperty::IS_PUBLIC, etc.)
     * @return array
     */
    public function getPropertyMetadata($filter = null)
    {
        $reflection = $this->getReflection();
        $properties = $filter ? $reflection->getProperties($filter) : $reflection->getProperties();
        
        $metadata = [];
        foreach ($properties as $property) {
            $metadata[$property->getName()] = [
                'name' => $property->getName(),
                'visibility' => $this->getPropertyVisibility($property),
                'docComment' => $property->getDocComment(),
                'isStatic' => $property->isStatic(),
                'attributes' => $this->getPropertyAttributes($property)
            ];
        }
        
        return $metadata;
    }
    
    /**
     * Get method metadata
     * 
     * @param int $filter Optional filter (ReflectionMethod::IS_PUBLIC, etc.)
     * @return array
     */
    public function getMethodMetadata($filter = null)
    {
        $reflection = $this->getReflection();
        $methods = $filter ? $reflection->getMethods($filter) : $reflection->getMethods();
        
        $metadata = [];
        foreach ($methods as $method) {
            $metadata[$method->getName()] = [
                'name' => $method->getName(),
                'visibility' => $this->getMethodVisibility($method),
                'docComment' => $method->getDocComment(),
                'isStatic' => $method->isStatic(),
                'parameters' => $this->getMethodParameters($method),
                'returnType' => $method->hasReturnType() ? (string)$method->getReturnType() : null
            ];
        }
        
        return $metadata;
    }
    
    /**
     * Get property attributes (PHP 8+)
     * 
     * @param ReflectionProperty $property
     * @return array
     */
    protected function getPropertyAttributes($property)
    {
        if (PHP_VERSION_ID < 80000) {
            return [];
        }
        
        $attributes = [];
        foreach ($property->getAttributes() as $attribute) {
            $attributes[] = [
                'name' => $attribute->getName(),
                'arguments' => $attribute->getArguments()
            ];
        }
        
        return $attributes;
    }
    
    /**
     * Get method parameter details
     * 
     * @param ReflectionMethod $method
     * @return array
     */
    protected function getMethodParameters($method)
    {
        $parameters = [];
        foreach ($method->getParameters() as $param) {
            $parameters[$param->getName()] = [
                'name' => $param->getName(),
                'type' => $param->hasType() ? (string)$param->getType() : null,
                'isOptional' => $param->isOptional(),
                'hasDefaultValue' => $param->isDefaultValueAvailable(),
                'defaultValue' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null
            ];
        }
        
        return $parameters;
    }
    
    /**
     * Get property visibility
     * 
     * @param ReflectionProperty $property
     * @return string
     */
    protected function getPropertyVisibility($property)
    {
        if ($property->isPublic()) return 'public';
        if ($property->isProtected()) return 'protected';
        if ($property->isPrivate()) return 'private';
        return 'unknown';
    }
    
    /**
     * Get method visibility
     * 
     * @param ReflectionMethod $method
     * @return string
     */
    protected function getMethodVisibility($method)
    {
        if ($method->isPublic()) return 'public';
        if ($method->isProtected()) return 'protected';
        if ($method->isPrivate()) return 'private';
        return 'unknown';
    }
    
    /**
     * Get model schema information
     * 
     * @return array
     */
    public function getSchemaInfo()
    {
        $schema = [
            'table' => $this->getTable(),
            'primaryKey' => $this->getKeyName(),
            'connection' => $this->getConnectionName(),
            'fillable' => $this->getFillable(),
            'guarded' => $this->getGuarded(),
            'dates' => $this->getDates(),
            'casts' => $this->getCasts(),
        ];
        
        if (property_exists($this, 'jsonable')) {
            $schema['jsonable'] = $this->jsonable;
        }
        
        if (property_exists($this, 'hidden')) {
            $schema['hidden'] = $this->hidden;
        }
        
        if (property_exists($this, 'visible')) {
            $schema['visible'] = $this->visible;
        }
        
        if (property_exists($this, 'appends')) {
            $schema['appends'] = $this->appends;
        }
        
        if (property_exists($this, 'rules')) {
            $schema['rules'] = $this->rules;
        }
        
        return $schema;
    }
    
    /**
     * Get related models metadata
     * 
     * @return array
     */
    public function getRelationships()
    {
        $relationships = [];
        $reflection = $this->getReflection();
        
        // Relationship types to check for
        $relationshipTypes = [
            'hasOne', 'hasMany', 'belongsTo', 'belongsToMany',
            'morphTo', 'morphOne', 'morphMany', 'morphToMany',
            'attachOne', 'attachMany'
        ];
        
        foreach ($relationshipTypes as $type) {
            if (property_exists($this, $type)) {
                $relationships[$type] = $this->$type;
            }
        }
        
        return $relationships;
    }
}
