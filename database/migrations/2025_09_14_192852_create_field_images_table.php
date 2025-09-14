<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('field_images', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AI
            $table->foreignId('sports_field_id')->constrained('sports_fields')->cascadeOnDelete();
            $table->string('path', 255);
            $table->boolean('is_cover')->default(false);
            $table->timestamps();

            $table->index('sports_field_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('field_images');
    }
};
