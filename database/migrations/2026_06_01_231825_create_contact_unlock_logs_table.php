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
        Schema::create('contact_unlock_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('unlockable'); // unlockable_type, unlockable_id
            
            $table->string('viewer_name');
            $table->string('viewer_mobile');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('source_page')->nullable();
            
            $table->timestamp('otp_sent_at')->nullable();
            $table->timestamp('otp_verified_at')->nullable();
            $table->timestamp('contact_revealed_at')->nullable();
            
            $table->unsignedTinyInteger('otp_attempts')->default(0);
            $table->unsignedTinyInteger('resend_count')->default(0);
            
            $table->enum('status', ['pending', 'otp_sent', 'verified', 'failed', 'expired', 'rate_limited'])->default('pending');
            $table->string('revealed_contact_last4', 4)->nullable();
            
            $table->timestamps();

            // Indexes (morphs automatically adds index for unlockable_type + unlockable_id)
            $table->index('viewer_mobile');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_unlock_logs');
    }
};
