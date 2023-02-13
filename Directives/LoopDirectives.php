<?php

namespace Services\Calibri\Directives;

use Services\Calibri\Compiler;
use Services\Calibri\Contracts\DirectiveHandlerContract;

class LoopDirectives implements DirectiveHandlerContract
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
    {
        return match ($this->directive) {
            'foreach' => Compiler::wrapPHP(sprintf('foreach (%s):', $this->args[0])),
            'endforeach' => Compiler::wrapPHP(sprintf('endforeach'))
        };
    }
}
