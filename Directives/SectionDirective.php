<?php

namespace Services\Calibri\Directives;

use Services\Calibri\Compiler;
use Services\Calibri\Contracts\DirectiveHandlerContract;

class SectionDirective implements DirectiveHandlerContract
{
    /**
     * Список секций с их контентом.
     *
     * @var array<string, string>
     */
    protected static array $sections = [];

    /**
     * Последняя открытая секция.
     *
     * @var string|null
     */
    protected static ?string $openedSection = null;

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
            'section' => $this->getOpenSectionCode(),
            'endsection' => $this->getCloseSectionCode(),
        };
    }

    /**
     * Обрабатывает директиву section.
     *
     * @return string Строка для замены вызова директивы на php код.
     */
    protected function getOpenSectionCode(): string
    {
        return match (count($this->args)) {
            1 => Compiler::wrapPHP(sprintf('%s::openSection(%s)', static::class, ...$this->args)),
            2 => Compiler::wrapPHP(sprintf('%s::addSection(%s, %s)', static::class, ...$this->args)),
        };
    }

    /**
     * Обрабатывает директиву endsection.
     *
     * @return string Строка для замены вызова директивы на php код.
     */
    protected function getCloseSectionCode(): string
    {
        return Compiler::wrapPHP(sprintf('%s::closeSection()', static::class));
    }

    /**
     * Записывает контент секции.
     *
     * @param string $sectionName
     * @param string $content
     * @return void
     */
    public static function addSection(string $sectionName, string $content): void
    {
        static::$sections[$sectionName] = $content;
    }

    /**
     * Открывает запись секции.
     *
     * @param string $sectionName
     * @return void
     */
    public static function openSection(string $sectionName): void
    {
        static::$openedSection = $sectionName;
        ob_start();
    }

    /**
     * Закрывает запись секции.
     *
     * @return void
     */
    public static function closeSection(): void
    {
        static::addSection(static::$openedSection, ob_get_clean());
        static::$openedSection = null;
    }

    /**
     * Возвращает контент секции.
     *
     * @param string $sectionName Название секции.
     * @return string|null Строка, если секция есть, в противном случае null.
     */
    public static function getSection(string $sectionName): ?string
    {
        if (!key_exists($sectionName, static::$sections)) {
            return null;
        }

        return static::$sections[$sectionName];
    }
}
