<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('writings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('kind', ['saha_yazisi', 'roportaj', 'deneme', 'not'])->default('deneme')->index();
            $table->enum('status', ['draft', 'scheduled', 'published'])->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();

            $table->string('location', 120)->nullable();

            // Translatable JSON fields (tr + en)
            $table->json('title');
            $table->json('slug');
            $table->json('excerpt');
            $table->json('body');
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            $table->string('canonical_url', 255)->nullable();

            $table->unsignedSmallInteger('read_minutes')->default(1);
            $table->unsignedTinyInteger('cover_hue_a')->default(20);
            $table->unsignedTinyInteger('cover_hue_b')->default(200);

            $table->boolean('is_featured')->default(false)->index();
            $table->integer('sort_order')->default(0)->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('writings');
    }
};
