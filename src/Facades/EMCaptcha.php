<?php

namespace Elysdan\EMCaptcha\Facades;

use Elysdan\EMCaptcha\EMCaptchaManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array create()
 * @method static array createFull()
 * @method static bool check(mixed $value)
 * @method static array refresh()
 * @method static string renderImage()
 * @method static string getImageUrl()
 *
 * @see \Elysdan\EMCaptcha\EMCaptchaManager
 */
class EMCaptcha extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EMCaptchaManager::class;
    }
}
