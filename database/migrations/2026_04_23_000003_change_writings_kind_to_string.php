<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Phase A — change writings.kind from enum to free string(32).
 *
 * New taxonomy (Content Agent + Owner approval):
 *   foto_notu · editoryal · analiz · roportaj · deneme · not
 *
 * The old `saha_yazisi` kind is kept readable for legacy data.
 * Once taxonomy becomes admin-editable (Phase B.1 SettingsRepository,
 * writings.kinds JSON), this column will stay string — validation
 * moves to application layer.
 *
 * SQLite: CHECK constraint is rebuilt by Laravel schema grammar via
 * table recreation. On MySQL/PG the ENUM → VARCHAR(32) is a simple
 * ALTER COLUMN.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite: rebuild table without CHECK constraint via raw SQL.
            // Laravel's ->change() support in SQLite is uneven; this approach
            // mirrors the standard SQLite "12-step" column alteration.
            DB::statement('PRAGMA foreign_keys = OFF');

            // 1. Create new table with desired schema
            DB::statement('CREATE TABLE writings__new AS SELECT * FROM writings');
            DB::statement('DROP TABLE writings');

            Schema::create('writings', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('kind', 32)->default('deneme')->index();
                $table->string('status', 20)->default('draft')->index();
                $table->timestamp('published_at')->nullable()->index();
                $table->string('location', 120)->nullable();
                $table->json('title');
                $table->json('slug');
                $table->json('excerpt')->nullable();
                $table->json('body')->nullable();
                $table->json('meta_title')->nullable();
                $table->json('meta_description')->nullable();
                $table->string('canonical_url', 255)->nullable();
                $table->unsignedSmallInteger('read_minutes')->default(1);
                $table->unsignedSmallInteger('cover_hue_a')->nullable();
                $table->unsignedSmallInteger('cover_hue_b')->nullable();
                $table->boolean('is_featured')->default(false)->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->string('photo_credit', 160)->nullable();
                $table->json('cover_caption')->nullable();
                $table->boolean('is_demo')->default(false)->index();
                $table->boolean('hero_eligible')->default(false)->index();
                $table->timestamps();
                $table->softDeletes();
            });

            DB::statement('INSERT INTO writings SELECT * FROM writings__new');
            DB::statement('DROP TABLE writings__new');

            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            Schema::table('writings', function (Blueprint $table): void {
                $table->string('kind', 32)->default('deneme')->change();
            });
        }
    }

    public function down(): void
    {
        // Non-reversible: old enum set excluded foto_notu / editoryal / analiz.
        // Rollback is not supported; re-seeding requires kind_string intact.
    }
};
