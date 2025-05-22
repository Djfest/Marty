<?php
namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use Marty\Nexgenrifle\Models\ProfileType;
use DB;

class AddAiEnabledToProfileTypes extends Migration
{
    public function up()
    {
        // Get all profile types
        $profileTypes = ProfileType::all();

        foreach ($profileTypes as $profileType) {
            // Get existing metadata or initialize empty array
            $metadata = $profileType->metadata ?? [];
            
            // Add ai_enabled field if it doesn't exist
            if (!isset($metadata['ai_enabled'])) {
                $metadata['ai_enabled'] = false;
            }

            // Update the record
            $profileType->metadata = $metadata;
            $profileType->save();
        }
    }

    public function down()
    {
        // Get all profile types
        $profileTypes = ProfileType::all();

        foreach ($profileTypes as $profileType) {
            // Get existing metadata
            $metadata = $profileType->metadata ?? [];
            
            // Remove ai_enabled field if it exists
            if (isset($metadata['ai_enabled'])) {
                unset($metadata['ai_enabled']);
            }

            // Update the record
            $profileType->metadata = $metadata;
            $profileType->save();
        }
    }
}
