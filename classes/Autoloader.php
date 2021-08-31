<?php

declare(strict_types=1);

namespace Bnomei;

use Symfony\Component\Finder\Finder;

final class Autoloader
{
    // exclude files like filename.config.(php|yml)
    public const PHP = '/^[\w\d\-\_]+\.php$/';
    public const YML = '/^[\w\d\-\_]+\.yml$/';
    public const PHP_OR_YML = '/^[\w\d\-\_]+\.(php|yml)$/';

    /** @var self */
    private static $singleton;

    /** @var array */
    private $options;

    /** @var array */
    private $registry;

    public function __construct(array $options = [])
    {
        $this->options = array_merge_recursive([
            'blueprints' => [
                'folder' => 'blueprints',
                'name' => static::PHP_OR_YML,
                'key' => 'relativepath',
                'require' => false,
            ],
            'collections' => [
                'folder' => 'collections',
                'name' => static::PHP,
                'key' => 'relativepath',
                'require' => true,
            ],
            'controllers' => [
                'folder' => 'controllers',
                'name' => static::PHP,
                'key' => 'filename',
                'require' => true,
            ],
            'models' => [
                'folder' => 'models',
                'name' => static::PHP,
                'key' => 'classname',
                'require' => false,
            ],
            'snippets' => [
                'folder' => 'snippets',
                'name' => static::PHP,
                'key' => 'relativepath',
                'require' => false,
            ],
            'templates' => [
                'folder' => 'templates',
                'name' => static::PHP,
                'key' => 'filename',
                'require' => false,
            ],
            /* TODO: translations
        	'translations' => [
        		'folder' => 'translations',
        		'name' => static::PHP,
        		'key' => 'relativepath',
        		'require' => true,
        	],
        	*/
        ], $options);

        if (!array_key_exists('dir', $this->options)) {
            throw new \Exception("Autoloader needs a directory to start scanning at.");
        }

        $this->registry = [];
    }

    private function registry(string $type): array
    {
        // only register once
        if (array_key_exists($type, $this->registry)) {
            return $this->registry[$type];
        }

        $options = $this->options[$type];

        $this->registry[$type] = [];
        $finder = (new Finder())->files()
            ->name($options['name'])
            ->in($this->options['dir'] . '/' . $options['folder']);

        foreach ($finder as $file) {
            $key = '';
            $class = '';
            $split = explode('.', $file->getPathname());
            $extension = array_pop($split);
            if ($options['key'] === 'relativepath') {
                $key = $file->getRelativePathname();
                $key = strtolower(str_replace('.' . $extension, '', $key));
            } elseif ($options['key'] === 'filename') {
                $key = basename($file->getRelativePathname());
                $key = strtolower(str_replace('.' . $extension, '', $key));
            } elseif ($options['key'] === 'classname') {
                $key = basename($file->getRelativePathname());
                $key = str_replace('.' . $extension, '', $key);
                $class = $key;
                $key = strtolower($key);
                if ($classFile = file_get_contents($file->getPathname())) {
                    if (preg_match('/^namespace (.*);$/im', $classFile, $matches) === 1) {
                        $class = $matches[1] . '\\' . $class;
                    }
                    $this->load([
                        $class => $file->getRelativePathname(),
                    ], $this->options['dir'] . '/' . $options['folder']);
                }
                $pageAt = strpos('Page', $key);
                if ($pageAt === strlen($key) - 4) {
                    $key = substr($key, 0, -4);
                }
            }
            if (empty($key)) {
                continue;
            }
            
            if ($options['key'] === 'classname') {
                $this->registry[$type][$key] = $class;
            } elseif ($options['require'] && $extension && strtolower($extension) === 'php') {
                $path = $file->getPathname();
                $this->registry[$type][$key] = require_once $path;
            } else {
                $this->registry[$type][$key] = $file->getRealPath();
            }
        }

        return $this->registry[$type];
    }

    public function blueprints(): array
    {
        return $this->registry('blueprints');
    }

    public function collections(): array
    {
        return $this->registry('collections');
    }

    public function controllers(): array
    {
        return $this->registry('controllers');
    }

    public function models(): array
    {
        return $this->registry('models');
    }

    public function snippets(): array
    {
        return $this->registry('snippets');
    }

    public function templates(): array
    {
        return $this->registry('templates');
    }

    public static function singleton(array $options = []): self
    {
        if (self::$singleton) {
            return self::$singleton;
        }
        self::$singleton = new self($options);
        return self::$singleton;
    }

    // https://github.com/getkirby/kirby/blob/c77ccb82944b5fa0e3a453b4e203bd697e96330d/config/helpers.php#L505
    /**
     * A super simple class autoloader
     *
     * @param array $classmap
     * @param string $base
     * @return void
     */
    private function load(array $classmap, string $base = null)
    {
        // convert all classnames to lowercase
        $classmap = array_change_key_case($classmap);

        spl_autoload_register(function ($class) use ($classmap, $base) {
            $class = strtolower($class);

            if (!isset($classmap[$class])) {
                return false;
            }

            if ($base) {
                include $base . '/' . $classmap[$class];
            } else {
                include $classmap[$class];
            }
        });
    }
}
