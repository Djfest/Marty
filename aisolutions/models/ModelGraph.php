<?php namespace Marty\AiSolutions\Models;

use Model;

class ModelGraph extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_aisolutions_model_graphs';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'title',
        'description',
        'graph_data',
        'profile_type_id'
    ];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'title' => 'required',
        'graph_data' => 'required'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'profile_type' => ['Marty\Nexgenrifle\Models\ProfileType']
    ];

    /**
     * Get graph_data attribute
     */
    public function getGraphDataAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Set graph_data attribute
     */
    public function setGraphDataAttribute($value)
    {
        $this->attributes['graph_data'] = is_array($value) ? json_encode($value) : $value;
    }
}
