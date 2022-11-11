<?php

namespace Framework\Calibri\Directives;

use Framework\Calibri\Compiler;
use Framework\Calibri\Contracts\DirectiveHandlerContract;

class YieldDirective implements DirectiveHandlerContract
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
        if (count($this->args) === 1) {
            $this->args[1] = 'null';
        }
    }

    /**
     * Скомпилировать директиву.
     *
     * @return string Строка для замены вызова директивы на php код.
     */
    public function execute(): string
    {
        return Compiler::wrapPHP(sprintf('%s::pasteSection(%s, %s)', static::class, ...$this->args));
    }

    /**
     * Пишет в документ контент секции.
     *
     * Зависит от директивы Framework\Calibri\Directives\SectionDirective.
     *
     * @param string $sectionName Название секции
     * @param string|null $default Значение по умолчанию
     * @return void
     */
    public static function pasteSection(string $sectionName, ?string $default = null): void
    {
        $sectionContent = SectionDirective::getSection($sectionName);

        if (is_null($sectionContent)) {
            echo $default ?? '';
        }

        echo $sectionContent;
    }
}
