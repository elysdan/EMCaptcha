<?php

namespace Elysdan\EMCaptcha\Renderers;

interface RendererInterface
{
    /**
     * Render an arithmetic expression as a binary image string.
     *
     * @param  string  $expression  The math expression (e.g. "5 + 3")
     * @return string Raw image binary data
     */
    public function render(string $expression): string;
}
