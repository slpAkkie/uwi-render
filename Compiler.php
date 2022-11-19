<?php

namespace Services\Calibri;

use Services\Calibri\Contracts\CompilerContract;
use Services\Calibri\Contracts\DirectiveHandlerContract;
use Services\Calibri\Directives\ConditionDirective;
use Services\Calibri\Directives\ExtendsDirective;
use Services\Calibri\Directives\IncludeDirective;
use Services\Calibri\Directives\SectionDirective;
use Services\Calibri\Directives\YieldDirective;

class Compiler implements CompilerContract
{
    /**
     * Путь к папке для хранения кэша скомпилированных представлений.
     *
     * Используется константа APP_ROOT_PATH, поэтому она должна быть инициализирована
     * в корне проекта, до того, как подключается компилятор представлений.
     *
     * @var string
     */
    protected const CACHE_PATH = APP_ROOT_PATH . '/storage/framework/cache/views';

    protected const PARAM_SEPARATOR = ';';

    /**
     * Список зарегистрированных обработчиков директив.
     *
     * По умолчанию указаны базовые обработчики. Их отключение может привести к
     * неработоспособности компилятора.
     *
     * @var array<string, string>
     */
    protected static array $directiveHandlers = [
        'extends' => ExtendsDirective::class,
        'section|endsection' => SectionDirective::class,
        'yield' => YieldDirective::class,
        'include' => IncludeDirective::class,
        'if|elseif|else|endif' => ConditionDirective::class,
    ];

    /**
     * Указывает но то, есть ли уже скомпилированный файл для этого представления в кэше.
     *
     * @var boolean
     */
    protected bool $cached;

    /**
     * Контент представления во время компиляции.
     *
     * @var string
     */
    protected string $content;

    /**
     * Информация о стэке вызовов компилятора.
     *
     * @var array<array<string, mixed>>
     */
    protected static array $stack = [];

    /**
     * Инициализация компилятора для представления.
     *
     * @param string $viewPath Абсолютный путь к представлению в ФС.
     * @param array<mixed> $arguments Аргументы для представления
     *                                (Доступны и тем представлениям,
     *                                которые будут вызваны внутри, например,
     *                                шаблонам и подключенным представлениям).
     */
    public function __construct(
        /**
         * Абсолютный путь к представлению в ФС.
         *
         * @var string
         */
        protected string $viewPath,
        array $arguments = [],
    ) {
        $this->cached = file_exists($this->getCacheFilePath());

        $this->content = !$this->cached
            ? file_get_contents($this->viewPath)
            : '';

        static::$stack[] = [
            'arguments' => $arguments,
            'EOFCallbacks' => [],
        ];
    }

    /**
     * Дестрокутор для компилятор - удаляет свой стэк.
     */
    public function __destruct()
    {
        array_pop(static::$stack);
    }

    /**
     * Компилирует представление, сохраняет в кэш и возвращает выполненный результат.
     *
     * @return string
     */
    public function compile(): string
    {
        $cacheFilePath = $this->getCacheFilePath();

        // Если представление уже было скомпилировано, то выполняем его из кэша и возвращаем.
        if ($this->cached) {
            return $this->getExecutedFileContent($cacheFilePath);
        }

        // Обрабатываем директивы.
        $this->content = preg_replace_callback(
            '/\[\[([^:\s]*)(\s?:\s?(.*?))?\]\]/',
            static::class . '::directiveHandler',
            file_get_contents($this->viewPath)
        );

        // Применяем EOF колбэк.
        $this->content .= array_reduce(
            static::$stack[array_key_last(static::$stack)]['EOFCallbacks'],
            fn ($postContent, $callback) => $postContent . $callback($this),
            ''
        );

        // Раскрываем интерполирование переменных в представлениях.
        $this->content = preg_replace_callback(
            '/{{(.*?)}}/',
            fn ($matches) => Compiler::wrapPHPEcho(trim($matches[1])),
            $this->content
        );



        // Сохраняем скомпилированное представление в файл.
        file_put_contents($cacheFilePath, trim($this->content));

        // Выполняем из кэша и возвращаем.
        $content = $this->getExecutedFileContent($cacheFilePath);

        if (defined('APP_DEBUG') && APP_DEBUG) {
            unlink($cacheFilePath);
        }

        return $content;
    }

    /**
     * Обрабатывает вызов директивы в представлении.
     *
     * @param array<string> $directiveData информации о директиве.
     * @return string PHP код (или другая строка) которая должна заменить вызов директивы.
     */
    protected function directiveHandler(array $directiveData): string
    {
        $directive = trim($directiveData[1]);

        $args = array_map(
            fn ($arg) => trim($arg),
            key_exists(3, $directiveData) ? explode(self::PARAM_SEPARATOR, trim($directiveData[3])) : []
        );

        return $this->applyDirective($directive, $args);
    }

    /**
     * Запускает обработчик директивы и передает ей аргументы.
     *
     * @param string $directive Одно из названий директивы в списке привязок.
     * @param array<mixed> $args Аргументы для выполнения директивы.
     * @return string PHP код (или другая строка) которая должна заменить вызов директивы.
     */
    protected function applyDirective(string $directive, array $args = []): string
    {
        $handlerClass = $this->getDirectiveHandler($directive);
        if (is_null($handlerClass)) {
            throw new \InvalidArgumentException(
                "Для директивы [{$directive}] не установлен обработчик"
            );
        }

        $directiveHandler = new $handlerClass($directive, $args);

        return $directiveHandler->execute();
    }

    /**
     * Получить класс-обработчик для директивы по ее названию.
     *
     * @param string $directive Название директивы
     * @return string|null Если директива зарегистрирована вернется строка, в противном случае null
     */
    protected function getDirectiveHandler(string $directive): ?string
    {
        foreach (static::$directiveHandlers as $directives => $handler) {
            if (in_array($directive, explode('|', $directives))) {
                return $handler;
            }
        }

        return null;
    }

    /**
     * Поулчить хэш для компилируемого файла.
     *
     * @return string
     */
    protected function getCacheFilePath(): string
    {
        return self::CACHE_PATH . '/' . hash_file('sha1', $this->viewPath) . '.php';
    }

    /**
     * Выполнить файл из кэша, раскрыв для него аргументы стэка.
     *
     * @param string $___cachedFile Абсолютный путь к файлу кэша.
     * @return string Контент выполненного представления.
     */
    protected function getExecutedFileContent(string $___cachedFile): string
    {
        ob_start();
        extract(array_reduce(
            static::$stack,
            fn ($args, $stackArgs) => [
                ...$args,
                ...$stackArgs['arguments'],
            ],
            []
        ));
        require($___cachedFile);

        return ob_get_clean();
    }

    /**
     * Добавляет колбэк на событие EOF, вызываемое по окончанию первичной компиляции файла.
     *
     * @param \Closure $callback
     * @return void
     */
    public static function onEOF(\Closure $callback): void
    {
        static::$stack[array_key_last(static::$stack)]['EOFCallbacks'][] = $callback;
    }

    /**
     * Добавить контент в результирующий файл представления.
     *
     * @param string $postContent Контент для добавления
     * @param boolean $EOL Закончить вставку переносом строки
     * @return void
     */
    public function pushContent(string $postContent, bool $EOL = true): void
    {
        $this->content .= $postContent . ($EOL ? PHP_EOL : '');
    }

    /**
     * Обернуть php код в теги.
     *
     * @param string|array $code Код для обертывания. Если аргумента является массивом,
     *                           то для каждой строки будет добавлена точка с запятой
     *                           при ее отсутствии.
     * @return string php код, обернутый в теги.
     */
    public static function wrapPHP(string|array $code): string
    {
        return '<?php ' . (is_array($code)
            ? join(PHP_EOL, array_map(fn ($cmd) => !str_ends_with($cmd, ';') ? $cmd . ';' : $cmd, $code))
            : $code) . ' ?>';
    }

    /**
     * Обернуть php выражение в тег вставки.
     *
     * @param string $expression Php выражение.
     * @return string Php выражение обернутое в тег вставки.
     */
    public static function wrapPHPEcho(string $expression): string
    {
        return '<?= ' . $expression . ' ?>';
    }

    /**
     * Компилирует представление с переданными аргументами.
     *
     * @param string $viewPath Абсолютный путь до файла представления.
     * @param array<mixed> $arguments Аргументы для представления.
     * @return string Выполненное представление.
     */
    public static function compileView(string $viewPath, array $arguments = []): string
    {
        return (new self($viewPath, $arguments))->compile();
    }

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
    public static function registerDirective(string|array $name, string $handlerClass): void
    {
        $name = is_array($name) ? join('|', $name) : $name;

        if (!is_subclass_of($handlerClass, DirectiveHandlerContract::class)) {
            throw new \InvalidArgumentException(
                "Класс-обработчик директив [{$handlerClass}] должен реализовывать интерфейс [" . DirectiveHandlerContract::class . ']'
            );
        }

        static::$directiveHandlers[$name] = $handlerClass;
    }
}
