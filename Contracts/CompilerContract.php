<?php

namespace Services\Calibri\Contracts;

interface CompilerContract
{
    /**
     * Инициализация компилятора для представления.
     *
     * @param string $viewPath Абсолютный путь к представлению в ФС.
     * @param array<mixed> $arguments Аргументы для представления
     *                                (Доступны и тем представлениям,
     *                                которые будут вызваны внутри, например,
     *                                шаблонам и подключенным представлениям).
     */
    public function __construct(string $viewPath, array $arguments = []);

    /**
     * Компилирует представление, сохраняет в кэш и возвращает выполненный результат.
     *
     * @return string
     */
    public function compile(): string;

    /**
     * Добавляет колбэк на событие EOF, вызываемое по окончанию первичной компиляции файла.
     *
     * @param \Closure $callback
     * @return void
     */
    public static function onEOF(\Closure $callback): void;

    /**
     * Добавить контент в результирующий файл представления.
     *
     * @param string $postContent Контент для добавления
     * @param boolean $EOL Закончить вставку переносом строки
     * @return void
     */
    public function pushContent(string $postContent, bool $EOL = true): void;

    /**
     * Обернуть php код в теги.
     *
     * @param string|array $code Код для обертывания. Если аргумента является массивом,
     *                           то для каждой строки будет добавлена точка с запятой
     *                           при ее отсутствии.
     * @return string php код, обернутый в теги.
     */
    public static function wrapPHP(string|array $code): string;

    /**
     * Обернуть php выражение в тег вставки.
     *
     * @param string $expression Php выражение.
     * @return string Php выражение обернутое в тег вставки.
     */
    public static function wrapPHPEcho(string $expression): string;

    /**
     * Компилирует представление с переданными аргументами.
     *
     * @param string $viewPath Абсолютный путь до файла представления.
     * @param array<mixed> $arguments Аргументы для представления.
     * @return string Выполненное представление.
     */
    public static function compileView(string $viewPath, array $arguments = []): string;

    /**
     * Регистрирует новую директиву.
     *
     * Если обработчик для директивы с таким параметром $name уже существует, то он будет заменен,
     * но если существует несколько обработчиков, включающие в себя $name, то исполняться будет
     * тот, который добавлен раньше.
     *
     * @param string|array $name Директивы для обработки.
     * @param string $handlerClass Класс-обработчик.
     * @return void
     * @throws \InvalidArgumentException
     */
    public static function registerDirective(string|array $name, string $handlerClass): void;
}
