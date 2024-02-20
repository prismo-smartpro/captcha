<?php

namespace SmartPRO\Technology;

use Exception;

class Captcha
{
    const FONT_PATH = __DIR__ . "/assets/fonts/Roboto-BoldItalic.ttf";
    const BACKGROUND = __DIR__ . "/assets/images/background.png";

    private static function Create($text): string
    {
        ob_start();
        $image = imagecreatefrompng(self::BACKGROUND);
        $textColor = imagecolorallocate($image, 255, 255, 255);
        $x = 16;
        $y = 28;
        $fontSize = 18;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, self::FONT_PATH, $text);
        imagepng($image);
        imagedestroy($image);
        $image_data = ob_get_contents();
        ob_end_clean();
        return 'data:image/png;base64,' . base64_encode($image_data);
    }

    /**
     * @throws Exception
     */
    public static function Verify($captcha): bool
    {
        $session = $_SESSION["server_captcha"] ?? null;
        if (is_null($session)) {
            throw new Exception("O captcha não foi criado");
        }

        if (empty($captcha)) {
            throw new Exception("O captcha informado é inválido");
        }

        if ($session !== $captcha) {
            throw new Exception("Captcha inválido");
        }

        return true;
    }

    private static function generateCaptchaString(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $captchaString = '';
        for ($i = 0; $i < 6; $i++) {
            $captchaString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $captchaString;
    }

    public static function Render(): string
    {
        $captcha = self::generateCaptchaString();
        $_SESSION["server_captcha"] = $captcha;
        $image = self::Create($captcha);
        return "<img src='{$image}' alt='Captcha'>";
    }
}