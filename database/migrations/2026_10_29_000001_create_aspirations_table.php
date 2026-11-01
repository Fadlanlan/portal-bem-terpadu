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
        Schema::create('aspirations', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 64)->unique();
            $table->string('tracking_code_hash');
            $table->string('category', 64)->index();
            $table->string('subject', 200);
            $table->text('message');
            $table->boolean('is_anonymous')->default(false)->index();
            $table->string('reporter_name')->nullable();
            $table->string('reporter_email')->nullable();
            $table->string('reporter_student_id')->nullable();
            $table->string('status', 32)->default('submitted')->index();
            $table->text('admin_response')->nullable();
            $table->timestamp('submitted_at')->index();
            $table->timestamp('resolved_at')->nullable()->index();
            $table->timestamps();

            $table->index(['status', 'submitted_at']);
            $table->index(['category', 'submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspirations');
    }
};
