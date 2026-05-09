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
        Schema::create('featured_listings', function (Blueprint $table) {
            $table->id();
            $table->morphs('listable'); // Car or CustomerCarListing
            $table->morphs('user');     // Dealer or Customer
            $table->foreignId('featured_plan_id')->constrained('featured_plans')->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_listings');
    }
};
