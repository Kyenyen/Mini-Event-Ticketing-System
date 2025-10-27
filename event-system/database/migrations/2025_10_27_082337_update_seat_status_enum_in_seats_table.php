<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement("ALTER TABLE seats MODIFY COLUMN status ENUM('available', 'processing', 'booked', 'blocked') DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement("ALTER TABLE seats MODIFY COLUMN status ENUM('available', 'booked', 'blocked') DEFAULT 'available'");
    }
};
