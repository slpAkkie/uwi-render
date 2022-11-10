<?php

namespace Framework\Calibri\Directives;

use Framework\Calibri\Compiler;
use Framework\Calibri\Contracts\DirectiveHandlerContract;

class SectionDirective implements DirectiveHandlerContract
{
    /**
     * TODO: Undocumented variable
     *
     * @var array<string, string>
     */
    protected static array $sections = [];

    /**
     * TODO: Undocumented variable
     *
     * @var string|null
     */
    protected static ?string $openedSection = null;

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
        return match ($this->directive) {
            'section' => $this->getOpenSectionCode(),
            'endsection' => $this->getCloseSectionCode(),
        };
    }

    /**
     * TODO: Undocumented function
     *
     * @return string
     */
    protected function getOpenSectionCode(): string
    {
        return match (count($this->args)) {
            1 => Compiler::wrapPHP(sprintf('%s::openSection(%s)', static::class, ...$this->args)),
            2 => Compiler::wrapPHP(sprintf('%s::addSection(%s, %s)', static::class, ...$this->args)),
        };
    }

    /**
     * TODO: Undocumented function
     *
     * @return string
     */
    protected function getCloseSectionCode(): string
    {
        return Compiler::wrapPHP(sprintf('%s::closeSection()', static::class));
    }

    /**
     * TODO: Undocumented function
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
     * TODO: Undocumented function
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
     * TODO: Undocumented function
     *
     * @return void
     */
    public static function closeSection(): void
    {
        $sectionContent = ob_get_clean();
        static::$sections[static::$openedSection] = $sectionContent;
        static::$openedSection = null;
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $sectionName
     * @return string|null
     */
    public static function getSection(string $sectionName): ?string
    {
        if (!key_exists($sectionName, static::$sections)) {
            return null;
        }

        return static::$sections[$sectionName];
    }
}
