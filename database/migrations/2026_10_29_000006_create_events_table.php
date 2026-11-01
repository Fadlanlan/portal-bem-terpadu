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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->string('category', 64)->index();
            $table->text('summary')->nullable();
            $table->text('description');
            $table->string('location', 255);
            $table->dateTime('started_at')->index();
            $table->dateTime('ended_at')->index();
            $table->string('organizer', 150);
            $table->string('registration_url', 255)->nullable();
            $table->boolean('is_registration_open')->default(false);
            $table->text('speakers')->nullable();
            $table->text('agenda_points')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->timestamps();

            $table->index(['status', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
