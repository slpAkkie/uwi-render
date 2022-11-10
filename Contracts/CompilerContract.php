<?php

namespace Framework\Calibri\Contracts;

interface CompilerContract
{
    /**
     * TODO: Undocumented function
     *
     * @param string $viewPath
     * @param array<mixed> $arguments
     */
    public function __construct(string $viewPath, array $arguments = []);

    /**
     * TODO: Undocumented function
     *
     * @param string $viewPath
     * @return string
     */
    public function compile(): string;

    /**
     * TODO: Undocumented function
     *
     * @param string $key
     * @param mixed $val
     * @return void
     */
    public function share(string $key, mixed $val): void;

    /**
     * TODO: Undocumented function
     *
     * @param string $key
     * @return mixed
     */
    public function getShared(string $key): mixed;

    /**
     * TODO: Undocumented function
     *
     * @param \Closure $callback
     * @return void
     */
    public function onEOF(\Closure $callback): void;

    /**
     * TODO: Undocumented function
     *
     * @param string $postContent
     * @param boolean $EOL
     * @return void
     */
    public function pushContent(string $postContent, bool $EOL = true): void;

    /**
     * TODO: Undocumented function
     *
     * @param string|array $code
     * @return string
     */
    public static function wrapPHP(string|array $code): string;

    /**
     * TODO: Undocumented function
     *
     * @param string $expression
     * @return string
     */
    public static function wrapPHPEcho(string $expression): string;

    /**
     * TODO: Undocumented function
     *
     * @param string $viewPath
     * @param array<mixed> $arguments
     * @return string
     */
    public static function compileView(string $viewPath, array $arguments = []): string;

    /**
     * TODO: Undocumented function
     *
     * @param string|array $name
     * @param string $handlerClass
     * @return void
     */
    public static function registerDirective(string|array $name, string $handlerClass): void;
}
