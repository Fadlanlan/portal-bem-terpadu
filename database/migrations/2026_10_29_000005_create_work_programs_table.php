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
        Schema::create('work_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->string('organization', 150);
            $table->string('department', 150)->nullable();
            $table->string('status', 64)->default('Rencana');
            $table->string('publication_status', 32)->default('draft')->index();
            $table->string('period', 100)->nullable();
            $table->text('description');
            $table->text('objectives')->nullable();
            $table->text('targets')->nullable();
            $table->text('indicators')->nullable();
            $table->string('timeline', 150)->nullable();
            $table->string('pic', 150)->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();

            $table->index(['publication_status', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_programs');
    }
};
