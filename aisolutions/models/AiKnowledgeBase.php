<?php namespace Marty\AiSolutions\Models;

use Model;

class AiKnowledgeBase extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_aisolutions_knowledge_base';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'title',
        'content',
        'keywords',
        'profile_type_id',
        'external_id',
        'user_id'
    ];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'title' => 'required',
        'content' => 'required'
        // Note: user_id and external_id are intentionally not required
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'profile_type' => ['Marty\Nexgenrifle\Models\ProfileType'],
        'user' => ['Backend\Models\User']
    ];
    
    /**
     * Search for knowledge base entries by keywords
     */
    public function scopeSearchKeywords($query, $keywords)
    {
        if (!$keywords) {
            return $query;
        }
        
        return $query->where(function($q) use ($keywords) {
            $q->where('keywords', 'like', "%$keywords%")
              ->orWhere('title', 'like', "%$keywords%")
              ->orWhere('content', 'like', "%$keywords%");
        });
    }
}
