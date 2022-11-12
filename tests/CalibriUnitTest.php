<?php

use Framework\Calibri\View;
use TestModule\Test;

class CalibriUnitTest
{
    public function all(): void
    {
        $this->testViewInit();
        $this->testViewCompilingWithInterpolation();
        $this->testViewCompiling();
    }

    public function testViewInit(): void
    {
        Test::printInfo('Тест создания экземпляра View');

        Test::run(
            desc: 'С не правильным указанием представления',
            test: function () {
                Test::assertException(function () {
                    new TestView('namespace1::namespace2::view');
                });
            }
        );

        Test::run(
            desc: 'С указанием не существующего пространства имен',
            test: function () {
                Test::assertException(function () {
                    new TestView('notExist::main');
                });
            }
        );

        Test::run(
            desc: 'С указанием существующего пространства имен',
            test: function () {
                Test::assertNonException(function () {
                    TestView::addNamespace('tpl', APP_ROOT_PATH . '/templates');
                    new TestView('tpl::main');
                    TestView::removeNamespace('tpl');
                });
            }
        );

        Test::run(
            desc: 'С указанием существующего пространства имен, но не существующего представления',
            test: function () {
                Test::assertException(function () {
                    TestView::addNamespace('tpl', APP_ROOT_PATH . '/templates');
                    new TestView('tpl::notExist');
                    TestView::removeNamespace('tpl');
                });
            }
        );

        Test::run(
            desc: 'Без указания пространства имен',
            test: function () {
                Test::assertNonException(function () {
                    TestView::addNamespace('tpl', APP_ROOT_PATH . '/templates');
                    new TestView('home');
                    TestView::removeNamespace('tpl');
                });
            }
        );

        Test::run(
            desc: 'Без пространства имен для представления, находящегося в папке',
            test: function () {
                Test::assertNonException(function () {
                    new TestView('errors/404');
                });
            }
        );

        Test::run(
            desc: 'С пространством имен для представления, находящегося в папке',
            test: function () {
                Test::assertNonException(function () {
                    TestView::addNamespace('errors', APP_ROOT_PATH . '/views/errors');
                    new TestView('errors::templates/exception');
                    TestView::removeNamespace('errors');
                });
            }
        );
    }

    public function testViewCompilingWithInterpolation(): void
    {
        Test::printInfo('Тест компиляции представлений с использованием в них интерполяции');

        Test::run(
            desc: 'Один переданный аргумент и одно выражение',
            test: function () {
                $year = date('Y');
                $view = new View('interpolation', [
                    'word' => 'World'
                ]);
                $viewCotent = trim($view);
                Test::assertEqual($viewCotent, "Hello World! {$year} year");
            }
        );
    }

    public function testViewCompiling(): void
    {
        Test::printInfo('Тест компиляции представлений');

        Test::run(
            desc: 'Файл без директив',
            test: function () {
                $view = new View('common');
                $viewCotent = trim($view);
                Test::assertEqual($viewCotent, 'Common');
            }
        );

        Test::run(
            desc: 'Файл с директивами extends и шаблон с директивами yield',
            test: function () {
                TestView::addNamespace('tpl', APP_ROOT_PATH . '/templates');
                $view = new View('home');
                $viewCotent = trim($view);
                Test::assertEqual($viewCotent, "Main Template: page-title=Home page; body=Home page body; default=Default value");
                TestView::removeNamespace('tpl');
            }
        );

        Test::run(
            desc: 'Файл с использованием директив условий if/elseif/else/endif',
            test: function () {
                $content = trim((new View('conditions', [
                    'greater' => 2,
                    'less' => 1
                ]))->getContent());
                Test::assertEqual($content, 'True');
            }
        );

        Test::run(
            desc: 'Файл с использованием директивы include',
            test: function () {
                $view = new View('include');
                $viewCotent = trim($view);
                Test::assertEqual($viewCotent, "Hello World!");
            }
        );
    }
}

class TestView extends View
{
    public static function removeNamespace(string $namespace)
    {
        unset(self::$namespaceMapping[$namespace]);
    }
}
