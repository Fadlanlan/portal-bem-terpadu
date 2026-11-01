<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('identity_mode', 20)->default('account'); // account|anonymous
            $table->string('anonymous_token_hash', 64)->nullable()->unique();
            $table->string('status', 20)->default('open'); // open|handoff|closed
            $table->string('handler_mode', 20)->default('ai'); // ai|human
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('unread_admin_count')->default(0);
            $table->unsignedInteger('unread_user_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
