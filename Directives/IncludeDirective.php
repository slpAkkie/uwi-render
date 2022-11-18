<?php

namespace Services\Calibri\Directives;

use Services\Calibri\Compiler;
use Services\Calibri\Contracts\DirectiveHandlerContract;
use Services\Calibri\View;

class IncludeDirective implements DirectiveHandlerContract
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
        return Compiler::wrapPHP(sprintf('%s::includeView(%s)', static::class, ...$this->args));
    }

    /**
     * Пишет в документ контент другого представления.
     *
     * @param string $viewName Название представления с пространством имен или без него.
     * @return void
     */
    public static function includeView(string $viewName): void
    {
        echo (new View($viewName))->getContent();
    }
}
