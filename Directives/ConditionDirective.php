<?php

namespace Framework\Calibri\Directives;

use Framework\Calibri\Compiler;
use Framework\Calibri\Contracts\DirectiveHandlerContract;

class ConditionDirective implements DirectiveHandlerContract
{
    /**
     * TODO: Undocumented function
     *
     * @param Framework\Calibri\Compiler $compiler
     * @param string $directive
     * @param array<string> $args
     */
    public function __construct(
        /**
         * TODO: Undocumented variable
         *
         * @var Framework\Calibri\Compiler
         */
        protected Compiler $compiler,
        /**
         * TODO: Undocumented variable
         *
         * @var string
         */
        protected string $directive,
        /**
         * TODO: Undocumented variable
         *
         * @var array<string>
         */
        protected array $args
    ) {
        //
    }

    /**
     * TODO: Undocumented function
     *
     * @return string
     */
    public function execute(): string
    {;
        return match ($this->directive) {
            'if' => Compiler::wrapPHP(sprintf('if (%s):', $this->args[0])),
            'elseif' => Compiler::wrapPHP(sprintf('elseif (%s):', $this->args[0])),
            'else' => Compiler::wrapPHP(sprintf('else:')),
            'endif' => Compiler::wrapPHP(sprintf('endif'))
        };
    }
}
