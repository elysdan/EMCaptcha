<?php

namespace Elysdan\EMCaptcha\Renderers;

class ImageRenderer implements RendererInterface
{
    protected int $width;
    protected int $height;
    protected int $fontSize;
    protected string $bgColor;
    protected string $textColor;
    protected int $noiseLines;
    protected int $noiseDots;

    public function __construct(array $config = [])
    {
        $this->width      = $config['width'] ?? 200;
        $this->height     = $config['height'] ?? 70;
        $this->fontSize   = $config['font_size'] ?? 28;
        $this->bgColor    = $config['bg_color'] ?? '#ffffff';
        $this->textColor  = $config['text_color'] ?? '#333333';
        $this->noiseLines = $config['noise_lines'] ?? 5;
        $this->noiseDots  = $config['noise_dots'] ?? 50;
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $expression): string
    {
        $image = imagecreatetruecolor($this->width, $this->height);

        // Allocate colors
        $bgRgb   = $this->hexToRgb($this->bgColor);
        $textRgb = $this->hexToRgb($this->textColor);

        $bg   = imagecolorallocate($image, $bgRgb[0], $bgRgb[1], $bgRgb[2]);
        $text = imagecolorallocate($image, $textRgb[0], $textRgb[1], $textRgb[2]);

        // Fill background
        imagefilledrectangle($image, 0, 0, $this->width - 1, $this->height - 1, $bg);

        // Add noise lines
        $this->drawNoiseLines($image);

        // Add noise dots
        $this->drawNoiseDots($image);

        // Render the expression text with slight rotation
        $this->drawText($image, $expression, $text);

        // Capture output
        ob_start();
        imagepng($image);
        $data = ob_get_clean();

        imagedestroy($image);

        return $data;
    }

    /**
     * Draw random noise lines on the image.
     */
    protected function drawNoiseLines(\GdImage $image): void
    {
        for ($i = 0; $i < $this->noiseLines; $i++) {
            $color = imagecolorallocate(
                $image,
                random_int(100, 220),
                random_int(100, 220),
                random_int(100, 220)
            );
            imageline(
                $image,
                random_int(0, $this->width),
                random_int(0, $this->height),
                random_int(0, $this->width),
                random_int(0, $this->height),
                $color
            );
        }
    }

    /**
     * Draw random noise dots on the image.
     */
    protected function drawNoiseDots(\GdImage $image): void
    {
        for ($i = 0; $i < $this->noiseDots; $i++) {
            $color = imagecolorallocate(
                $image,
                random_int(100, 220),
                random_int(100, 220),
                random_int(100, 220)
            );
            imagesetpixel(
                $image,
                random_int(0, $this->width - 1),
                random_int(0, $this->height - 1),
                $color
            );
        }
    }

    /**
     * Draw the expression text centered on the image with a slight rotation.
     */
    protected function drawText(\GdImage $image, string $expression, int $color): void
    {
        // Use GD built-in font (size 5 is the largest built-in font)
        $fontWidth  = imagefontwidth(5);
        $fontHeight = imagefontheight(5);
        $textWidth  = $fontWidth * strlen($expression);

        $x = (int) (($this->width - $textWidth) / 2);
        $y = (int) (($this->height - $fontHeight) / 2);

        // Draw each character with slight vertical jitter for anti-OCR
        for ($i = 0; $i < strlen($expression); $i++) {
            $charX = $x + ($i * $fontWidth);
            $charY = $y + random_int(-3, 3);
            imagechar($image, 5, $charX, $charY, $expression[$i], $color);
        }
    }

    /**
     * Convert a hex color string to an RGB array.
     *
     * @return array{0: int, 1: int, 2: int}
     */
    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        ];
    }
}
