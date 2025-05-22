<?php namespace Marty\NexGenRifle\Models;

use Model;
use Marty\NexGenRifle\Models\BuildCategory;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'marty_nexgenrifle_settings';

    public $settingsFields = 'fields.yaml';

    protected $cache = [];

    public $rules = [
        'affiliate_enabled' => 'boolean',
        'default_build_category' => 'exists:marty_nexgenrifle_build_categories,id'
    ];

    protected $fillable = [
        'affiliate_enabled',
        'default_build_category',
        'notification_email',
        'build_options',
        'custom_css',
        'tracking_code'
    ];

    protected $casts = [
        'affiliate_enabled' => 'boolean',
        'build_options' => 'json'
    ];

    public function listBuildCategories()
    {
        return BuildCategory::lists('name', 'id');
    }
}
