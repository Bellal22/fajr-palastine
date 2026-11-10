<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeRegionIdNullableInLocationsTable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE locations MODIFY region_id BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE locations MODIFY region_id BIGINT UNSIGNED NOT NULL');
    }
}

