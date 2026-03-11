<?php

namespace Elysdan\EMCaptcha\Generators;

class SubtractionGenerator implements OperationGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(string $difficulty): array
    {
        [$min, $max] = $this->getRange($difficulty);

        $a = random_int($min, $max);
        $b = random_int($min, $max);

        // Ensure result is >= 0 by swapping if needed
        if ($a < $b) {
            [$a, $b] = [$b, $a];
        }

        return [
            'expression' => "{$a} - {$b}",
            'answer'     => $a - $b,
        ];
    }

    /**
     * Get the number range for the given difficulty.
     *
     * @return array{0: int, 1: int}
     */
    protected function getRange(string $difficulty): array
    {
        return match ($difficulty) {
            'easy'   => [1, 9],
            'medium' => [10, 99],
            'hard'   => [100, 999],
            default  => [10, 99],
        };
    }
}
