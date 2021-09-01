<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Bnomei\Autoloader;
use PHPUnit\Framework\TestCase;

final class AutoloaderTest extends TestCase
{
    private $dir;

    public function setUp(): void
    {
        $this->dir = __DIR__ . '/site/plugins/example';
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
        $this->assertIsCallable($collections['colle']);
    }

    public function testControllers()
    {
        $autoloader = autoloader($this->dir);
        $controllers = $autoloader->controllers();

        $this->assertIsArray($controllers);
        $this->assertArrayHasKey('default', $controllers);
        $this->assertIsCallable($controllers['default']);
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

    public function testSnippets()
    {
        $autoloader = autoloader($this->dir);
        $snippets = $autoloader->snippets();

        $this->assertIsArray($snippets);
        $this->assertArrayHasKey('snippet1', $snippets);
        $this->assertArrayNotHasKey('snippet1.config', $snippets);
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
        $this->assertArrayNotHasKey('default.blade', $templates);
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
