<?php

namespace Elysdan\EMCaptcha\Generators;

class MultiplicationGenerator implements OperationGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(string $difficulty): array
    {
        [$min, $max] = $this->getRange($difficulty);

        $a = random_int($min, $max);
        $b = random_int($min, $max);

        return [
            'expression' => "{$a} × {$b}",
            'answer'     => $a * $b,
        ];
    }

    /**
     * Get the number range for the given difficulty.
     *
     * For multiplication we use smaller numbers on harder levels
     * to keep the answers reasonable.
     *
     * @return array{0: int, 1: int}
     */
    protected function getRange(string $difficulty): array
    {
        return match ($difficulty) {
            'easy'   => [1, 9],
            'medium' => [2, 15],
            'hard'   => [10, 50],
            default  => [2, 15],
        };
    }
}
