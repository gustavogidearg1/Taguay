<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->unsignedBigInteger('cultivo_id')->nullable()->change();
            $table->decimal('hectareas', 10, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->unsignedBigInteger('cultivo_id')->nullable(false)->change();
            $table->decimal('hectareas', 10, 2)->nullable(false)->change();
        });
    }
};
