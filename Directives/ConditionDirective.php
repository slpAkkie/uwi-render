<?php

namespace Framework\Calibri\Directives;

use Framework\Calibri\Compiler;
use Framework\Calibri\Contracts\DirectiveHandlerContract;

class ConditionDirective implements DirectiveHandlerContract
{
    /**
     * Инициализировать обработчик.
     *
     * @param string $directive
     * @param array<string> $args
     */
    public function __construct(
        /**
         * Название обрабатываемой директивы.
         *
         * @var string
         */
        protected string $directive,
        /**
         * Аргументы директивы.
         *
         * @var array<string>
         */
        protected array $args
    ) {
        //
    }

    /**
     * Скомпилировать директиву.
     *
     * @return string Строка для замены вызова директивы на php код.
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
