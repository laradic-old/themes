<?php
/**
 * Part of the Laradic packages.
 * MIT License and copyright information bundled with this package in the LICENSE file.
 */
namespace Laradic\Themes;

use App;
use ArrayAccess;
use ArrayIterator;
use Config;
use Countable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\NamespacedItemResolver;
use IteratorAggregate;
use Laradic\Themes\Contracts\ThemeFactory as ThemeFactoryContract;
use RuntimeException;

/**
 * Class ThemeFactory
 *
 * @package     Laradic\Themes
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class ThemeFactory implements ArrayAccess, Countable, IteratorAggregate, ThemeFactoryContract
{

    /**
     * Contains all the resolved theme instances using slug => Class instance association
     * @var Theme[]
     */
    protected $themes = [];

    /**
     * The active theme instance
     * @var \Laradic\Themes\Theme
     */
    protected $active;

    /**
     * The default theme instance
     * @var \Laradic\Themes\Theme
     */
    protected $default;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Illuminate\View\ViewFinderInterface
     */
    protected $finder;

    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Filesystem paths to directories where themes are installed
     * @var $paths array
     */
    protected $paths = [];

    /** @var array */
    protected $config;

    /** @var Publisher[] */
    protected $publishers = [];

    /**
     * Instantiates the class
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Filesystem\Filesystem            $files
     * @param \Illuminate\Contracts\Events\Dispatcher      $events
     */
    public function __construct(Application $app, Filesystem $files, Dispatcher $events)
    {
        $this->app        = $app;
        $this->files      = $files;
        $this->dispatcher = $events;
    }

    /**
     * setConfig
     *
     * @param $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->paths  = $config['paths'];
        $this->config = $config;

        return $this;
    }

    /**
     * Set the active theme
     *
     * @param string|\Laradic\Themes\Theme $theme The slug or Theme instance
     * @return $this
     */
    public function setActive($theme)
    {
        if ( ! $theme instanceof Theme )
        {
            $this->active = $this->resolveTheme($theme);
        }

        return $this;
    }

    /**
     * Get the activated theme
     *
     * @return \Laradic\Themes\Theme
     */
    public function getActive()
    {
        if ( ! $this->active )
        {
            throw new RuntimeException('Could not get active theme because there isn\'t any defined');
        }

        return $this->active;
    }

    /**
     * Get the default theme
     * @return \Laradic\Themes\Theme
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set the default theme
     *
     * @param string|\Laradic\Themes\Theme $theme The slug or Theme instance
     */
    public function setDefault($theme)
    {
        $theme       = $this->resolveTheme($theme);
        $this->default = $theme;
    }

    /**
     * resolveTheme
     *
     * @param $slug
     * @return Theme
     */
    public function resolveTheme($slug)
    {
        if ( in_array($slug, $this->themes) )
        {
            return $this->themes[$slug];
        }

        $resolver = new NamespacedItemResolver;
        list($area, $key) = $resolver->parseKey($slug);

        foreach ($this->paths as $path)
        {
            $themePath = $this->getThemePath($path[0], $key, $area);

            if ( $this->files->isDirectory($themePath) )
            {
                $class = Config::get('radic_themes.themeClass');

                return $this->themes[$slug] = new $class($this, $this->dispatcher, $themePath);
            }
        }
    }

    public function all()
    {
        return array_keys($this->themes);
    }

    /**
     * Get a theme with the provided slug
     * @return \Laradic\Themes\Theme
     */
    public function get($slug)
    {
        return $this->resolveTheme($slug);
    }

    /**
     * Check if a theme is present
     *
     * @param $slug
     * @return bool
     */
    public function has($slug)
    {
        $this->resolveTheme($slug);

        return in_array($slug, array_keys($this->themes));
    }

    /**
     * Get the number of themes
     * @return int
     */
    public function count()
    {
        return count($this->themes);
    }

    /**
     * addNamespace
     *
     * @param $id
     * @param $dirName
     */
    public function addNamespace($id, $dirName)
    {
        $location = $this->getPath('namespaces') . '/' . $dirName;
        $view     = $this->app->make('view');
        $view->addLocation($location);
        $view->addNamespace($id, $location);
    }

    /**
     * getPath
     *
     * @param $type
     * @return mixed
     */
    public function getPath($type)
    {
        return $this->paths[$type];
    }

    /**
     * getCascadedPaths
     *
     * @param      $cascadeType
     * @param null $cascadeName
     * @param null $pathType
     * @param null $theme
     * @return array
     */
    public function getCascadedPaths($cascadeType, $cascadeName = null, $pathType = null, $theme = null)
    {
        $paths  = array();
        $looped = array();

        $current = is_null($theme) ? $this->getActive() : $this->resolveTheme($theme);

        while (true)
        {
            $paths[]  = $current->getCascadedPath($cascadeType, $cascadeName, $pathType);
            $looped[] = $current;

            if ( ! $parent = $current->getParentTheme() )
            {
                break;
            }

            if ( $parent === $this->getDefault() )
            {
                break;
            }

            $current = $parent;
        }

        if ( $default = $this->getDefault() and ! in_array($default, $looped) )
        {
            $paths[] = $default->getCascadedPath($cascadeType, $cascadeName, $pathType);
        }

        return $paths;
    }

    /**
     * getThemePath
     *
     * @param      $path
     * @param      $key
     * @param null $area
     * @return string
     */
    protected function getThemePath($path, $key, $area = null)
    {
        $split = '/(\/|\\\)/';

        if ( ($keyCount = count(preg_split($split, $key))) > 2 )
        {
            throw new RuntimeException("Theme had folder depth of [{$keyCount}] however it must be less than or equal to [2].");
        }

        if ( isset($area) )
        {
            if ( ($areaCount = count(preg_split($split, $area))) != 1 )
            {
                throw new RuntimeException("Theme area had folder depth of [{$areaCount}] however it must match [1].");
            }

            return "{$path}/{$area}/{$key}";
        }

        return "{$path}/{$key}";
    }

    /**
     * addPackagePublisher
     *
     * @param      $package
     * @param      $sourcePath
     * @param null $theme
     */
    public function addPackagePublisher($package, $sourcePath, $theme = null)
    {
        $theme = is_null($theme) ? $this->getActive() : $this->resolveTheme($theme);

        $this->publishers[$package] = Publisher::create($this->getFiles())
            ->asPackage($package)
            ->from($sourcePath)
            ->toTheme($theme);
    }

    /**
     * addNamespacePublisher
     *
     * @param      $namespace
     * @param      $sourcePath
     * @param null $theme
     */
    public function addNamespacePublisher($namespace, $sourcePath, $theme = null)
    {
        $theme = is_null($theme) ? $this->getActive() : $this->resolveTheme($theme);

        $this->publishers[$namespace] = Publisher::create($this->getFiles())
            ->asNamespace($namespace)
            ->from($sourcePath)
            ->toTheme($theme);
    }

    /**
     * publish
     *
     * @param null $namespaceOrPackage
     */
    public function publish($namespaceOrPackage = null)
    {
        if ( is_null($namespaceOrPackage) )
        {
            foreach ($this->publishers as $publisher)
            {
                $publisher->publish();
            }
        }
        else
        {
            if ( isset($this->publishers[$namespaceOrPackage]) )
            {
                $this->publishers[$namespaceOrPackage]->publish();
            }
            else
            {
                throw new \InvalidArgumentException("Could not publish [$namespaceOrPackage]. The publisher could not be resolved for $namespaceOrPackage");
            }
        }
    }

    /**
     * Get the value of publishers
     *
     * @return Publisher[]
     */
    public function getPublishers()
    {
        return $this->publishers;
    }



    //
    /* EVENTS */
    //
    /**
     * boot
     */
    public function boot()
    {
        $this->getActive()->boot();
    }

    //
    /* GETTERS AND SETTERS */
    //
    /**
     * getApplication
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * setApplication
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return $this
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * getFinder
     *
     * @return \Illuminate\View\ViewFinderInterface
     */
    public function getFinder()
    {
        return $this->finder;
    }

    /**
     * setFinder
     *
     * @param \Laradic\Themes\ThemeViewFinder $finder
     * @return $this
     */
    public function setFinder(ThemeViewFinder $finder)
    {
        $this->finder = $finder;

        return $this;
    }

    /**
     * getFiles
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * setFiles
     *
     * @param $files
     * @return $this
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * offsetExists
     *
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->themes);
    }

    /**
     * offsetGet
     *
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->themes[$key];
    }

    /**
     * offsetSet
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        if ( is_null($key) )
        {
            $this->themes[] = $value;
        }
        else
        {
            $this->themes[$key] = $value;
        }
    }

    /**
     * offsetUnset
     *
     * @param mixed $key
     */
    public function offsetUnset($key)
    {
        unset($this->themes[$key]);
    }

    /**
     * getIterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->themes);
    }

}
