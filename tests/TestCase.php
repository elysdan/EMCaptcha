<?php

namespace Elysdan\EMCaptcha\Tests;

use Elysdan\EMCaptcha\EMCaptchaServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            EMCaptchaServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'EMCaptcha' => \Elysdan\EMCaptcha\Facades\EMCaptcha::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }
}
