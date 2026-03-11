<?php

namespace Elysdan\EMCaptcha\Tests\Unit;

use Elysdan\EMCaptcha\EMCaptchaManager;
use Elysdan\EMCaptcha\Tests\TestCase;

class EMCaptchaManagerTest extends TestCase
{
    protected EMCaptchaManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = app(EMCaptchaManager::class);
    }

    public function test_create_returns_expression_and_answer(): void
    {
        $result = $this->manager->createFull();

        $this->assertArrayHasKey('expression', $result);
        $this->assertArrayHasKey('answer', $result);
        $this->assertArrayHasKey('key', $result);
        $this->assertIsInt($result['answer']);
        $this->assertNotEmpty($result['expression']);
    }

    public function test_check_validates_correct_answer(): void
    {
        $result = $this->manager->createFull();
        $answer = $result['answer'];

        $this->assertTrue($this->manager->check($answer));
    }

    public function test_check_rejects_wrong_answer(): void
    {
        $result = $this->manager->createFull();
        $wrongAnswer = $result['answer'] + 9999;

        $this->assertFalse($this->manager->check($wrongAnswer));
    }

    public function test_check_is_one_time_use(): void
    {
        $result = $this->manager->createFull();
        $answer = $result['answer'];

        // First check should pass
        $this->assertTrue($this->manager->check($answer));

        // Second check with the same answer should fail (session cleared)
        $this->assertFalse($this->manager->check($answer));
    }

    public function test_refresh_generates_new_captcha(): void
    {
        $first  = $this->manager->createFull();
        $second = $this->manager->refresh();

        $this->assertArrayHasKey('expression', $second);
        $this->assertArrayHasKey('answer', $second);
        $this->assertIsInt($second['answer']);
    }

    public function test_check_returns_false_when_no_captcha_exists(): void
    {
        $this->assertFalse($this->manager->check(42));
    }

    public function test_render_image_returns_png_data(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD extension not available.');
        }

        $this->manager->createFull();
        $imageData = $this->manager->renderImage();

        $this->assertNotEmpty($imageData);
        // PNG files start with the PNG signature
        $this->assertStringStartsWith("\x89PNG", $imageData);
    }
}
