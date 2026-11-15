<?php

namespace App\Services\Cargo;

/**
 * QR codes as inline SVG - no GD, no external service, printable.
 *
 * Uses bacon/bacon-qr-code. If the package is missing on the server (composer was not run
 * after the update) the caller gets null and shows the code as text instead of breaking the
 * page, so the feature degrades quietly.
 */
class QrCodeService
{
    public function svg(string $text, int $size = 240): ?string
    {
        if (! class_exists(\BaconQrCode\Writer::class)) {
            return null;
        }

        try {
            $writer = new \BaconQrCode\Writer(
                new \BaconQrCode\Renderer\ImageRenderer(
                    new \BaconQrCode\Renderer\RendererStyle\RendererStyle($size, 1),
                    new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                )
            );

            $svg = $writer->writeString($text);

            // Inline it: the app renders it without an extra request.
            return preg_replace('/<\?xml[^>]*\?>/', '', $svg);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /** data: URI, handy inside <img src="...">. */
    public function dataUri(string $text, int $size = 240): ?string
    {
        $svg = $this->svg($text, $size);

        return $svg ? 'data:image/svg+xml;base64,' . base64_encode($svg) : null;
    }

    /** The URL a customer scans to see their own cargo status. */
    public function trackingUrl(string $token): string
    {
        return url('/track/' . $token);
    }
}
