<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table): void {
            $table->id();

            // Translatable via Spatie\Translatable — stored as JSON arrays.
            $table->json('title');
            $table->json('caption')->nullable();
            $table->json('alt_text')->nullable();

            // Slug is also translatable so we can have tr/en variants; admin
            // will auto-derive from title on tr if blank.
            $table->json('slug');

            // Legal + editorial metadata
            $table->string('credit', 160)->nullable();
            $table->string('source', 120)->nullable();            // AA, personal, assignment, freelance, etc.
            $table->string('license', 80)->nullable();            // editorial-only | cc-by | all-rights-reserved
            $table->string('rights_notes', 300)->nullable();
            $table->string('location', 160)->nullable();
            $table->timestamp('captured_at')->nullable()->index();

            // Classification — enum kept loose as string for future growth.
            $table->string('kind', 40)->default('reportage')->index();

            // Display flags
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('hero_eligible')->default(false)->index();
            $table->boolean('is_demo')->default(false)->index();

            // Optional relationship: one photo may illustrate a writing.
            $table->foreignId('writing_id')->nullable()
                ->constrained('writings')
                ->nullOnDelete();

            // Sort ordering (manual drag-handle later)
            $table->unsignedInteger('sort_order')->default(0)->index();

            // Audit
            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Compound indices for common public queries
            $table->index(['is_published', 'captured_at']);
            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
