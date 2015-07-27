<?php
/**
 * Part of the Robin Radic's PHP packages.
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes;

use App;
use ArrayAccess;
use ArrayIterator;
use Config;
use Countable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\NamespacedItemResolver;
use IteratorAggregate;
use Laradic\Themes\Contracts\NavigationFactory;
use Laradic\Themes\Contracts\ThemeFactory as ThemeFactoryContract;
use RuntimeException;

/**
 * This is the ThemeFactory class.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class ThemeFactory implements ArrayAccess, Countable, IteratorAggregate, ThemeFactoryContract
{

    /**
     * Contains all the resolved theme instances using slug => Class instance association
     *
     * @var Theme[]
     */
    protected $themes = [ ];

    /**
     * The active theme instance
     *
     * @var \Laradic\Themes\Theme
     */
    protected $active;

    /**
     * The default theme instance
     *
     * @var \Laradic\Themes\Theme
     */
    protected $default;

    /**
     * The filesystem object
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The theme package's view finder to locate theme views
     *
     * @var \Laradic\Themes\ThemeViewFinder
     */
    protected $finder;

    /**
     * The event dispatcher
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;


    /**
     * Filesystem paths to directories where themes are installed
     *
     * @var $paths array
     */
    protected $paths = [ ];

    /**
     * Collection of all theme publishers that have been added
     *
     * @var Publisher[]
     */
    protected $publishers = [ ];

    /**
     * The navigation instance
     *
     * @var \Laradic\Themes\Contracts\NavigationFactory
     */
    protected $navigation;

    /**
     * The breadcrumb instance
     *
     * @var \DaveJamesMiller\Breadcrumbs\Manager
     */
    protected $breadcrumbs;

    /**
     * The asset factory instance
     *
     * @var \Laradic\Themes\Assets\AssetFactory
     */
    protected $assets;

    protected $themeClass;

    /**
     * Instantiates the class
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Filesystem\Filesystem            $files
     * @param \Illuminate\Contracts\Events\Dispatcher      $events
     */
    public function __construct(Filesystem $files, Dispatcher $events)
    {
        $this->files      = $files;
        $this->dispatcher = $events;
    }

    /**
     * Set the active theme that should be used
     *
     * @param string|\Laradic\Themes\Theme $theme The slug or Theme instance
     * @return $this
     */
    public function setActive($theme)
    {
        if ( ! $theme instanceof Theme )
        {
            $theme = $this->resolveTheme($theme);
        }
        else
        {
            if ( ! array_key_exists($theme->getSlug(), $this->themes) )
            {
                $this->themes[ $theme->getSlug() ] = $theme;
            }
        }

        $this->active = $theme;

        return $this;
    }

    /**
     * Get the activated theme
     *
     * @return \Laradic\Themes\Theme
     */
    public function getActive()
    {
        if ( ! isset($this->active) )
        {
            throw new RuntimeException('Could not get active theme because there isn\'t any defined');
        }

        return $this->active;
    }

    /**
     * Get the default theme
     *
     * @return \Laradic\Themes\Theme
     */
    public function getDefault()
    {
        if ( ! isset($this->default) )
        {
            return;
        }

        return $this->default;
    }

    /**
     * Set the default theme
     *
     * @param string|\Laradic\Themes\Theme $theme The slug or Theme instance
     */
    public function setDefault($theme)
    {
        if ( ! $theme instanceof Theme )
        {
            $theme = $this->resolveTheme($theme);
        }
        else
        {
            if ( ! array_key_exists($theme->getSlug(), $this->themes) )
            {
                $this->themes[ $theme->getSlug() ] = $theme;
            }
        }
        $this->default = $theme;
    }

    /**
     * Resolve a theme using it's slug. It will check all theme paths for the required theme.
     * It will instantiate the theme, register it with the factory and return it.
     *
     * @param string $slug The theme slug
     * @return Theme
     */
    public function resolveTheme($slug)
    {
        if ( array_key_exists($slug, $this->themes) )
        {
            return $this->themes[ $slug ];
        }

        list($area, $key) = with(new NamespacedItemResolver)->parseKey($slug);

        foreach ( $this->paths[ 'themes' ] as $path )
        {
            $themePath = $this->getThemePath($path, $key, $area);

            if ( $this->files->isDirectory($themePath) )
            {
                return $this->themes[ $slug ] = new $this->themeClass($this, $this->dispatcher, $themePath);
            }
        }
    }

    /**
     * Returns all resolved theme slugs
     *
     * @return array
     */
    public function all()
    {
        return array_keys($this->themes);
    }

    /**
     * Get a theme with the provided slug, equal to resolveTheme
     *
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
     *
     * @return int
     */
    public function count()
    {
        return count($this->themes);
    }

    /**
     * Add a namespace to the theme
     *
     * @param string $name
     * @param string $dirName
     */
    public function addNamespace($name, $dirName)
    {
        $location = $this->getPath('namespaces') . '/' . $dirName;
        //$view     = app('view');

        app('view')->addLocation($location);
        app('view')->addNamespace($name, $location);

        return $this;
    }

    /**
     * Get a path by type, as configured in config.
     *
     * @param string $type views, assets, namespaces or packages
     * @return string
     */
    public function getPath($type)
    {
        return $this->paths[ $type ];
    }

    /**
     * Get paths cascadingly for the defined options.
     *
     * @param string      $cascadeType The type, either namespaces, packages
     * @param null|string $cascadeName The namespaced or package name
     * @param null|string $pathType    The path type like views or assets
     * @param null|string $theme
     * @return array
     */
    public function getCascadedPaths($cascadeType, $cascadeName = null, $pathType = null, $theme = null)
    {
        $paths  = array();
        $looped = array();

        $current = is_null($theme) ? $this->getActive() : $this->resolveTheme($theme);

        while ( true )
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
     * Get the path tot the theme
     *
     * @param      $path
     * @param      $key
     * @param null $area
     * @return string
     */
    public function getThemePath($path, $key, $area = null)
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
     * Register/add a theme publisher that publishes as a package
     *
     * @param string      $package    Package name
     * @param string      $sourcePath Path to the theme
     * @param string|null $theme      Exclude to a specific theme using tthis slug
     */
    public function addPackagePublisher($package, $sourcePath, $theme = null)
    {
        $theme = is_null($theme) ? $this->getActive() : $this->resolveTheme($theme);

        $this->publishers[ $package ] = Publisher::create($this->getFiles())
            ->asPackage($package)
            ->from($sourcePath)
            ->toTheme($theme);

        return $this;
    }

    /**
     * Register/add a theme publisher that publishes as a namespace
     *
     * @param string      $namespace  Name
     * @param string      $sourcePath Path to the theme
     * @param string|null $theme      Exclude to a specific theme using tthis slug
     */
    public function addNamespacePublisher($namespace, $sourcePath, $theme = null)
    {
        $theme = is_null($theme) ? $this->getActive() : $this->resolveTheme($theme);

        $this->publishers[ $namespace ] = Publisher::create($this->getFiles())
            ->asNamespace($namespace)
            ->from($sourcePath)
            ->toTheme($theme);

        return $this;
    }

    /**
     * Publish an namespace or package
     *
     * @param null $namespaceOrPackage
     */
    public function publish($namespaceOrPackage = null, $theme = null)
    {
        if ( is_null($namespaceOrPackage) )
        {
            foreach ( $this->publishers as $publisher )
            {
                if ( ! is_null($theme) )
                {
                    $publisher->toTheme($theme instanceof Theme ? $theme : $this->resolveTheme($theme));
                }
                $publisher->publish();
            }
        }
        else
        {
            if ( isset($this->publishers[ $namespaceOrPackage ]) )
            {
                if ( ! is_null($theme) )
                {
                    $this->publishers[ $namespaceOrPackage ]->toTheme($theme instanceof Theme ? $theme : $this->resolveTheme($theme));
                }
                $this->publishers[ $namespaceOrPackage ]->publish();
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
     * Boot the active theme
     *
     * @param bool $bootParent
     */
    public function boot($bootParent = true, $bootDefault = false)
    {
        $this->getActive()->boot();
        if ( $bootParent and $this->getActive()->hasParent() )
        {
            $this->getActive()->getParentTheme()->boot();
        }
        if ( $bootDefault )
        {
            $this->getDefault()->boot();
        }
    }

    //
    /* GETTERS AND SETTERS */
    //

    /**
     * Get the theme view finder instance
     *
     * @return \Laradic\Themes\ThemeViewFinder
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
     * Get the filesystem object
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set the filesystem object
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
        return $this->themes[ $key ];
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
            $this->themes[ $key ] = $value;
        }
    }

    /**
     * offsetUnset
     *
     * @param mixed $key
     */
    public function offsetUnset($key)
    {
        unset($this->themes[ $key ]);
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

    /**
     * Get the NavigationFactory instance
     *
     * @return NavigationFactory
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * Sets the NavigationFactory instance
     *
     * @param NavigationFactory $navigation
     * @return NavigationFactory
     */
    public function setNavigation($navigation)
    {
        $this->navigation = $navigation;

        return $this;
    }

    /**
     * Get the value of breadcrumbs
     *
     * @return \DaveJamesMiller\Breadcrumbs\Manager
     */
    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    /**
     * Sets the value of breadcrumbs
     *
     * @param \DaveJamesMiller\Breadcrumbs\Manager $breadcrumbs
     * @return $this
     */
    public function setBreadcrumbs($breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;

        return $this;
    }

    /**
     * Get the asset factory instance
     *
     * @return \Laradic\Themes\Assets\AssetFactory
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * Sets the value of assets
     *
     * @param Assets\AssetFactory $assets
     * @return $this
     */
    public function setAssets($assets)
    {
        $this->assets = $assets;

        return $this;
    }

    /**
     * get themeClass value
     *
     * @return mixed
     */
    public function getThemeClass()
    {
        return $this->themeClass;
    }

    /**
     * Set the themeClass value
     *
     * @param mixed $themeClass
     * @return ThemeFactory
     */
    public function setThemeClass($themeClass)
    {
        $this->themeClass = $themeClass;

        return $this;
    }

    /**
     * Set the paths value
     *
     * @param array $paths
     * @return ThemeFactory
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;

        return $this;
    }


}
