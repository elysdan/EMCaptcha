<?php

namespace Elysdan\EMCaptcha\Tests\Unit;

use Elysdan\EMCaptcha\Generators\AdditionGenerator;
use Elysdan\EMCaptcha\Generators\MultiplicationGenerator;
use Elysdan\EMCaptcha\Generators\SubtractionGenerator;
use PHPUnit\Framework\TestCase;

class OperationGeneratorTest extends TestCase
{
    /**
     * @dataProvider difficultyProvider
     */
    public function test_addition_generates_correct_result(string $difficulty): void
    {
        $generator = new AdditionGenerator();

        for ($i = 0; $i < 20; $i++) {
            $result = $generator->generate($difficulty);

            $this->assertArrayHasKey('expression', $result);
            $this->assertArrayHasKey('answer', $result);
            $this->assertIsInt($result['answer']);

            // Parse expression to verify
            preg_match('/^(\d+) \+ (\d+)$/', $result['expression'], $matches);
            $this->assertNotEmpty($matches, "Expression format invalid: {$result['expression']}");

            $a = (int) $matches[1];
            $b = (int) $matches[2];
            $this->assertEquals($a + $b, $result['answer']);
        }
    }

    /**
     * @dataProvider difficultyProvider
     */
    public function test_subtraction_generates_non_negative_result(string $difficulty): void
    {
        $generator = new SubtractionGenerator();

        for ($i = 0; $i < 20; $i++) {
            $result = $generator->generate($difficulty);

            $this->assertArrayHasKey('expression', $result);
            $this->assertArrayHasKey('answer', $result);
            $this->assertIsInt($result['answer']);
            $this->assertGreaterThanOrEqual(0, $result['answer']);

            // Parse expression to verify
            preg_match('/^(\d+) - (\d+)$/', $result['expression'], $matches);
            $this->assertNotEmpty($matches, "Expression format invalid: {$result['expression']}");

            $a = (int) $matches[1];
            $b = (int) $matches[2];
            $this->assertEquals($a - $b, $result['answer']);
            $this->assertGreaterThanOrEqual($b, $a, "First operand should be >= second");
        }
    }

    /**
     * @dataProvider difficultyProvider
     */
    public function test_multiplication_generates_correct_result(string $difficulty): void
    {
        $generator = new MultiplicationGenerator();

        for ($i = 0; $i < 20; $i++) {
            $result = $generator->generate($difficulty);

            $this->assertArrayHasKey('expression', $result);
            $this->assertArrayHasKey('answer', $result);
            $this->assertIsInt($result['answer']);

            // The multiplication symbol is UTF-8 ×
            preg_match('/^(\d+) × (\d+)$/', $result['expression'], $matches);
            $this->assertNotEmpty($matches, "Expression format invalid: {$result['expression']}");

            $a = (int) $matches[1];
            $b = (int) $matches[2];
            $this->assertEquals($a * $b, $result['answer']);
        }
    }

    public function test_addition_easy_range(): void
    {
        $generator = new AdditionGenerator();

        for ($i = 0; $i < 30; $i++) {
            $result = $generator->generate('easy');
            preg_match('/^(\d+) \+ (\d+)$/', $result['expression'], $matches);
            $a = (int) $matches[1];
            $b = (int) $matches[2];
            $this->assertGreaterThanOrEqual(1, $a);
            $this->assertLessThanOrEqual(9, $a);
            $this->assertGreaterThanOrEqual(1, $b);
            $this->assertLessThanOrEqual(9, $b);
        }
    }

    public function test_addition_medium_range(): void
    {
        $generator = new AdditionGenerator();

        for ($i = 0; $i < 30; $i++) {
            $result = $generator->generate('medium');
            preg_match('/^(\d+) \+ (\d+)$/', $result['expression'], $matches);
            $a = (int) $matches[1];
            $b = (int) $matches[2];
            $this->assertGreaterThanOrEqual(10, $a);
            $this->assertLessThanOrEqual(99, $a);
            $this->assertGreaterThanOrEqual(10, $b);
            $this->assertLessThanOrEqual(99, $b);
        }
    }

    public function test_addition_hard_range(): void
    {
        $generator = new AdditionGenerator();

        for ($i = 0; $i < 30; $i++) {
            $result = $generator->generate('hard');
            preg_match('/^(\d+) \+ (\d+)$/', $result['expression'], $matches);
            $a = (int) $matches[1];
            $b = (int) $matches[2];
            $this->assertGreaterThanOrEqual(100, $a);
            $this->assertLessThanOrEqual(999, $a);
            $this->assertGreaterThanOrEqual(100, $b);
            $this->assertLessThanOrEqual(999, $b);
        }
    }

    public static function difficultyProvider(): array
    {
        return [
            'easy'   => ['easy'],
            'medium' => ['medium'],
            'hard'   => ['hard'],
        ];
    }
}
