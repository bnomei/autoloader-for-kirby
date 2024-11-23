# Autoloader for Kirby

![Release](https://flat.badgen.net/packagist/v/bnomei/autoloader-for-kirby?color=ae81ff)
![Downloads](https://flat.badgen.net/packagist/dt/bnomei/autoloader-for-kirby?color=272822)
[![Coverage](https://flat.badgen.net/codeclimate/coverage/bnomei/autoloader-for-kirby)](https://codeclimate.com/github/bnomei/autoloader-for-kirby)
[![Maintainability](https://flat.badgen.net/codeclimate/maintainability/bnomei/autoloader-for-kirby)](https://codeclimate.com/github/bnomei/autoloader-for-kirby)
[![Discord](https://flat.badgen.net/badge/discord/bnomei?color=7289da)](https://discordapp.com/users/bnomei)

Helper to automatically load various Kirby extensions in a plugin

## Installation

```bash
composer require bnomei/autoloader-for-kirby
```

## This package is NOT a kirby plugin

- This is a composer package because that actually makes it easier to setup and does not mess with the loading order of extensions.
- Being a package it also can be used not only for local plugins but also as a composer dependency within plugins published online.

### Autoloading of extensions

Add the autoloader for each extension type you want once and it will register all files in subfolders correctly.

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

- Loading translations from yaml or json files is added by this package and not originally part of kirby core.
- The `classes` autoloader is very basic. Using a custom array with kirby's `load()`-helper or composers psr-4 autoloading is recommended.
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

The package does come with [default settings](https://github.com/bnomei/autoloader-for-kirby/blob/main/classes/Autoloader.php#L27) to fit most usecases.

## Suggestion

This plugin works great in combination with my [Kirby CLI Tool](https://github.com/bnomei/kirby3-plopfile) which helps you to create extension files faster.

## Disclaimer

This package is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/autoloader-for-kirby/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this package in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.
