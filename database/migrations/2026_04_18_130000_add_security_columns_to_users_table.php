<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('locale', 5)->default('tr')->after('email_verified_at');
            $table->timestamp('last_login_at')->nullable()->after('locale');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->timestamp('locked_until')->nullable()->after('last_login_ip');
            $table->unsignedSmallInteger('failed_attempts')->default(0)->after('locked_until');
            $table->timestamp('password_changed_at')->nullable()->after('failed_attempts');
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'locale',
                'last_login_at',
                'last_login_ip',
                'locked_until',
                'failed_attempts',
                'password_changed_at',
            ]);
        });
    }
};
