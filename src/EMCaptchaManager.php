<?php

namespace Elysdan\EMCaptcha;

use Elysdan\EMCaptcha\Generators\AdditionGenerator;
use Elysdan\EMCaptcha\Generators\MultiplicationGenerator;
use Elysdan\EMCaptcha\Generators\OperationGeneratorInterface;
use Elysdan\EMCaptcha\Generators\SubtractionGenerator;
use Elysdan\EMCaptcha\Renderers\ImageRenderer;
use Elysdan\EMCaptcha\Renderers\RendererInterface;
use Illuminate\Support\Facades\Session;

class EMCaptchaManager
{
    /**
     * Map of operator symbols to their generator classes.
     *
     * @var array<string, class-string<OperationGeneratorInterface>>
     */
    protected array $generatorMap = [
        '+' => AdditionGenerator::class,
        '-' => SubtractionGenerator::class,
        '*' => MultiplicationGenerator::class,
    ];

    protected RendererInterface $renderer;
    protected string $difficulty;
    protected array $operations;
    protected string $sessionKey;
    protected int $expireMinutes;

    public function __construct(array $config = [])
    {
        $this->difficulty    = $config['difficulty'] ?? 'medium';
        $this->operations    = $config['operations'] ?? ['+', '-', '*'];
        $this->sessionKey    = $config['session_key'] ?? 'emcaptcha_answer';
        $this->expireMinutes = $config['expire_minutes'] ?? 5;

        $imageConfig    = $config['image'] ?? [];
        $this->renderer = new ImageRenderer($imageConfig);
    }

    /**
     * Create a new captcha, store the answer in session, and return the data.
     *
     * @return array{expression: string, answer: int, key: string}
     */
    public function create(): array
    {
        $generator  = $this->resolveGenerator();
        $captcha    = $generator->generate($this->difficulty);

        // Store the answer and expiration time in the session
        Session::put($this->sessionKey, [
            'answer'     => (int) $captcha['answer'],
            'expires_at' => now()->addMinutes($this->expireMinutes)->timestamp,
        ]);

        return [
            'expression' => $captcha['expression'],
            'answer'     => (int) $captcha['answer'],
            'key'        => $this->sessionKey,
        ];
    }

    /**
     * Render the current captcha expression as a PNG image.
     *
     * If no captcha exists in session, one will be created first.
     */
    public function renderImage(): string
    {
        $sessionData = Session::get($this->sessionKey);

        if (! $sessionData) {
            $this->create();
        }

        // We need to regenerate the expression for rendering.
        // Since the answer is stored in session we need to keep a separate expression.
        // Let's store the full data including expression.
        $captchaData = Session::get($this->sessionKey);

        $expression = $captchaData['expression'] ?? '';

        if (empty($expression)) {
            // Fallback: create a new captcha
            $data = $this->create();
            $expression = $data['expression'];
        }

        return $this->renderer->render($expression . ' = ?');
    }

    /**
     * Create a new captcha and store both the answer and the expression.
     *
     * @return array{expression: string, answer: int, key: string}
     */
    public function createFull(): array
    {
        $generator = $this->resolveGenerator();
        $captcha   = $generator->generate($this->difficulty);

        Session::put($this->sessionKey, [
            'answer'     => (int) $captcha['answer'],
            'expression' => $captcha['expression'],
            'expires_at' => now()->addMinutes($this->expireMinutes)->timestamp,
        ]);

        return [
            'expression' => $captcha['expression'],
            'answer'     => (int) $captcha['answer'],
            'key'        => $this->sessionKey,
        ];
    }

    /**
     * Check if the given value matches the captcha answer.
     */
    public function check(mixed $value): bool
    {
        $sessionData = Session::get($this->sessionKey);

        if (! $sessionData) {
            return false;
        }

        // Check expiration
        if (isset($sessionData['expires_at']) && now()->timestamp > $sessionData['expires_at']) {
            Session::forget($this->sessionKey);
            return false;
        }

        $isValid = (int) $value === (int) $sessionData['answer'];

        // Clear the captcha after validation attempt (one-time use)
        Session::forget($this->sessionKey);

        return $isValid;
    }

    /**
     * Refresh — generate a new captcha and return its data.
     *
     * @return array{expression: string, answer: int, key: string}
     */
    public function refresh(): array
    {
        Session::forget($this->sessionKey);
        return $this->createFull();
    }

    /**
     * Get the image URL for the current captcha.
     */
    public function getImageUrl(): string
    {
        return route('emcaptcha.show') . '?_=' . time();
    }

    /**
     * Resolve a random generator from the enabled operations.
     */
    protected function resolveGenerator(): OperationGeneratorInterface
    {
        $operation = $this->operations[array_rand($this->operations)];
        $class     = $this->generatorMap[$operation] ?? AdditionGenerator::class;

        return new $class();
    }
}
