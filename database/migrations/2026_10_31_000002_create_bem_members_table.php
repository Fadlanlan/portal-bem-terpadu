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
        Schema::create('bem_members', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('position', 120);
            $table->string('department', 120)->nullable();
            $table->string('level', 50)->default('staff')->index();
            $table->string('period', 50)->default('2026/2027');
            $table->string('photo_path', 255)->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();

            $table->index(['period', 'is_published', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bem_members');
    }
};
