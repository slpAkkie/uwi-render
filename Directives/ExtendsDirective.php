<?php

namespace Framework\Calibri\Directives;

use Framework\Calibri\Compiler;
use Framework\Calibri\Contracts\DirectiveHandlerContract;
use Framework\Calibri\View;

class ExtendsDirective implements DirectiveHandlerContract
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
        Compiler::onEOF(
            fn ($compiler) => $compiler->pushContent(Compiler::wrapPHP(sprintf('%s::extendTemplate(%s)', static::class, ...$this->args)))
        );

        return '';
    }

    /**
     * Пишет в документ контент шаблона.
     *
     * @param string $templateView Название представления шаблона с пространством имен или без него.
     * @return void
     */
    public static function extendTemplate(string $templateView): void
    {
        echo (new View($templateView))->getContent();
    }
}
