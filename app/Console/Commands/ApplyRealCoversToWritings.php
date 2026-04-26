<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Photo;
use App\Models\Writing;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Replace generated demo covers on writings with real AA photos
 * (sourced from the imported Photo archive). Round-robin assignment.
 */
#[Signature('aa:apply-covers {--limit=0 : Cap number of writings updated}')]
#[Description('Apply real AA photos as covers to existing writings.')]
class ApplyRealCoversToWritings extends Command
{
    public function handle(): int
    {
        $writings = Writing::query()->orderBy('id')->get();
        $limit = (int) $this->option('limit');

        if ($limit > 0) {
            $writings = $writings->take($limit);
        }

        $photos = Photo::query()
            ->published()
            ->where('source', 'AA')
            ->whereHas('media', fn ($q) => $q->where('collection_name', 'image'))
            ->orderBy('id')
            ->get();

        if ($photos->isEmpty()) {
            $this->error('No AA photos available; run aa:import-photos first.');

            return self::FAILURE;
        }

        $this->info(sprintf('Applying real covers from %d AA photos to %d writings', $photos->count(), $writings->count()));

        $applied = 0;
        foreach ($writings as $i => $writing) {
            $photo = $photos[$i % $photos->count()];
            $media = $photo->getFirstMedia('image');

            if (! $media) {
                continue;
            }

            $sourcePath = $media->getPath();
            if (! is_file($sourcePath)) {
                $this->warn("  ⚠ {$writing->title} → source missing");

                continue;
            }

            $writing->clearMediaCollection('cover');
            $writing->addMedia($sourcePath)
                ->preservingOriginal()
                ->usingName($writing->getTranslation('slug', 'tr'))
                ->usingFileName($writing->getTranslation('slug', 'tr').'.jpg')
                ->toMediaCollection('cover');

            $applied++;
            $this->line('  ✓ '.$writing->title.' ← '.$photo->getTranslationWithFallback('title'));
        }

        $this->newLine();
        $this->info(sprintf('Applied %d covers.', $applied));

        return self::SUCCESS;
    }
}
