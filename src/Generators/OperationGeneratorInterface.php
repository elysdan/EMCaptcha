<?php

namespace Elysdan\EMCaptcha\Generators;

interface OperationGeneratorInterface
{
    /**
     * Generate an arithmetic expression and its answer.
     *
     * @param  string  $difficulty  'easy', 'medium', or 'hard'
     * @return array{expression: string, answer: int}
     */
    public function generate(string $difficulty): array;
}
