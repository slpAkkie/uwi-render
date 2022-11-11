<?php

namespace Framework\Calibri;

use Framework\Calibri\Contracts\ViewContract;

class View implements ViewContract
{
    /**
     * Расширение файлов для представлений.
     *
     * @var string
     */
    protected const VIEW_EXT = '.clbr.php';

    /**
     * Пространство имен для представлений по умолчанию.
     *
     * @var string
     */
    protected const DEFAULT_NAMESPACE = '__app_views';

    /**
     * Привязки пространств имен к папкам с представлениями.
     *
     * @var array<string, string>
     */
    protected static array $namespaceMapping = [
        self::DEFAULT_NAMESPACE => APP_ROOT_PATH . '/views',
    ];

    /**
     * Пространство имен представления.
     *
     * @var string
     */
    protected string $viewNamespace;

    /**
     * Путь к представлению относительно пространства имен.
     *
     * @var string
     */
    protected string $viewRelativePath;

    /**
     * Абсолютный путь к представлению в ФС.
     *
     * @var string
     */
    protected string $viewAbsolutePath;

    /**
     * Инициализировать новый объект представления.
     *
     * @param string $viewName Имя представления с пространством имен или без него.
     * @param array<mixed> $arguments Аргументы для представления.
     */
    public function __construct(
        /**
         * Имя представления с пространством имен или без него.
         *
         * @var string
         */
        protected string $viewName,

        /**
         * Аргументы для представления.
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
     * Добавить привязку для пространства имен.
     * Если привязка для пространство имен уже есть, то она будет заменена.
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
     * Получить скомпилированный контент представления.
     *
     * @return string
     */
    public function getContent(): string
    {
        return Compiler::compileView($this->viewAbsolutePath, $this->arguments);
    }

    /**
     * Привести объект представления к строке.
     * То же самое, что и вызов getContent().
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getContent();
    }
}
