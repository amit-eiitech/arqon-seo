<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tableName = config('arqon-seo.table', 'seo_metadata');

        if (Schema::hasTable($tableName)) {
            return;
        }

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('seoable');
            $table->string('route_name')->nullable()->unique();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->json('robots')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
            $table->unique(['seoable_type', 'seoable_id'], 'arqon_seo_seoable_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('arqon-seo.table', 'seo_metadata'));
    }
};
