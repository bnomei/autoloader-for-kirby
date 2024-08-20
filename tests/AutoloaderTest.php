<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Bnomei\Autoloader;

beforeEach(function () {
    $this->dir = __DIR__.'/site/plugins/example';
    $this->dir2 = __DIR__.'/site/plugins/another';
    $this->dir3 = __DIR__.'/site/plugins/routastic';

    Autoloader::singletonClear(); // force reset singleton for this test
});
test('singleton', function () {
    // create
    $autoloader = Autoloader::singleton(['dir' => $this->dir]);
    expect($autoloader)->toBeInstanceOf(Autoloader::class);

    // from static cached
    $autoloader = Autoloader::singleton(['dir' => $this->dir]);
    expect($autoloader)->toBeInstanceOf(Autoloader::class);
});
test('global helper', function () {
    $autoloader = autoloader($this->dir);
    expect($autoloader)->toBeInstanceOf(Autoloader::class);

    $autoloader2 = autoloader($this->dir);
    expect($autoloader2)->toEqual($autoloader);

    $autoloader3 = autoloader($this->dir2);
    expect($autoloader === $autoloader3)->toBeFalse();
});
test('blueprints', function () {
    $autoloader = autoloader($this->dir);
    $blueprints = $autoloader->blueprints();
    ray($blueprints)->purple();

    expect($blueprints)->toBeArray();
    expect($blueprints)->toHaveKey('files/touch');
    expect($blueprints)->toHaveKey('pages/default');
    expect($blueprints)->toHaveKey('pages/isphp');
    $this->assertArrayNotHasKey('page/isconf', $blueprints);
    expect($blueprints)->toHaveKey('users/test');
    expect($blueprints)->toHaveKey('users/dyn');
    expect($blueprints['files/touch'])->toBeArray();
    expect($blueprints['pages/isphp'])->toBeArray();
    expect($blueprints['pages/default'])->toBeArray();
    expect($blueprints['users/test'])->toBeArray();
    expect($blueprints['users/dyn'])->toBeArray();
});
test('classes', function () {
    $autoloader = autoloader($this->dir);
    $classes = $autoloader->classes();

    expect($classes)->toBeArray();
    expect($classes)->toHaveKey('mega');
    expect($classes)->toHaveKey('ueber');
    expect(class_exists('Alpha\\Mega'))->toBeTrue();
    expect(trait_exists('Alpha\\Traits\\Ueber'))->toBeTrue();
});
test('collections', function () {
    $autoloader = autoloader($this->dir);
    $collections = $autoloader->collections();

    expect($collections)->toBeArray();
    expect($collections)->toHaveKey('colle');
    expect($collections)->toHaveKey('withUpper');
    expect($collections['colle'])->toBeCallable();
    expect($collections['withUpper'])->toBeCallable();
});
test('commands', function () {
    $autoloader = autoloader($this->dir);
    $commands = $autoloader->commands();

    expect($commands)->toBeArray();
    expect($commands)->toHaveKey('tecom');
    expect($commands['tecom']['description'])->toBeString();
    expect($commands['tecom']['args'])->toBeArray();
    expect($commands['tecom']['command'])->toBeCallable();

    expect($commands)->toHaveKey('sub:subcom');
    expect($commands['sub:subcom']['description'])->toBeString();
    expect($commands['sub:subcom']['args'])->toBeArray();
    expect($commands['sub:subcom']['command'])->toBeCallable();
});
test('controllers', function () {
    $autoloader = autoloader($this->dir);
    $controllers = $autoloader->controllers();

    expect($controllers)->toBeArray();
    expect($controllers)->toHaveKey('default');
    expect($controllers)->toHaveKey('default.json');
    expect($controllers['default'])->toBeCallable();
    expect($controllers['default.json'])->toBeCallable();
});
test('block models', function () {
    $autoloader = autoloader($this->dir);
    $models = $autoloader->blockModels();

    expect($models)->toBeArray();
    expect($models)->toHaveKey('very-amaze');
    expect($models)->toHaveKey('bloba');
    expect(class_exists('VeryAmazeBlock'))->toBeTrue();

    // exists but kirby will not find it since
    // "some" and "somename\somepage" do not match
    expect(class_exists('SomeName\\BlobaBlock'))->toBeTrue();
});
test('page models', function () {
    $autoloader = autoloader($this->dir);
    $models = $autoloader->pageModels();

    expect($models)->toBeArray();
    expect($models)->toHaveKey('some');
    expect($models)->toHaveKey('other');
    expect($models)->toHaveKey('just-another');
    expect(class_exists('OtherPage'))->toBeTrue();
    expect(class_exists('JustAnotherPage'))->toBeTrue();

    // exists but kirby will not find it since
    // "some" and "somename\somepage" do not match
    expect(class_exists('SomeName\\SomePage'))->toBeTrue();
});
test('user models', function () {
    $autoloader = autoloader($this->dir);
    $models = $autoloader->userModels();

    expect($models)->toBeArray();
    expect($models)->toHaveKey('editor');
    expect(class_exists('EditorUser'))->toBeTrue();
});
test('routes', function () {
    $autoloader = autoloader($this->dir3);
    $routes = $autoloader->routes();

    expect($routes)->toBeArray();
    expect($routes)->toHaveCount(3);
    usort($routes, function ($a, $b) {
        return strcmp($a['pattern'], $b['pattern']);
    });
    expect($routes[0]['pattern'])->toEqual('routastic');
    expect($routes[0]['action']())->toEqual('index');
    expect($routes[1]['pattern'])->toEqual('routastic/(:any)/register');
    expect($routes[1]['action']())->toEqual('register');
    expect($routes[2]['pattern'])->toEqual('routastic/(:any)/unregister');
    expect($routes[2]['action']())->toEqual('unregister');

    $apiRoutes = $autoloader->apiRoutes();
    usort($apiRoutes, function ($a, $b) {
        return strcmp($a['pattern'], $b['pattern']);
    });
    expect($apiRoutes)->toHaveCount(1);
    expect($apiRoutes[0]['pattern'])->toEqual('routastic/(:any)');
    expect($apiRoutes[0]['action']('hello'))->toEqual('api.index.hello');
});
test('snippets', function () {
    $autoloader = autoloader($this->dir);
    $snippets = $autoloader->snippets();

    expect($snippets)->toBeArray();
    expect($snippets)->toHaveKey('snippet1');

    //$this->assertArrayNotHasKey('snippet1.config', $snippets);
    expect($snippets)->toHaveKey('sub/snippet2');
    expect($snippets['snippet1'])->toBeFile();
    expect($snippets['sub/snippet2'])->toBeFile();
});
test('templates', function () {
    $autoloader = autoloader($this->dir);
    $templates = $autoloader->templates();

    expect($templates)->toBeArray();
    expect($templates)->toHaveKey('default');
    expect($templates)->toHaveKey('default.json');
    expect($templates['default'])->toBeFile();
});
test('translations', function () {
    $autoloader = autoloader($this->dir);
    $translations = $autoloader->translations();

    expect($translations)->toBeArray();
    expect($translations['de']['lang'])->toEqual('Deutsch');
    expect($translations['en']['lang'])->toEqual('English');
    expect($translations['jp']['lang'])->toEqual('日本語');
});

it('has a helper to transform the key', function () {
    expect(Autoloader::pascalToKebabCase('SomeName'))->toBe('some-name')
        ->and(Autoloader::pascalToCamelCase('SomeName'))->toBe('someName')
        ->and(Autoloader::pascalToDotCase('SomeName'))->toBe('some.name');
});

it('can merge options', function () {
    $autoloader = autoloader($this->dir, [
        'blockModels' => [
            'transform' => fn ($key) => md5($key),
        ],
    ])->toArray();

    expect($autoloader['blockModels'])->toHaveKey(md5('Bloba'));
});

it('can merge roots', function () {
    $autoloader = autoloader($this->dir)->toArray([
        'options' => [
            'test' => 'Test',
        ],
        'blueprints' => [
            'fields/test' => [ // <-- this must be merged
                'type' => 'info',
                'text' => 'Test',
            ],
        ],
    ]);

    expect($autoloader['blueprints']['pages/isphp'])->toBeArray();
    expect($autoloader['blueprints']['fields/test'])->toBeArray();
});
