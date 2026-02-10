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
        Schema::create('newsletter_campaign_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->timestamp('sent_at');
            $table->boolean('opened')->default(false);
            $table->boolean('clicked')->default(false);
            $table->timestamps();

            $table->index(['campaign_id', 'subscriber_id']);
            $table->index('sent_at');

            $table->foreign('campaign_id')->references('id')->on('newsletter_campaigns')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('id')->on('newsletters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_campaign_logs');
    }
};
