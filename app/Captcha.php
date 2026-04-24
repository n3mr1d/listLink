<?php

namespace App;

use App\Models\Captch;

class Captcha
{
    /**
     * Create a new class instance.
     */
    public $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Removed O, 0, 1, I, l
    public $code = '';
    public $expired_at;
    public $base64code = '';

    public function generateCode($length = 6)
    {
        $this->code = '';
        for ($i = 0; $i < $length; $i++) {
            $this->code .= $this->chars[mt_rand(0, strlen($this->chars) - 1)];
        }
        return $this->code;
    }

    public function generateImagebase64($code)
    {
        $width = 160;
        $height = 50;
        $image = imagecreatetruecolor($width, $height);

        // Colors
        $bg = imagecolorallocate($image, 13, 17, 23); // dark gh-style
        imagefill($image, 0, 0, $bg);

        // Add some noise (lines)
        for ($i = 0; $i < 6; $i++) {
            $line_color = imagecolorallocate($image, mt_rand(30, 80), mt_rand(30, 80), mt_rand(30, 80));
            imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $line_color);
        }

        // Add some noise (dots)
        for ($i = 0; $i < 500; $i++) {
            $dot_color = imagecolorallocate($image, mt_rand(20, 60), mt_rand(20, 60), mt_rand(20, 60));
            imagesetpixel($image, mt_rand(0, $width), mt_rand(0, $height), $dot_color);
        }

        $font = public_path('playwrite.ttf');
        $char_width = ($width - 20) / strlen($code);

        for ($i = 0; $i < strlen($code); $i++) {
            $text_color = imagecolorallocate($image, mt_rand(150, 255), mt_rand(150, 255), mt_rand(150, 255));
            $angle = mt_rand(-15, 15);
            $x = 10 + ($i * $char_width) + mt_rand(0, 5);
            $y = 35 + mt_rand(-5, 5);

            // Fallback if font doesn't exist
            if (file_exists($font)) {
                imagettftext($image, 20, $angle, $x, $y, $text_color, $font, $code[$i]);
            } else {
                imagestring($image, 5, $x, $y - 20, $code[$i], $text_color);
            }
        }

        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();

        imagedestroy($image);
        $this->base64code = 'data:image/png;base64,' . base64_encode($imageData);
    }
    public function startCaptcha()
    {

        $this->generateCode();
        $this->generateImagebase64($this->code);
        $save = Captch::create([
            'code' => $this->code,
            'expired_at' => now()->addMinutes(5),
        ]);
        return $this->base64code;

    }
    public static function checkCaptcha(string $code): bool
    {
        $captcha = Captch::where('code', $code)->first();
        if ($captcha && $captcha->expired_at > now()) {
            $captcha->delete();
            return true;
        }
        return false;
    }


}
