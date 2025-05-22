<?php
namespace Marty\AiSolutions\Services;

use Marty\NexGenRifle\Models\ProfileType;
use October\Rain\Database\Model;
use Log;

class OntologySyncService
{
    /**
     * Sync specific models by profile type IDs
     * @param array $profileTypeIds Array of profile type IDs to sync
     * @return array Results of the sync operation
     */
    public static function syncSpecific($profileTypeIds)
    {
        $results = [];

        try {
            $profileTypes = ProfileType::whereIn('id', $profileTypeIds)->get();

            foreach ($profileTypes as $profileType) {
                $results[] = self::syncProfileType($profileType);
            }
        } catch (\Exception $e) {
            Log::error('AI Ontology Sync failed: ' . $e->getMessage());
            throw $e;
        }

        return $results;
    }

    /**
     * Sync all models with AI analysis capability
     */
    public static function syncAll()
    {
        $results = [];

        try {
            // Get all AI-enabled profile types
            $profileTypes = ProfileType::aiEnabled()->get();

            foreach ($profileTypes as $profileType) {
                $results[] = self::syncProfileType($profileType);
            }
        } catch (\Exception $e) {
            Log::error('AI Ontology Sync failed: ' . $e->getMessage());
            throw $e;
        }

        return $results;
    }

    /**
     * Sync a single profile type
     * @param ProfileType $profileType The profile type to sync
     * @return array Result of the sync operation
     */
    protected static function syncProfileType($profileType)
    {
        $modelClass = $profileType->model_class;

        if (!class_exists($modelClass)) {
            return [
                'model' => $modelClass,
                'status' => 'error',
                'message' => 'Model class not found',
                'profile_type_id' => $profileType->id,
                'ai_enabled' => $profileType->ai_enabled
            ];
        }

        try {
            $model = new $modelClass;

            // Get model metadata
            $metadata = [
                'fields' => self::getModelFields($model),
                'relationships' => self::getModelRelationships($model),
                'rules' => self::getModelRules($model)
            ];

            // Store metadata in ProfileType
            $profileType->metadata = array_merge(
                $profileType->metadata ?? [],
                ['ai_metadata' => $metadata]
            );
            $profileType->save();

            return [
                'model' => $modelClass,
                'status' => 'success',
                'message' => 'Synced successfully',
                'profile_type_id' => $profileType->id,
                'ai_enabled' => $profileType->ai_enabled
            ];

        } catch (\Exception $e) {
            return [
                'model' => $modelClass,
                'status' => 'error',
                'message' => $e->getMessage(),
                'profile_type_id' => $profileType->id,
                'ai_enabled' => $profileType->ai_enabled
            ];
        }
    }

    /**
     * Get model field definitions
     */
    protected static function getModelFields(Model $model)
    {
        $fields = [];

        // Get fillable fields
        foreach ($model->getFillable() as $field) {
            $fields[$field] = [
                'type' => 'field',
                'fillable' => true
            ];
        }

        // Get casts
        foreach ($model->getCasts() as $field => $type) {
            if (isset($fields[$field])) {
                $fields[$field]['cast'] = $type;
            }
        }

        // Get dates
        foreach ($model->getDates() as $field) {
            if (isset($fields[$field])) {
                $fields[$field]['date'] = true;
            }
        }

        return $fields;
    }

    /**
     * Get model relationship definitions
     */
    protected static function getModelRelationships(Model $model)
    {
        $relationships = [];

        foreach ($model->getRelationDefinitions() as $name => $definition) {
            $relationships[$name] = [
                'type' => $definition[0] ?? null,
                'model' => $definition[1] ?? null,
                'key' => $definition[2] ?? null
            ];
        }

        return $relationships;
    }

    /**
     * Get model validation rules
     */
    protected static function getModelRules(Model $model)
    {
        return $model->rules ?? [];
    }
}
