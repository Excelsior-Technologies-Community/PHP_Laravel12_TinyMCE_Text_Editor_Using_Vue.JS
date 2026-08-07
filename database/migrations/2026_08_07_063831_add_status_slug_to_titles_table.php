<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void
    {
        Schema::table('title', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('description');
            $table->string('slug')->unique()->nullable()->after('status');
            $table->softDeletes()->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('titles', function (Blueprint $table) {
            //
        });
    }
};
