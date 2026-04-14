<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

/**
 * AdBannerCompressor
 *
 * Resizes, crops, and compresses an ad-banner image to exactly 670 × 76 px.
 * Uses native PHP GD — no Intervention Image or Imagick required.
 *
 * Output format: WebP (if supported) → JPEG fallback.
 * Quality: 82 (good balance of file-size vs. visual fidelity).
 */
class AdBannerCompressor
{
    /** Target banner dimensions (pixels) */
    public const WIDTH  = 670;
    public const HEIGHT = 76;

    /** JPEG/WebP compression quality (0–100) */
    public const QUALITY = 82;

    /**
     * Compress and store an uploaded banner file.
     *
     * @param  UploadedFile  $file      The raw uploaded image
     * @param  string        $disk      Laravel storage disk name (default: 'public')
     * @param  string        $directory Sub-directory inside the disk (default: 'ads')
     * @return string                   The stored path relative to the disk root
     */
    public static function process(
        UploadedFile $file,
        string $disk = 'public',
        string $directory = 'ads'
    ): string {
        $srcPath = $file->getRealPath();
        $mime    = $file->getMimeType();

        // ── 1. Decode source image ─────────────────────────────────────────
        $src = match (true) {
            str_contains($mime, 'png')  => imagecreatefrompng($srcPath),
            str_contains($mime, 'gif')  => imagecreatefromgif($srcPath),
            str_contains($mime, 'webp') => imagecreatefromwebp($srcPath),
            default                     => imagecreatefromjpeg($srcPath),
        };

        if (! $src) {
            // If GD cannot parse the image, fall back to raw storage
            return $file->store($directory, $disk);
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        // ── 2. Calculate cover-fit crop (fill target rect, crop excess) ────
        $scaleW = self::WIDTH  / $srcW;
        $scaleH = self::HEIGHT / $srcH;
        $scale  = max($scaleW, $scaleH);    // cover: use the larger ratio

        $fittedW = (int) round($srcW * $scale);
        $fittedH = (int) round($srcH * $scale);

        $cropX = (int) round(($fittedW - self::WIDTH)  / 2);
        $cropY = (int) round(($fittedH - self::HEIGHT) / 2);

        // ── 3. Create destination canvas ───────────────────────────────────
        $dst = imagecreatetruecolor(self::WIDTH, self::HEIGHT);

        // Preserve transparency for PNG / GIF sources
        if (in_array($mime, ['image/png', 'image/gif', 'image/webp'], true)) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, self::WIDTH, self::HEIGHT, $transparent);
        }

        // Resize-then-crop in one pass
        imagecopyresampled(
            $dst, $src,
            0 - $cropX, 0 - $cropY,   // dst offset
            0, 0,                       // src offset
            $fittedW, $fittedH,        // dst rendered size
            $srcW, $srcH               // src size
        );

        imagedestroy($src);

        // ── 4. Encode to WebP or JPEG ──────────────────────────────────────
        $useWebP = function_exists('imagewebp');
        $ext     = $useWebP ? 'webp' : 'jpg';
        $tmpName = tempnam(sys_get_temp_dir(), 'ad_banner_') . '.' . $ext;

        if ($useWebP) {
            imagewebp($dst, $tmpName, self::QUALITY);
        } else {
            imagejpeg($dst, $tmpName, self::QUALITY);
        }

        imagedestroy($dst);

        // ── 5. Move to storage disk ────────────────────────────────────────
        $storedName = $directory . '/' . uniqid('banner_', true) . '.' . $ext;
        \Illuminate\Support\Facades\Storage::disk($disk)->put(
            $storedName,
            file_get_contents($tmpName)
        );
        @unlink($tmpName);

        return $storedName;
    }
}
