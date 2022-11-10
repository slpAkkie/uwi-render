<?php

namespace Framework\Calibri\Directives;

use Framework\Calibri\Compiler;
use Framework\Calibri\Contracts\DirectiveHandlerContract;
use Framework\Calibri\View;

class IncludeDirective implements DirectiveHandlerContract
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
    {
        return Compiler::wrapPHP(sprintf('%s::includeView(%s)', static::class, ...$this->args));
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $viewName
     * @return void
     */
    public static function includeView(string $viewName): void
    {
        echo (new View($viewName))->getContent();
    }
}
