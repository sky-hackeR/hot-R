<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('swipers', function (Blueprint $table) {
            $table->json('slides_text')->nullable()->after('video_url');
        });
    }

    public function down(): void
    {
        Schema::table('swipers', function (Blueprint $table) {
            $table->dropColumn('slides_text');
        });
    }
};
