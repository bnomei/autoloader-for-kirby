# Autoloader for Kirby

[![Kirby 5](https://flat.badgen.net/badge/Kirby/5?color=ECC748)](https://getkirby.com)
![PHP 8.2](https://flat.badgen.net/badge/PHP/8.2?color=4E5B93&icon=php&label)
![Release](https://flat.badgen.net/packagist/v/bnomei/autoloader-for-kirby?color=ae81ff&icon=github&label)
![Downloads](https://flat.badgen.net/packagist/dt/bnomei/autoloader-for-kirby?color=272822&icon=github&label)
[![Coverage](https://flat.badgen.net/codeclimate/coverage/bnomei/autoloader-for-kirby?icon=codeclimate&label)](https://codeclimate.com/github/bnomei/autoloader-for-kirby)
[![Maintainability](https://flat.badgen.net/codeclimate/maintainability/bnomei/autoloader-for-kirby?icon=codeclimate&label)](https://codeclimate.com/github/bnomei/autoloader-for-kirby/issues)
[![Discord](https://flat.badgen.net/badge/discord/bnomei?color=7289da&icon=discord&label)](https://discordapp.com/users/bnomei)
[![Buymecoffee](https://flat.badgen.net/badge/icon/donate?icon=buymeacoffee&color=FF813F&label)](https://www.buymeacoffee.com/bnomei)

Helper to automatically load various Kirby extensions in a plugin

## Installation

```bash
composer require bnomei/autoloader-for-kirby
```

## This package is NOT a Kirby plugin

This is a composer package because it is easier to set up and does not mess with the loading order of extensions.
- Being a package, it can also be used not only for local plugins but also as a composer dependency within plugins published online.

### Autoloading of extensions

Add the autoloader for each extension type you want once, and it will correctly register all files in subfolders.

#### Supported Extensions

The following extensions can be autoloaded:

- [x] blueprints (php or yml, classes)
- [x] classes (php)
- [x] collections (php)
- [x] commands (php)
- [x] controllers (php)
- [x] blockModels (php)
- [x] pageModels (php)
- [x] routes (php)
- [x] api/routes (php)
- [x] userModels (php)
- [x] snippets (php)
- [x] templates (php)
- [X] translations (php or yml or json)

#### Notes

- Loading translations from YAML or JSON files are added by this package and is not originally part of Kirby core.
- The `classes` autoloader is very basic. It is recommended that you use a custom array with Kirby's `load()` helper or composers psr-4 autoloading.
- The `routes` and `apiRoutes` autoloader is based on code from @tobimori and needs a file structure similar to Next.js [see examples](https://github.com/bnomei/autoloader-for-kirby/blob/main/tests/site/plugins/routastic).
- Blueprints loaded from classes need the [kirby-blueprints](https://github.com/bnomei/kirby-blueprints) plugin

## Usage

After requiring it as a dependency in either your project or plugin `composer.json` you can use the `autoload()`-helper to load various extension.

**/site/plugins/example/index.php**
```php
<?php

// only autoloader
Kirby::plugin('bnomei/example', autoloader(__DIR__)->toArray());
```

```php
<?php

// merge autoloader with custom config
Kirby::plugin('bnomei/example', autoloader(__DIR__)->toArray([
    'options' => [
        // options
    ],
    // other extensions
]));
```

```php
<?php

// optionally change some settings
/*
autoloader(__DIR__, [
    'snippets' => [
        'folder' => 'schnippschnapp',
    ],
]);
*/

autoloader(__DIR__)->classes();
// use a different folder
// autoloader(__DIR__)->classes('src');

// set each option explicitly without merging
Kirby::plugin('bnomei/example', [
    'options' => [
        // options
    ],
    'blueprints' => autoloader(__DIR__)->blueprints(),
    'collections' => autoloader(__DIR__)->collections(),
    'commands' => autoloader(__DIR__)->commands(),
    'controllers' => autoloader(__DIR__)->controllers(),
    'blockModels' => autoloader(__DIR__)->blockModels(),
    'pageModels' => autoloader(__DIR__)->pageModels(),
    'routes' => autoloader(__DIR__)->routes(),
    'userModels' => autoloader(__DIR__)->userModels(),
    'snippets' => autoloader(__DIR__)->snippets(),
    'templates' => autoloader(__DIR__)->templates(),
    'translations' => autoloader(__DIR__)->translations(),
    // other extensions
]);
```

## Settings

The package does come with [default settings](https://github.com/bnomei/autoloader-for-kirby/blob/main/classes/Autoloader.php#L27) to fit most usecases. But you can change them every time you call the `autoloader()`-helper for a different directory (aka in each plugin `index.php`-file).

**/site/plugins/example/index.php**
```php
<?php

Kirby::plugin('bnomei/example', autoloader(__DIR__, [
        'blockModels' => [
            // mapping BlockModel class names to file names, like
            // MyCustomBlock::class => 'my.custom' (site/blueprints/blocks/my.custom.yml)
            'transform' => fn ($key) => \Bnomei\Autoloader::pascalToDotCase($key),
        ],
    ])->toArray()
);
```

## Disclaimer

This package is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/autoloader-for-kirby/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this package in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.
