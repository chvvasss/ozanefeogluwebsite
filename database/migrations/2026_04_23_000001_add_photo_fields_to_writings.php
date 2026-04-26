<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase A blocker — writings table extensions:
 * - photo_credit (dynamic per-item override; falls back to
 *   config('site.default_photo_credit') = 'Foto: Ozan Efeoğlu / AA')
 * - cover_caption (JSON translatable; full editorial caption separate from body)
 * - is_demo (seed-data banner flag)
 * - hero_eligible (admin opt-in for hero rotation pool)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('writings', function (Blueprint $table): void {
            $table->string('photo_credit', 160)->nullable()->after('cover_hue_b');
            $table->json('cover_caption')->nullable()->after('photo_credit');
            $table->boolean('is_demo')->default(false)->index()->after('is_featured');
            $table->boolean('hero_eligible')->default(false)->index()->after('is_demo');
        });
    }

    public function down(): void
    {
        Schema::table('writings', function (Blueprint $table): void {
            $table->dropColumn(['photo_credit', 'cover_caption', 'is_demo', 'hero_eligible']);
        });
    }
};
