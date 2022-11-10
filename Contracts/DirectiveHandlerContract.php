<?php

namespace Framework\Calibri\Contracts;

use Framework\Calibri\Compiler;

interface DirectiveHandlerContract
{
    /**
     * TODO: Undocumented function
     *
     * @param Framework\Calibri\Compiler $compiler
     * @param string $directive
     * @param array<string> $args
     */
    public function __construct(Compiler $compiler, string $directive, array $args);

    /**
     * TODO: Undocumented function
     *
     * @return string
     */
    public function execute(): string;
}
