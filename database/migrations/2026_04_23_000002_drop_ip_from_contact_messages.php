<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase A blocker — drop IP / User-Agent from contact_messages.
 *
 * Source protection absolutism per Owner decision: a journalist's site
 * must not persist IP or UA; subpoena scenarios expose sources. Rate
 * limiting is handled at the route layer (throttle middleware) in-memory;
 * no DB persistence of client fingerprint.
 *
 * Also adds `retention_expires_at` timestamp — messages older than this
 * are auto-purged by a scheduled job (to be wired in Phase E).
 *
 * Source refs:
 * - KVKK m.4 (ölçülülük) + Kurul 2020/649 (IP = kişisel veri)
 * - Basın Konseyi m.10 (kaynak gizliliği)
 * - CMK m.167 (gazeteci tanıklık çekilme hakkı)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->dropColumn(['ip_address', 'user_agent']);
            $table->timestamp('retention_expires_at')->nullable()->index()->after('read_at');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->dropColumn('retention_expires_at');
        });
    }
};
