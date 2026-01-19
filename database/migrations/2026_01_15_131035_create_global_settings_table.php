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
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            
            // Site Information
            $table->string('site_name')->default('SMK RPL');
            $table->string('site_tagline')->nullable();
            
            // Logos and Images
            $table->string('logo_primary')->nullable();
            $table->string('logo_secondary')->nullable();
            $table->string('favicon')->nullable();
            
            // Theme Colors
            $table->string('primary_color')->default('rgb(18,180,224)');
            $table->string('secondary_color')->default('rgb(255,255,255)');
            
            // Contact Information
            $table->text('footer_text')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->text('address')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_settings');
    }
};
