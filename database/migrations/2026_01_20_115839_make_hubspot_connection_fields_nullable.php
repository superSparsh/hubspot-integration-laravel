<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Make HubSpot OAuth fields nullable so we can create a connection record
     * before the HubSpot OAuth flow completes (e.g., when saving WAPAPP API token first)
     */
    public function up(): void
    {
        // Drop unique constraint on hubspot_portal_id (it needs to be nullable for new records)
        DB::statement('ALTER TABLE hubspot_connections DROP INDEX hubspot_connections_hubspot_portal_id_unique');
        
        // Make fields nullable
        DB::statement('ALTER TABLE hubspot_connections MODIFY hubspot_portal_id VARCHAR(255) NULL');
        DB::statement('ALTER TABLE hubspot_connections MODIFY access_token TEXT NULL');
        DB::statement('ALTER TABLE hubspot_connections MODIFY refresh_token TEXT NULL');
        DB::statement('ALTER TABLE hubspot_connections MODIFY expires_at TIMESTAMP NULL');
        
        // Re-add unique index (allows nulls in MySQL)
        DB::statement('ALTER TABLE hubspot_connections ADD UNIQUE INDEX hubspot_connections_hubspot_portal_id_unique (hubspot_portal_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive change - records with NULL values would fail
        // Only reverse if no NULL values exist
    }
};
