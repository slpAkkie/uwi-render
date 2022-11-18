<?php

namespace Services\Calibri\Contracts;

interface ViewContract
{
    /**
     * Инициализировать новый объект представления.
     *
     * @param string $viewName Имя представления с пространством имен или без него.
     * @param array<mixed> $arguments Аргументы для представления.
     */
    public function __construct(string $viewName, array $arguments = []);

    /**
     * Добавить привязку для пространства имен.
     * Если привязка для пространство имен уже есть, то она будет заменена.
     *
     * @param string $namespace
     * @param string $path
     * @return void
     */
    public static function addNamespace(string $namespace, string $path);

    /**
     * Получить скомпилированный контент представления.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Привести объект представления к строке.
     * То же самое, что и вызов getContent().
     *
     * @return string
     */
    public function __toString(): string;
}
