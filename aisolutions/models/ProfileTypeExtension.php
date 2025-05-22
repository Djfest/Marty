<?php namespace Marty\AiSolutions\Models;

use Model;

class ProfileTypeExtension extends Model
{
    public $table = 'marty_aisolutions_profile_type_extensions';
    
    protected $fillable = [
        'profile_type_id',
        'ai_enabled',
        'metadata',
        'settings',
        'description'
    ];

    protected $jsonable = [
        'metadata',
        'settings'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $belongsTo = [
        'profile_type' => ['Marty\Djfest\Models\ProfileType']
    ];
}
