<?php

namespace Framework\Calibri;

use Framework\Calibri\Contracts\ViewContract;

class View implements ViewContract
{
    /**
     * TODO: Undocumented const
     *
     * @var string
     */
    protected const VIEW_EXT = '.clbr.php';

    /**
     * TODO: Undocumented const
     *
     * @var string
     */
    protected const DEFAULT_NAMESPACE = '__app_views';

    /**
     * TODO: Undocumented variable
     *
     * @var array<string, string>
     */
    protected static array $namespaceMapping = [
        self::DEFAULT_NAMESPACE => APP_ROOT_PATH . '/views',
    ];

    /**
     * TODO: Undocumented variable
     *
     * @var string
     */
    protected string $viewNamespace;

    /**
     * TODO: Undocumented variable
     *
     * @var string
     */
    protected string $viewRelativePath;

    /**
     * TODO: Undocumented variable
     *
     * @var string
     */
    protected string $viewAbsolutePath;

    /**
     * TODO: Undocumented function
     *
     * @param string $view
     * @param array<mixed> $arguments
     */
    public function __construct(
        /**
         * TODO: Undocumented variable
         *
         * @var string
         */
        protected string $viewName,

        /**
         * TODO: Undocumented variable
         *
         * @var array<mixed>
         */
        protected array $arguments = []
    ) {
        $viewData = explode('::', $this->viewName);

        // Указание представление без пространства имен.
        if (count($viewData) === 1) {
            $this->viewNamespace = self::DEFAULT_NAMESPACE;
            $this->viewRelativePath = $viewData[0];
        }
        // Указание представления с пространстов имен
        else if (count($viewData) === 2) {
            $this->viewNamespace = $viewData[0];
            $this->viewRelativePath = $viewData[1];
        }
        // Не правильной указание представления (Несколько разделителей пространства имен).
        else {
            throw new \InvalidArgumentException(
                "Представление [{$this->viewName}] указано не правильно"
            );
        }

        // Указанное пространство имен не найдено.
        if (!key_exists($this->viewNamespace, self::$namespaceMapping)) {
            throw new \InvalidArgumentException(
                "Пространство имен [{$this->viewNamespace}] не найдено"
            );
        }

        $this->viewAbsolutePath = self::$namespaceMapping[$this->viewNamespace] . '/' . $this->viewRelativePath .  self::VIEW_EXT;

        // Файл для представления не найден.
        if (!file_exists($this->viewAbsolutePath)) {
            throw new \InvalidArgumentException(
                "Представление [{$this->viewAbsolutePath}] не найдено"
            );
        }
    }

    /**
     * TODO: Undocumented function
     *
     * @param string $namespace
     * @param string $path
     * @return void
     */
    public static function addNamespace(string $namespace, string $path)
    {
        self::$namespaceMapping[$namespace] = $path;
    }

    /**
     * TODO: Undocumented function
     *
     * @return string
     */
    public function getContent(): string
    {
        return Compiler::compileView($this->viewAbsolutePath, $this->arguments);
    }

    /**
     * TODO: Undocumented function
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getContent();
    }
}
