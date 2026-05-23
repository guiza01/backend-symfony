<?php

namespace Symfony\Component\HttpFoundation;

class Request
{
    private string $content;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public static function createFromGlobals(): self
    {
        $content = file_get_contents('php://input');
        return new self($content === false ? '' : $content);
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
