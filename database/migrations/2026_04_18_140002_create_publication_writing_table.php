<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publication_writing', function (Blueprint $table): void {
            $table->foreignId('writing_id')->constrained('writings')->cascadeOnDelete();
            $table->foreignId('publication_id')->constrained('publications')->cascadeOnDelete();
            $table->string('link', 255)->nullable();
            $table->timestamps();

            $table->primary(['writing_id', 'publication_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_writing');
    }
};
