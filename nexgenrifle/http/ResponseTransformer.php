<?php namespace Marty\NexGenRifle\Http;

use Marty\NexGenRifle\Models\ProfileType;
use October\Rain\Exception\ApplicationException;

class ResponseTransformer
{
    protected $profileType;
    protected $structure;

    public function __construct($resource)
    {
        $this->loadProfileType($resource);
    }

    protected function loadProfileType($resource)
    {
        $this->profileType = ProfileType::where('name', 'like', '%' . str_replace('-', ' ', $resource) . '%')
            ->orWhereRaw("JSON_CONTAINS(common, '\"{$resource}\"', '$')")
            ->first();

        if (!$this->profileType) {
            throw new ApplicationException("No profile type found for resource: {$resource}");
        }

        $this->structure = $this->profileType->response_structure;
    }

    public function transform($data, $includeLinks = true)
    {
        if (!$this->structure) {
            return $data;
        }

        $transformed = $this->applyStructure($data);

        if ($includeLinks) {
            $transformed = $this->addHateoasLinks($transformed);
        }

        return $transformed;
    }

    protected function applyStructure($data)
    {
        if (is_array($data) && !isset($data['id'])) {
            return array_map(function($item) {
                return $this->transformSingle($item);
            }, $data);
        }

        return $this->transformSingle($data);
    }

    protected function transformSingle($item)
    {
        if (!is_array($item)) {
            $item = $item->toArray();
        }

        $result = [];

        foreach ($this->structure as $field => $type) {
            if (str_contains($type, '|')) {
                $types = explode('|', $type);
                foreach ($types as $t) {
                    $value = $this->extractValue($item, $field, $t);
                    if ($value !== null) {
                        $result[$field] = $value;
                        break;
                    }
                }
            } else {
                $result[$field] = $this->extractValue($item, $field, $type);
            }
        }

        return $result;
    }

    protected function extractValue($item, $field, $type)
    {
        if (!isset($item[$field])) {
            return null;
        }

        $value = $item[$field];

        switch ($type) {
            case 'integer':
                return (int)$value;
            case 'decimal':
                return (float)$value;
            case 'boolean':
                return (bool)$value;
            case 'string':
                return (string)$value;
            case 'json':
                return is_string($value) ? json_decode($value, true) : $value;
            case 'array':
                if (is_array($value)) {
                    if (isset($this->structure[$field]['items'])) {
                        return array_map(function($item) use ($field) {
                            return $this->transformArrayItem($item, $this->structure[$field]['items']);
                        }, $value);
                    }
                    return $value;
                }
                return [];
            default:
                if (is_array($type) && isset($item[$field])) {
                    return $this->transformNested($item[$field], $type);
                }
                return $value;
        }
    }

    protected function transformNested($data, $structure)
    {
        if (!$data) return null;

        $result = [];
        foreach ($structure as $field => $type) {
            if (isset($data[$field])) {
                $result[$field] = $this->extractValue($data, $field, $type);
            }
        }
        return $result;
    }

    protected function transformArrayItem($item, $structure)
    {
        $result = [];
        foreach ($structure as $field => $type) {
            if (isset($item[$field])) {
                $result[$field] = $this->extractValue($item, $field, $type);
            }
        }
        return $result;
    }

    protected function addHateoasLinks($data)
    {
        if (!isset($data['id'])) {
            return $data;
        }

        $resource = str_replace(' ', '-', strtolower($this->profileType->name));
        
        $links = [
            'self' => ['href' => url("/api/{$resource}/{$data['id']}")],
            'collection' => ['href' => url("/api/{$resource}")]
        ];

        // Add relationship links if defined
        if ($relationships = $this->profileType->relationships) {
            foreach ($relationships as $name => $relation) {
                if (isset($data[$name])) {
                    $relationType = str_replace(' ', '-', strtolower($relation['model']));
                    if (is_array($data[$name])) {
                        foreach ($data[$name] as $relatedItem) {
                            $links[$name][] = [
                                'href' => url("/api/{$relationType}/{$relatedItem['id']}")
                            ];
                        }
                    } else {
                        $links[$name] = [
                            'href' => url("/api/{$relationType}/{$data[$name]['id']}")
                        ];
                    }
                }
            }
        }

        $data['_links'] = $links;
        return $data;
    }

    public function getRequiredFields()
    {
        return $this->profileType->required_fields ?? [];
    }

    public function validateData($data)
    {
        $required = $this->getRequiredFields();
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                throw new ApplicationException("Missing required field: {$field}");
            }
        }

        return true;
    }
}
