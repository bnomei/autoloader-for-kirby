<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Bnomei\Autoloader;
use PHPUnit\Framework\TestCase;

final class AutoloaderTest extends TestCase
{
    private $dir;
    private $dir2;

    public function setUp(): void
    {
        $this->dir = __DIR__ . '/site/plugins/example';
        $this->dir2 = __DIR__ . '/site/plugins/another';
        $this->dir3 = __DIR__ . '/site/plugins/routastic';
    }

    public function testSingleton()
    {
        // create
        $autoloader = Autoloader::singleton(['dir' => $this->dir]);
        $this->assertInstanceOf(Autoloader::class, $autoloader);

        // from static cached
        $autoloader = Autoloader::singleton(['dir' => $this->dir]);
        $this->assertInstanceOf(Autoloader::class, $autoloader);
    }

    public function testGlobalHelper()
    {
        $autoloader = autoloader($this->dir);
        $this->assertInstanceOf(Autoloader::class, $autoloader);

        $autoloader2 = autoloader($this->dir);
        $this->assertEquals($autoloader, $autoloader2);

        $autoloader3 = autoloader($this->dir2);
        $this->assertFalse($autoloader === $autoloader3);
    }

    public function testBlueprints()
    {
        $autoloader = autoloader($this->dir);
        $blueprints = $autoloader->blueprints();

        $this->assertIsArray($blueprints);
        $this->assertArrayHasKey('files/touch', $blueprints);
        $this->assertArrayHasKey('pages/default', $blueprints);
        $this->assertArrayHasKey('pages/isphp', $blueprints);
        $this->assertArrayNotHasKey('page/isconf', $blueprints);
        $this->assertArrayHasKey('users/admin', $blueprints);
        $this->assertFileExists($blueprints['files/touch']);
        $this->assertFileExists($blueprints['pages/isphp']);
        $this->assertFileExists($blueprints['pages/default']);
        $this->assertFileExists($blueprints['users/admin']);
    }

    public function testClasses()
    {
        $autoloader = autoloader($this->dir);
        $classes = $autoloader->classes();

        $this->assertIsArray($classes);
        $this->assertArrayHasKey('mega', $classes);
        $this->assertArrayHasKey('ueber', $classes);
        $this->assertTrue(class_exists('Alpha\\Mega'));
        $this->assertTrue(trait_exists('Alpha\\Traits\\Ueber'));
    }

    public function testCollections()
    {
        $autoloader = autoloader($this->dir);
        $collections = $autoloader->collections();

        $this->assertIsArray($collections);
        $this->assertArrayHasKey('colle', $collections);
        $this->assertArrayHasKey('withUpper', $collections);
        $this->assertIsCallable($collections['colle']);
        $this->assertIsCallable($collections['withUpper']);
    }

    public function testControllers()
    {
        $autoloader = autoloader($this->dir);
        $controllers = $autoloader->controllers();

        $this->assertIsArray($controllers);
        $this->assertArrayHasKey('default', $controllers);
        $this->assertArrayHasKey('default.json', $controllers);
        $this->assertIsCallable($controllers['default']);
        $this->assertIsCallable($controllers['default.json']);
    }

    public function testBlockModels()
    {
        $autoloader = autoloader($this->dir);
        $models = $autoloader->blockModels();

        $this->assertIsArray($models);
        $this->assertArrayHasKey('amaze', $models);
        $this->assertArrayHasKey('bloba', $models);
        $this->assertTrue(class_exists('AmazeBlock'));

        // exists but kirby will not find it since
        // "some" and "somename\somepage" do not match
        $this->assertTrue(class_exists('SomeName\\BlobaBlock'));
    }

    public function testPageModels()
    {
        $autoloader = autoloader($this->dir);
        $models = $autoloader->pageModels();

        $this->assertIsArray($models);
        $this->assertArrayHasKey('some', $models);
        $this->assertArrayHasKey('other', $models);
        $this->assertTrue(class_exists('OtherPage'));

        // exists but kirby will not find it since
        // "some" and "somename\somepage" do not match
        $this->assertTrue(class_exists('SomeName\\SomePage'));
    }

    public function testUserModels()
    {
        $autoloader = autoloader($this->dir);
        $models = $autoloader->userModels();

        $this->assertIsArray($models);
        $this->assertArrayHasKey('editor', $models);
        $this->assertTrue(class_exists('EditorUser'));
    }

    public function testRoutes()
    {
        $autoloader = autoloader($this->dir3);
        $routes = $autoloader->routes();

        $this->assertIsArray($routes);
        $this->assertCount(3, $routes);
        usort($routes, function($a, $b) {
           return strcmp($a['pattern'], $b['pattern']);
        });
        $this->assertEquals('routastic', $routes[0]['pattern']);
        $this->assertEquals('index', $routes[0]['action']());
        $this->assertEquals('routastic/(:any)/register', $routes[1]['pattern']);
        $this->assertEquals('register', $routes[1]['action']());
        $this->assertEquals('routastic/(:any)/unregister', $routes[2]['pattern']);
        $this->assertEquals('unregister', $routes[2]['action']());

        $apiRoutes = $autoloader->apiRoutes();
        usort($apiRoutes, function($a, $b) {
            return strcmp($a['pattern'], $b['pattern']);
        });
        $this->assertCount(1, $apiRoutes);
        $this->assertEquals('routastic/(:any)', $apiRoutes[0]['pattern']);
        $this->assertEquals('api.index.hello', $apiRoutes[0]['action']('hello'));
    }

    public function testSnippets()
    {
        $autoloader = autoloader($this->dir);
        $snippets = $autoloader->snippets();

        $this->assertIsArray($snippets);
        $this->assertArrayHasKey('snippet1', $snippets);
        //$this->assertArrayNotHasKey('snippet1.config', $snippets);
        $this->assertArrayHasKey('sub/snippet2', $snippets);
        $this->assertFileExists($snippets['snippet1']);
        $this->assertFileExists($snippets['sub/snippet2']);
    }

    public function testTemplates()
    {
        $autoloader = autoloader($this->dir);
        $templates = $autoloader->templates();

        $this->assertIsArray($templates);
        $this->assertArrayHasKey('default', $templates);
        $this->assertArrayHasKey('default.json', $templates);
        $this->assertFileExists($templates['default']);
    }

    public function testTranslations()
    {
        $autoloader = autoloader($this->dir);
        $translations = $autoloader->translations();

        $this->assertIsArray($translations);
        $this->assertEquals('Deutsch', $translations['de']['lang']);
        $this->assertEquals('English', $translations['en']['lang']);
        $this->assertEquals('日本語', $translations['jp']['lang']);
    }
}
