<?php

namespace Framework\Calibri;

use Framework\Calibri\Contracts\CompilerContract;
use Framework\Calibri\Directives\ConditionDirective;
use Framework\Calibri\Directives\ExtendsDirective;
use Framework\Calibri\Directives\IncludeDirective;
use Framework\Calibri\Directives\SectionDirective;
use Framework\Calibri\Directives\YieldDirective;

class Compiler implements CompilerContract
{
    /**
     * TODO: Undocumented const
     *
     * @var string
     */
    protected const CACHE_PATH = APP_ROOT_PATH . '/storage/framework/cache/views';

    /**
     * TODO: Undocumented variable
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
     * TODO: Undocumented variable
     *
     * @var boolean
     */
    protected bool $cached;

    /**
     * TODO: Undocumented variable
     *
     * @var string
     */
    protected string $content;

    /**
     * TODO: Undocumented variable
     *
     * @var array<mixed>
     */
    protected array $EOFCallbacks = [];

    /**
     * TODO: Undocumented variable
     *
     * @var array<string, mixed>
     */
    protected array $shared = [];

    /**
     * TODO: Undocumented function
     *
     * @param string $viewPath
     * @param array<mixed> $arguments
     */
    public function __construct(
        protected string $viewPath,
        protected array $arguments = [],
    ) {
        $this->cached = file_exists($this->getCacheFilePath()) && false; // TODO: Enable cache

        $this->content = $this->cached
            ? file_get_contents($this->getCacheFilePath())
            : file_get_contents($this->viewPath);
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $viewPath
     * @return string
     */
    public function compile(): string
    {
        if ($this->cached) {
            return $this->content;
        }

        $this->content = preg_replace_callback(
            '/\[\[([^:\s]*)(\s?:\s?(.*?))?\]\]/',
            static::class . '::directiveHandler',
            file_get_contents($this->viewPath)
        );

        $this->content .= array_reduce(
            $this->EOFCallbacks,
            fn ($postContent, $callback) => $postContent . $callback(),
            ''
        );

        $this->content = preg_replace_callback(
            '/{{(.*?)}}/',
            fn ($matches) => Compiler::wrapPHPEcho(trim($matches[1])),
            $this->content
        );



        file_put_contents($this->getCacheFilePath(), trim($this->content));

        return $this->getExecutedContent($this->getCacheFilePath());
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $key
     * @param mixed $val
     * @return void
     */
    public function share(string $key, mixed $val): void
    {
        $this->shared[$key] = $val;
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $key
     * @return mixed
     */
    public function getShared(string $key): mixed
    {
        key_exists($key, $this->shared)
            ? $this->shared[$key]
            : null;
    }

    /**
     * TODO: Undocumented function
     *
     * @param array<string> $directiveData
     * @return string
     */
    protected function directiveHandler(array $directiveData): string
    {
        $directive = trim($directiveData[1]);

        $args = array_map(
            fn ($arg) => trim($arg),
            key_exists(3, $directiveData) ? explode('&', trim($directiveData[3])) : []
        );

        return $this->applyDirective($directive, $args);
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $directive
     * @param array $args
     * @return string
     */
    protected function applyDirective(string $directive, array $args): string
    {
        $handlerClass = $this->getDirectiveHandler($directive);
        if (is_null($handlerClass)) {
            throw new \InvalidArgumentException(
                "Для директивы [{$directive}] не установлен обработчик"
            );
        }

        $directiveHandler = new $handlerClass($this, $directive, $args);

        return $directiveHandler->execute();
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $directive
     * @return string|null
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
     * TODO: Undocumented function
     *
     * @return string
     */
    protected function getCacheFilePath(): string
    {
        return self::CACHE_PATH . '/' . hash_file('sha1', $this->viewPath) . '.php';
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $cachedFile
     * @return string
     */
    protected function getExecutedContent(string $cachedFile): string
    {
        ob_start();
        extract($this->arguments);
        require($cachedFile);

        return ob_get_clean();
    }

    /**
     * TODO: Undocumented function
     *
     * @param \Closure $callback
     * @return void
     */
    public function onEOF(\Closure $callback): void
    {
        $this->EOFCallbacks[] = $callback;
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $postContent
     * @param boolean $EOL
     * @return void
     */
    public function pushContent(string $postContent, bool $EOL = true): void
    {
        $this->content .= $postContent . ($EOL ? PHP_EOL : '');
    }

    /**
     * TODO: Undocumented function
     *
     * @param string|array $code
     * @return string
     */
    public static function wrapPHP(string|array $code): string
    {
        return '<?php ' . (is_array($code)
            ? join(PHP_EOL, array_map(fn ($cmd) => !str_ends_with($cmd, ';') ? $cmd . ';' : $cmd, $code))
            : $code) . ' ?>';
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $expression
     * @return string
     */
    public static function wrapPHPEcho(string $expression): string
    {
        return '<?= ' . $expression . ' ?>';
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $viewPath
     * @param array<mixed> $arguments
     * @return string
     */
    public static function compileView(string $viewPath, array $arguments = []): string
    {
        return (new self($viewPath, $arguments))->compile();
    }
}
