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
    Schema::table('datasets', function (Blueprint $table) {
        $table->longText('ai_insight')->nullable()->after('column_count');
        $table->longText('ai_chart_config')->nullable()->after('ai_insight');
    });
}

public function down(): void
{
    Schema::table('datasets', function (Blueprint $table) {
        $table->dropColumn(['ai_insight', 'ai_chart_config']);
    });
}
};
