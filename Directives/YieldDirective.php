<?php

namespace Framework\Calibri\Directives;

use Framework\Calibri\Compiler;
use Framework\Calibri\Contracts\DirectiveHandlerContract;

class YieldDirective implements DirectiveHandlerContract
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
        if (count($this->args) === 1) {
            $this->args[1] = 'null';
        }
    }

    /**
     * TODO: Undocumented function
     *
     * @return string
     */
    public function execute(): string
    {
        return Compiler::wrapPHP(sprintf('%s::pasteSection(%s, %s)', static::class, ...$this->args));
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $sectionName
     * @param string|null $default
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
