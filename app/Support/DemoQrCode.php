<?php

namespace App\Support;

/**
 * Helper untuk QR Code DEMO (simulasi).
 * Menghasilkan QR berdasarkan data dummy — BUKAN QRIS merchant asli.
 */
class DemoQrCode
{
    protected static bool $autoloaded = false;

    /**
     * Daftarkan namespace library QR ke composer ClassLoader.
     */
    public static function registerAutoload(): void
    {
        if (static::$autoloaded) {
            return;
        }
        static::$autoloaded = true;

        $loader = require base_path('vendor/autoload.php');
        $loader->setPsr4('SimpleSoftwareIO\\QrCode\\', [base_path('vendor/simplesoftwareio/simple-qrcode/src')]);
        $loader->setPsr4('DASPRiD\\Enum\\', [base_path('vendor/dasprid/enum/src')]);
        $loader->setPsr4('BaconQrCode\\', [base_path('vendor/bacon/bacon-qr-code/src')]);
    }

    /**
     * Kembalikan SVG QR berisi data dummy.
     */
    public static function svg(string $data = 'DEMO-QRIS-VERDANT-BISTRO'): string
    {
        static::registerAutoload();

        try {
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(360),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer = new \BaconQrCode\Writer($renderer);

            return $writer->writeString($data);
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Kembalikan PNG QR (via GD). Digunakan untuk mencetak kartu meja.
     */
    public static function png(string $data, int $pixel = 12, int $margin = 4): string
    {
        static::registerAutoload();

        try {
            $qr = \BaconQrCode\Encoder\Encoder::encode($data, \BaconQrCode\Common\ErrorCorrectionLevel::M());
            $matrix = $qr->getMatrix();
            $size = $matrix->getWidth();
            $total = ($size + $margin * 2) * $pixel;

            $img = imagecreatetruecolor($total, $total);
            $white = imagecolorallocate($img, 255, 255, 255);
            $black = imagecolorallocate($img, 0, 0, 0);
            imagefilledrectangle($img, 0, 0, $total, $total, $white);

            for ($y = 0; $y < $size; $y++) {
                for ($x = 0; $x < $size; $x++) {
                    if ($matrix->get($x, $y)) {
                        $px = ($x + $margin) * $pixel;
                        $py = ($y + $margin) * $pixel;
                        imagefilledrectangle($img, $px, $py, $px + $pixel - 1, $py + $pixel - 1, $black);
                    }
                }
            }

            ob_start();
            imagepng($img);
            $png = (string) ob_get_clean();
            imagedestroy($img);

            return $png;
        } catch (\Throwable $e) {
            return '';
        }
    }
}
