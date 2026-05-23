<?php

namespace Symfony\Component\Routing\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    public function __construct(
        public string $path,
        public ?string $name = null,
        public array $methods = []
    ) {
    }
}
