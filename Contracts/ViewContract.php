<?php

namespace Framework\Calibri\Contracts;

interface ViewContract
{
    /**
     * TODO: Undocumented function
     *
     * @param string $view
     * @param array<mixed> $arguments
     */
    public function __construct(string $view, array $arguments = []);

    /**
     * TODO: Undocumented function
     *
     * @param string $namespace
     * @param string $path
     * @return void
     */
    public static function addNamespace(string $namespace, string $path);

    /**
     * TODO: Undocumented function
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * TODO: Undocumented function
     *
     * @return string
     */
    public function __toString(): string;
}
