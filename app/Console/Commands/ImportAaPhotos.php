<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * ImportAaPhotos — bulk-import the real AA archive into the Photo model.
 *
 * Source filenames follow the pattern:
 *   YYYYMMDD_2_XXXXXX_YYYYYY_Orjinal_Logolu.jpg
 *
 * The leading 8-digit token is the capture date — we parse it and use
 * it for `captured_at`. Slug is `aa-YYYYMMDD-{tail}` to avoid collisions
 * for same-day shoots.
 *
 * Idempotent: re-running skips files already in DB (matched by slug).
 */
#[Signature('aa:import-photos {--source= : Absolute path to .jpg dir (defaults to AA_PHOTO_SOURCE env)} {--purge-demo : Remove existing demo photos before import} {--limit=0 : Cap number of files imported (0 = all)} {--featured=4 : First N photos marked featured + hero_eligible}')]
#[Description('Import the real AA photo archive into the Photo model.')]
class ImportAaPhotos extends Command
{
    public function handle(): int
    {
        $source = (string) ($this->option('source') ?: env('AA_PHOTO_SOURCE', ''));
        $limit = (int) $this->option('limit');
        $featuredCount = (int) $this->option('featured');

        if ($source === '' || ! is_dir($source)) {
            $this->error('Source directory not found.');
            $this->line('  Pass --source=/path or set AA_PHOTO_SOURCE in .env');

            return self::FAILURE;
        }

        if ($this->option('purge-demo')) {
            $this->info('Purging existing demo photos...');
            $deleted = 0;
            foreach (Photo::query()->where('is_demo', true)->withTrashed()->get() as $p) {
                $p->clearMediaCollection('image');
                $p->forceDelete();
                $deleted++;
            }
            $this->line("  ✓ {$deleted} demo photos removed");
        }

        $files = glob(rtrim($source, '/\\').DIRECTORY_SEPARATOR.'*.jpg') ?: [];
        sort($files);

        if ($limit > 0) {
            $files = array_slice($files, 0, $limit);
        }

        if ($files === []) {
            $this->error('No .jpg files in source directory.');

            return self::FAILURE;
        }

        $creator = User::query()->orderBy('id')->first();
        if (! $creator) {
            $this->error('No user to own imports.');

            return self::FAILURE;
        }

        $defaultCredit = (string) site_setting('photo.default_credit', 'Foto: Ozan Efeoğlu / AA');

        $this->info(sprintf('Importing %d photos from %s', count($files), $source));
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $imported = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($files as $i => $path) {
            $base = pathinfo($path, PATHINFO_FILENAME);
            $captured = $this->extractDate($base) ?? Carbon::now();

            $tokens = explode('_', $base);
            $tail = end($tokens) ?: (string) $i;
            $slug = 'aa-'.$captured->format('Ymd').'-'.Str::lower($tail);

            $existing = Photo::query()->where('slug->tr', $slug)->first();
            if ($existing) {
                $skipped++;
                $bar->advance();

                continue;
            }

            try {
                $photo = new Photo([
                    'title' => ['tr' => 'AA · '.$captured->translatedFormat('d F Y')],
                    'slug' => ['tr' => $slug],
                    'caption' => ['tr' => null],
                    'alt_text' => ['tr' => 'Anadolu Ajansı arşivinden — '.$captured->format('Y-m-d')],
                    'kind' => 'reportage',
                    'credit' => $defaultCredit,
                    'source' => 'AA',
                    'license' => 'editorial-only',
                    'rights_notes' => 'AA Uluslararası Haber Merkezi orijinal logolu — kişisel portfolyo kullanımı için onaylı.',
                    'location' => null,
                    'captured_at' => $captured,
                    'is_published' => true,
                    'is_featured' => $i < $featuredCount,
                    'hero_eligible' => $i < $featuredCount,
                    'is_demo' => false,
                    'sort_order' => $i,
                    'created_by' => $creator->id,
                ]);
                $photo->save();

                $photo->addMedia($path)
                    ->preservingOriginal()
                    ->usingName($slug)
                    ->usingFileName($slug.'.jpg')
                    ->toMediaCollection('image');

                $imported++;
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->warn('  ⚠ '.basename($path).' → '.substr($e->getMessage(), 0, 100));
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('✓ Import complete.');
        $this->line(sprintf('  Imported: %d | Skipped (already in DB): %d | Errors: %d', $imported, $skipped, $errors));
        $this->line(sprintf('  Total published photos in archive: %d', Photo::query()->published()->count()));

        return self::SUCCESS;
    }

    private function extractDate(string $base): ?Carbon
    {
        if (preg_match('/^(\d{4})(\d{2})(\d{2})/', $base, $m)) {
            try {
                return Carbon::create((int) $m[1], (int) $m[2], (int) $m[3], 12, 0, 0);
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }
}
