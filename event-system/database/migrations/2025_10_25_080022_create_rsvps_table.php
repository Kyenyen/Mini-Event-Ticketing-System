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
        Schema::create('rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('seat_id')->nullable()->constrained()->onDelete('set null');

            // Guest-only fields (used when user_id is null)
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();

            $table->enum('status', ['confirmed', 'canceled'])->default('confirmed');
            $table->timestamps();

            // One RSVP per user or guest email per event
            $table->unique(['event_id', 'user_id']);
            $table->unique(['event_id', 'guest_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsvps');
    }
};
