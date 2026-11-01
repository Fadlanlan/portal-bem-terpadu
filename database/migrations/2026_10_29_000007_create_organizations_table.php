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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->index();
            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->string('level', 150)->nullable();
            $table->string('role_type', 150)->nullable();
            $table->text('description');
            $table->string('leader_name', 150)->nullable();
            $table->string('period', 100)->nullable();
            $table->string('logo', 255)->nullable();
            $table->text('functions')->nullable();
            $table->text('structure_overview')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->string('status', 32)->default('published')->index();
            $table->timestamps();

            $table->index(['status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
