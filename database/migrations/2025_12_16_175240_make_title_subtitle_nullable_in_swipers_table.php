<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('swipers', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('subtitle')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('swipers', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->string('subtitle')->nullable(false)->change();
        });
    }
};
