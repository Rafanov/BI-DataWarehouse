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
    Schema::create('chart_configs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dataset_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->string('chart_type'); // bar, line, pie, doughnut
        $table->string('x_column');
        $table->string('y_column');
        $table->integer('limit')->default(10);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('chart_configs');
}
};
