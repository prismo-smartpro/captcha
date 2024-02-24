<?php

namespace SmartPRO\Technology;

use Exception;

/**
 *
 */
class Captcha
{
    const FONT_PATH = __DIR__ . "/assets/fonts/Roboto-BoldItalic.ttf";
    const BACKGROUND = __DIR__ . "/assets/images/background.png";

    /**
     * @param $text
     * @return string
     */
    private static function Create($text): string
    {
        ob_start();
        $image = imagecreatefrompng(self::BACKGROUND);
        $box = imagettfbbox(18, 0, self::FONT_PATH, $text);
        $width = $box[2] - $box[0];
        $position = (int)((120 - $width) / 2);
        $textColor = imagecolorallocate($image, 255, 255, 255);
        $x = $position;
        $y = 27;
        $fontSize = 18;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, self::FONT_PATH, $text);
        imagepng($image);
        imagedestroy($image);
        $image_data = ob_get_contents();
        ob_end_clean();
        return "data:image/png;base64," . base64_encode($image_data);
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

    /**
     * @return string
     */
    private static function generateCaptchaString(): string
    {
        $rand = mt_rand(4, 6);
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        $charactersLength = strlen($characters);
        $captchaString = "";
        for ($i = 0; $i < $rand; $i++) {
            $captchaString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $captchaString;
    }

    /**
     * @return string
     */
    public static function Render(): string
    {
        do {
            $captcha = self::generateCaptchaString();
        } while (is_numeric($captcha));

        $_SESSION["server_captcha"] = $captcha;
        $image = self::Create($captcha);
        return "<img class='captcha-image-stm' src='{$image}' alt='Captcha'>";
    }
}