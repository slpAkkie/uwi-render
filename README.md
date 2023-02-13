# Описание

PHP шаблонизатор страниц

## Использование

По умолчанию файлы шаблонов хранятся в папке `views` в корне вашего проекта

Шаблоны должны быть расширения `.clbr.php`

Для использования шаблона в вашем проекте используйте следующую конструкцию:

```php
new View('welcome');
```

В данном примере будет создан объект для файла `welcome.clbr.php` в папке `views`

Если шаблон находится внутри другой папки, просто укажите путь отновительно пространства имен (при его отсутствии относительно `views`)

### Пространства имен для шаблонов

Для шаблонов можно указать пространство имен, предварительно зарегистрировав его:

```php
View::addNamespace('blog', APP_ROOT_PATH . '/blog');

new View('blog::main');
```

Тогда для доступа к шаблону нужно будет указать его пространство имен разделяя его от имени шаблона символами `::`

### Параметры шаблонов

В шаблоны можно передавать параметры вторым аргументов при создании объекта

```php
new View('main', [ 'title' => 'Главная страница' ])
```

В шаблоне эти аргументы будут доступны как переменные, которые можно вставить следующей конструкцией:

```php
{{ $title }}
```

С помощью `{{ }}` можно вывести любое выражение, которое будет расчитано динамически при получении контента

### Директивы шаблонов

Внутри своих шаблонов вы можете использовать директивы

Общий пример директив

```html
[[имяДирективы: параметр1; параметр2]]
```

#### Условия (if)

```html
[[if: выражение]]
<!-- If True -->
[[elseif: выражение]]
<!-- Elseif True -->
[[else]]
<!-- Else -->
[[endif]]
```

#### Подключение других файлов (include)

```html
[[include: имяФайла]]
```

Имя файла указывается в таком же синтаксисе как и при создании объекта шаблона

#### Расширение шаблона (extends)

```html
[[extends: имяФайла]]
```

Имя файла указывается в таком же синтаксисе как и при создании объекта шаблона

При использовании этой директивы содержимое основного файла должно состоять из определения секция (директива section)

#### Секции (section)

Если секция состоит просто из текста

```html
[[section: имяСекции; контентСекции]]
```

Если нужно больше контента, можно использовать другой вариант определения секции

```html
[[section: имяСекции]]
Контент секции
[[endsection]]
```

В таком варианте контент секции может содержить другие директивы и вставки с помощью `{{ }}`

#### Вставка секции (yield)

Внутри шаблонов для вставки контента секции используйте директиву `yield`

```html
[[yield: имяСекции; контентПоУмолчанию]]
```

Значение по умолчанию будет отображено, если секция не была указана

### Кэширование

По умолчанию Calibri кэширует скомпилированные шаблоны, что может стать проблемой при разработке, когда шаблоны очень часто меняются, поэтому, чтобы отключить кэширование установить контсанту `APP_DEDUG` в значение `true`

# Автор

Shamanin Alexandr (@slpAkkie)

# Version

1.1.0
