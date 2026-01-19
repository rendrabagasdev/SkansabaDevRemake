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
        Schema::table('global_settings', function (Blueprint $table) {
            $table->string('whatsapp', 30)->nullable()->after('contact_phone');
            $table->string('facebook')->nullable()->after('address');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('twitter')->nullable()->after('instagram');
            $table->string('youtube')->nullable()->after('twitter');
            $table->string('linkedin')->nullable()->after('youtube');
            $table->string('tiktok')->nullable()->after('linkedin');
            $table->text('maps_url')->nullable()->after('tiktok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp',
                'facebook',
                'instagram',
                'twitter',
                'youtube',
                'linkedin',
                'tiktok',
                'maps_url'
            ]);
        });
    }
};
