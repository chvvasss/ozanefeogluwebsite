<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ContactMessage;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Prune contact messages whose retention window has elapsed.
 *
 * Each ContactMessage is created with `retention_expires_at = now()+90d`
 * (default) by ContactController. This command, scheduled daily, deletes
 * any row past that mark — fulfilling KVKK m.7 (depersonalisation /
 * deletion when the processing purpose is exhausted).
 *
 * Run `--dry-run` to see the impact without writing.
 */
#[Signature('contact:prune-expired {--dry-run : Report only, do not delete}')]
#[Description('Delete contact messages past their retention window (KVKK m.7).')]
class PruneExpiredContactMessages extends Command
{
    public function handle(): int
    {
        $now = now();

        $query = ContactMessage::query()->whereNotNull('retention_expires_at')->where('retention_expires_at', '<=', $now);
        $count = $query->count();

        if ($count === 0) {
            $this->info('No expired contact messages.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->warn("[dry-run] Would delete {$count} expired contact message(s).");

            return self::SUCCESS;
        }

        $deleted = $query->delete();
        $this->info("Pruned {$deleted} expired contact message(s).");

        return self::SUCCESS;
    }
}
