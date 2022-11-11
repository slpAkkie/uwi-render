<?php

namespace Framework\Calibri\Contracts;

interface DirectiveHandlerContract
{
    /**
     * Инициализировать обработчик.
     *
     * @param string $directive
     * @param array<string> $args
     */
    public function __construct(string $directive, array $args);

    /**
     * Скомпилировать директиву.
     *
     * @return string Строка для замены вызова директивы на php код.
     */
    public function execute(): string;
}
