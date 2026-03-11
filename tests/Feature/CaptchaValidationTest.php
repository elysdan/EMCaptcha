<?php

namespace Elysdan\EMCaptcha\Tests\Feature;

use Elysdan\EMCaptcha\EMCaptchaManager;
use Elysdan\EMCaptcha\Tests\TestCase;

class CaptchaValidationTest extends TestCase
{
    public function test_captcha_show_route_returns_png_image(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD extension not available.');
        }

        $response = $this->get(route('emcaptcha.show'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_captcha_refresh_returns_json_with_url(): void
    {
        $response = $this->get(route('emcaptcha.refresh'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['url', 'expression']);
    }

    public function test_validation_passes_with_correct_answer(): void
    {
        /** @var EMCaptchaManager $manager */
        $manager = app(EMCaptchaManager::class);
        $captcha = $manager->createFull();

        $this->assertTrue($manager->check($captcha['answer']));
    }

    public function test_validation_fails_with_incorrect_answer(): void
    {
        /** @var EMCaptchaManager $manager */
        $manager = app(EMCaptchaManager::class);
        $captcha = $manager->createFull();

        $wrongAnswer = $captcha['answer'] + 1;
        $this->assertFalse($manager->check($wrongAnswer));
    }

    public function test_validation_rule_in_request(): void
    {
        /** @var EMCaptchaManager $manager */
        $manager = app(EMCaptchaManager::class);
        $captcha = $manager->createFull();

        // Register a test route
        $this->app['router']->post('/test-captcha', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'captcha' => ['required', new \Elysdan\EMCaptcha\Rules\ValidCaptcha],
            ]);

            return response()->json(['success' => true]);
        })->middleware('web');

        // Test with correct answer
        $response = $this->withSession([
            'emcaptcha_answer' => [
                'answer'     => $captcha['answer'],
                'expression' => $captcha['expression'],
                'expires_at' => now()->addMinutes(5)->timestamp,
            ],
        ])->postJson('/test-captcha', [
            'captcha' => $captcha['answer'],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_validation_rule_fails_with_wrong_answer(): void
    {
        /** @var EMCaptchaManager $manager */
        $manager = app(EMCaptchaManager::class);
        $captcha = $manager->createFull();

        $this->app['router']->post('/test-captcha-fail', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'captcha' => ['required', new \Elysdan\EMCaptcha\Rules\ValidCaptcha],
            ]);

            return response()->json(['success' => true]);
        })->middleware('web');

        $response = $this->withSession([
            'emcaptcha_answer' => [
                'answer'     => $captcha['answer'],
                'expression' => $captcha['expression'],
                'expires_at' => now()->addMinutes(5)->timestamp,
            ],
        ])->postJson('/test-captcha-fail', [
            'captcha' => $captcha['answer'] + 999,
        ]);

        $response->assertStatus(422);
    }
}
