<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Photo;
use App\Models\User;
use App\Models\Writing;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * SeedDemoMedia — generate editorial-style placeholder images and attach
 * them to demo writings + create a full /görüntü archive.
 *
 * Everything is stamped `is_demo => true` so it can be filtered out once
 * real photography replaces it. Images are generated via GD with a
 * gradient + scan-line texture + title overlay — the goal is a visual
 * stand-in that reads as photography at first glance, not a placeholder
 * checker-pattern.
 */
#[Signature('demo:seed-media {--fresh : Wipe existing demo photos + covers first}')]
#[Description('Populate demo writings with placeholder covers + create /görüntü archive entries.')]
class SeedDemoMedia extends Command
{
    /**
     * Editorial scenes matched to visual moods.
     *
     * @var array<int, array<string, mixed>>
     */
    private const SCENES = [
        ['location' => 'Üsküdar', 'kind' => 'reportage', 'palette' => ['#1a1a1a', '#3c2a2a', '#7a1e1e'], 'title' => 'Haber Masası · Sabah'],
        ['location' => 'Kadıköy', 'kind' => 'reportage', 'palette' => ['#0f0f14', '#1e2a3a', '#45627a'], 'title' => 'Akşam Vardiyası'],
        ['location' => 'Beyoğlu', 'kind' => 'editorial', 'palette' => ['#1a1616', '#2a1e1e', '#a31629'], 'title' => 'İstiklal · Mürekkep'],
        ['location' => 'Sarıyer', 'kind' => 'drone', 'palette' => ['#0a0f1a', '#14243a', '#2a4a6a'], 'title' => 'Boğaz Hattı'],
        ['location' => 'Fatih', 'kind' => 'protocol', 'palette' => ['#1a1a1a', '#282828', '#8a7a6a'], 'title' => 'Protokol Salonu'],
        ['location' => 'Beşiktaş', 'kind' => 'reportage', 'palette' => ['#0d0d0d', '#2a2a2a', '#5a5a5a'], 'title' => 'Stadın Gölgesi'],
        ['location' => 'Eminönü', 'kind' => 'editorial', 'palette' => ['#1a1414', '#3a2a1a', '#a35a1a'], 'title' => 'İskele · Işık'],
        ['location' => 'Şişli', 'kind' => 'reportage', 'palette' => ['#14141a', '#2a2a3a', '#4a4a6a'], 'title' => 'Mecidiyeköy'],
        ['location' => 'Taksim', 'kind' => 'editorial', 'palette' => ['#1a0f0f', '#2a1a1a', '#a31629'], 'title' => 'Meydan · Gece'],
        ['location' => 'Adana', 'kind' => 'archive', 'palette' => ['#14100a', '#2a1f14', '#6a4f2a'], 'title' => 'Güneyden Kayıt'],
        ['location' => 'İzmir', 'kind' => 'drone', 'palette' => ['#0a141a', '#14283a', '#2a5070'], 'title' => 'Körfez Sabahı'],
        ['location' => 'Ankara', 'kind' => 'protocol', 'palette' => ['#141414', '#2a2a2a', '#7a7a7a'], 'title' => 'Meclis Koridoru'],
    ];

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->error('PHP GD extension is required.');

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->info('Wiping existing demo photos...');
            Photo::query()->where('is_demo', true)->forceDelete();
            foreach (Writing::query()->where('is_demo', true)->get() as $w) {
                $w->clearMediaCollection('cover');
            }
        }

        $creator = User::query()->orderBy('id')->first();
        if (! $creator) {
            $this->error('No user found to own demo photos.');

            return self::FAILURE;
        }

        $this->attachCoversToWritings();
        $this->seedPhotoArchive($creator->id);

        $this->newLine();
        $this->info('✓ Demo media seeded.');
        $this->line('  Writings with covers: '.Writing::query()->whereHas('media', fn ($q) => $q->where('collection_name', 'cover'))->count());
        $this->line('  Photos published:     '.Photo::query()->published()->count());

        return self::SUCCESS;
    }

    /**
     * Attach editorial-style covers to every demo writing, using
     * per-writing palette so the covers feel distinct (not stamped).
     */
    private function attachCoversToWritings(): void
    {
        $writings = Writing::query()->where('is_demo', true)->orderBy('id')->get();

        $this->info('Generating covers for '.$writings->count().' writings...');

        foreach ($writings as $i => $writing) {
            $scene = self::SCENES[$i % count(self::SCENES)];

            $title = $writing->getTranslation('title', 'tr', false) ?: 'Dispatch';
            $path = $this->generateEditorialImage(
                width: 1920,
                height: 1280,
                palette: $scene['palette'],
                overlay: $title,
                kicker: strtoupper($scene['location']),
                seed: $writing->id * 7,
            );

            $cleanName = Str::slug($title);
            $writing->clearMediaCollection('cover');
            $writing->addMedia($path)
                ->usingName($cleanName)
                ->usingFileName($cleanName.'.jpg')
                ->toMediaCollection('cover');

            @unlink($path);

            $this->line('  ✓ '.$title);
        }
    }

    /**
     * Create 12 photo archive entries spanning kinds + locations, each
     * with its own generated image — so /görüntü has a full visual grid.
     */
    private function seedPhotoArchive(int $creatorId): void
    {
        $this->info('Seeding photo archive (12 entries)...');

        foreach (self::SCENES as $i => $scene) {
            $captured = Carbon::now()->subDays(($i + 1) * 11)->setTime(8 + ($i % 10), 15 * ($i % 4));

            $titleTr = $scene['title'];
            $slug = Str::slug($titleTr).'-'.($i + 1);

            $existing = Photo::query()
                ->where('is_demo', true)
                ->where('slug->tr', $slug)
                ->first();

            if ($existing) {
                continue;
            }

            $photo = new Photo([
                'title' => ['tr' => $titleTr],
                'slug' => ['tr' => $slug],
                'caption' => ['tr' => $this->captionFor($scene)],
                'alt_text' => ['tr' => strtolower($titleTr).' — '.$scene['location']],
                'kind' => $scene['kind'],
                'credit' => 'Foto: Ozan Efeoğlu / AA',
                'source' => 'AA',
                'license' => 'editorial-only',
                'location' => $scene['location'],
                'captured_at' => $captured,
                'is_published' => true,
                'is_featured' => $i < 3,
                'hero_eligible' => $i < 4,
                'is_demo' => true,
                'sort_order' => $i,
                'created_by' => $creatorId,
            ]);
            $photo->save();

            $path = $this->generateEditorialImage(
                width: 2200,
                height: 1467,
                palette: $scene['palette'],
                overlay: $titleTr,
                kicker: strtoupper($scene['location']),
                seed: ($i + 1) * 13,
            );

            $photo->addMedia($path)
                ->usingName($slug)
                ->usingFileName($slug.'.jpg')
                ->toMediaCollection('image');

            @unlink($path);

            $this->line('  ✓ '.$titleTr.' · '.$scene['location']);
        }
    }

    private function captionFor(array $scene): string
    {
        $kind = $scene['kind'];
        $loc = $scene['location'];

        return match ($kind) {
            'reportage' => "Sahadan kayıt — {$loc}. Haber masasının sahaya uzantısı; kare değil kararlar.",
            'drone' => "Drone / hava kaydı — {$loc}. Ölçek insanda; mesafe kadrajda.",
            'portrait' => "Portre — {$loc}. Yüz değil, bağlam.",
            'protocol' => "Protokol — {$loc}. Sahnenin değil, sahnelenenin fotoğrafı.",
            'editorial' => "Editoryal — {$loc}. Metnin öncülü görüntü; görüntünün ikametgâhı metin.",
            default => "Arşivden — {$loc}.",
        };
    }

    /**
     * Generate an editorial-looking JPEG: dark gradient + scan-line
     * texture + vignette + subtle noise, with a kicker + title overlay.
     * Returns a filesystem path the caller owns (should unlink after use).
     */
    private function generateEditorialImage(int $width, int $height, array $palette, string $overlay, string $kicker, int $seed = 0): string
    {
        mt_srand($seed);

        $img = imagecreatetruecolor($width, $height);

        [$c1, $c2, $c3] = array_map(fn ($hex) => $this->hexToRgb($hex), $palette);

        // Vertical gradient c1 → c2
        for ($y = 0; $y < $height; $y++) {
            $t = $y / max(1, $height - 1);
            $r = (int) (($c1[0] * (1 - $t)) + ($c2[0] * $t));
            $g = (int) (($c1[1] * (1 - $t)) + ($c2[1] * $t));
            $b = (int) (($c1[2] * (1 - $t)) + ($c2[2] * $t));
            imageline($img, 0, $y, $width, $y, imagecolorallocate($img, $r, $g, $b));
        }

        // Radial highlight in upper-left from c3 (editorial accent)
        $cx = (int) ($width * 0.28);
        $cy = (int) ($height * 0.35);
        $rad = (int) ($width * 0.55);
        for ($i = $rad; $i > 0; $i -= 2) {
            $alpha = (int) (110 * (1 - $i / $rad));
            if ($alpha <= 0) {
                continue;
            }
            $col = imagecolorallocatealpha($img, $c3[0], $c3[1], $c3[2], 127 - (int) min(127, $alpha / 4));
            imagefilledellipse($img, $cx, $cy, $i * 2, $i * 2, $col);
        }

        // Scan-line texture — subtle horizontal darkening every 3px
        $scan = imagecolorallocatealpha($img, 0, 0, 0, 110);
        for ($y = 0; $y < $height; $y += 3) {
            imageline($img, 0, $y, $width, $y, $scan);
        }

        // Vignette
        $vx = imagesx($img) / 2;
        $vy = imagesy($img) / 2;
        for ($y = 0; $y < $height; $y += 2) {
            for ($x = 0; $x < $width; $x += 2) {
                $dx = ($x - $vx) / $vx;
                $dy = ($y - $vy) / $vy;
                $d = min(1, sqrt($dx * $dx + $dy * $dy));
                if ($d > 0.6) {
                    $a = (int) (min(127, ($d - 0.6) * 240));
                    $v = imagecolorallocatealpha($img, 0, 0, 0, 127 - $a);
                    imagesetpixel($img, $x, $y, $v);
                    imagesetpixel($img, $x + 1, $y, $v);
                }
            }
        }

        // Grain
        for ($n = 0; $n < (int) ($width * $height * 0.01); $n++) {
            $x = mt_rand(0, $width - 1);
            $y = mt_rand(0, $height - 1);
            $gv = mt_rand(0, 40);
            imagesetpixel($img, $x, $y, imagecolorallocatealpha($img, 255, 255, 255, 127 - $gv));
        }

        // Kicker + title text, bottom-left — editorial masthead feel
        $white = imagecolorallocate($img, 240, 236, 230);
        $red = imagecolorallocate($img, 227, 36, 48);

        // Use built-in GD font (imagettftext would require a TTF on disk).
        // imagestring sizes: 1-5. 5 is largest. Use layered approach.
        $pad = (int) ($height * 0.08);

        // Left vertical rule (red)
        imagefilledrectangle($img, $pad, (int) ($height * 0.55), $pad + 4, (int) ($height - $pad), $red);

        // Kicker
        imagestring($img, 4, $pad + 18, (int) ($height * 0.55), $kicker, $white);

        // Title wrap — break to 2 lines if long
        $lines = $this->wrapText($overlay, 30);
        $y = (int) ($height * 0.62);
        foreach ($lines as $line) {
            imagestring($img, 5, $pad + 18, $y, strtoupper($line), $white);
            $y += 22;
        }

        $path = tempnam(sys_get_temp_dir(), 'demomedia_').'.jpg';
        imagejpeg($img, $path, 82);
        imagedestroy($img);

        return $path;
    }

    /** @return array<int, int> */
    private function hexToRgb(string $hex): array
    {
        $h = ltrim($hex, '#');
        if (strlen($h) !== 6) {
            return [30, 30, 30];
        }

        return [
            (int) hexdec(substr($h, 0, 2)),
            (int) hexdec(substr($h, 2, 2)),
            (int) hexdec(substr($h, 4, 2)),
        ];
    }

    /** @return array<int, string> */
    private function wrapText(string $text, int $perLine): array
    {
        $words = preg_split('/\s+/u', $text) ?: [];
        $lines = [];
        $cur = '';
        foreach ($words as $w) {
            if ($cur === '') {
                $cur = $w;

                continue;
            }
            if (mb_strlen($cur.' '.$w) > $perLine) {
                $lines[] = $cur;
                $cur = $w;
            } else {
                $cur .= ' '.$w;
            }
        }
        if ($cur !== '') {
            $lines[] = $cur;
        }

        return array_slice($lines, 0, 2);
    }
}
