<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Responses;

final class HtmlResponse implements Response
{
    public function __construct(
        private readonly int $statusCode,
        private readonly string $html,
    ) {
    }

    public static function ok(string $html): self
    {
        return new self(200, $html);
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: text/html; charset=utf-8');
        echo $this->html;
    }
}
