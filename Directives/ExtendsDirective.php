<?php

namespace Framework\Calibri\Directives;

use Framework\Calibri\Compiler;
use Framework\Calibri\Contracts\DirectiveHandlerContract;
use Framework\Calibri\View;

class ExtendsDirective implements DirectiveHandlerContract
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
        $this->compiler->onEOF(
            fn () => $this->compiler->pushContent(Compiler::wrapPHP(sprintf('%s::extendTemplate(%s)', static::class, ...$this->args)))
        );

        return '';
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $templateView
     * @return void
     */
    public static function extendTemplate(string $templateView): void
    {
        echo (new View($templateView))->getContent();
    }
}
