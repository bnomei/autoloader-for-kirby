# Autoloader for Kirby

![Release](https://flat.badgen.net/packagist/v/bnomei/autoloader-for-kirby?color=ae81ff)
![Downloads](https://flat.badgen.net/packagist/dt/bnomei/autoloader-for-kirby?color=272822)
[![Twitter](https://flat.badgen.net/badge/twitter/bnomei?color=66d9ef)](https://twitter.com/bnomei)

Helper Class to automatically load various Kirby extensions in a plugin

## Commerical Usage

This package is free but if you use it in a commercial project please consider to

-   [make a donation 🍻](https://www.paypal.me/bnomei/5) or
-   [buy me ☕](https://buymeacoff.ee/bnomei) or
-   [buy a Kirby license using this affiliate link](https://a.paddle.com/v2/click/1129/35731?link=1170)

## Installation

```bash
composer require bnomei/kirby3-autoloader-for-kirby
```

> This is NOT a kirby plugin. This is a composer package because that actually makes it easier to setup and does not mess with the loading order of extensions.

### Autoloading of extensions

Add the autoloader for each extension type you want once and it will register all files in subfolders correctly. The following extensions can be autoloaded:

#### Supported Extensions

- [x] blueprints
- [x] collections
- [x] controllers
- [x] models (with registering the class)
- [x] snippets
- [x] templates

#### Roadmap

- [ ] translastions

**/site/plugins/example/index.php**
```php
<?php

Kirby::plugin('bnomei/example', [
    'options' => [
        // options
    ],
    'blueprints' => autoloader(__DIR__)->blueprints(),
    'collections' => autoloader(__DIR__)->collections(),
    'controllers' => autoloader(__DIR__)->controllers(),
    'models' => autoloader(__DIR__)->models(),
    'snippets' => autoloader(__DIR__)->snippets(),
    'templates' => autoloader(__DIR__)->templates(),
    // other extensions
]);
```

## Disclaimer

This package is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/autoloader-for-kirby/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this package in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.
