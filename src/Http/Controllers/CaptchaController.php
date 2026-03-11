<?php

namespace Elysdan\EMCaptcha\Http\Controllers;

use Elysdan\EMCaptcha\EMCaptchaManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class CaptchaController extends Controller
{
    protected EMCaptchaManager $captcha;

    public function __construct(EMCaptchaManager $captcha)
    {
        $this->captcha = $captcha;
    }

    /**
     * Show the captcha as a PNG image.
     */
    public function show(): Response
    {
        // Ensure a captcha exists in session
        $this->captcha->createFull();

        $imageData = $this->captcha->renderImage();

        return response($imageData, 200, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }

    /**
     * Refresh the captcha and return JSON with the new image URL.
     */
    public function refresh(): JsonResponse
    {
        $data = $this->captcha->refresh();

        return response()->json([
            'url'        => $this->captcha->getImageUrl(),
            'expression' => $data['expression'],
        ]);
    }
}
