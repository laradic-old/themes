<?php namespace Laradic\Themes;

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

class ThemeFactory implements ArrayAccess, Countable, IteratorAggregate, ThemeFactoryContract
{

    /**
     * @var array
     */
    protected $themes = array();

    /** @var Theme */
    protected $active;

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
     * @var $paths array
     */
    protected $paths = [];

    /** @var array */
    protected $config;

    /** @var Publisher[] */
    protected $publishers = [];

    public function __construct(Application $app, Filesystem $files, Dispatcher $events)
    {
        $this->app        = $app;
        $this->files      = $files;
        $this->dispatcher = $events;
    }

    public function setConfig($config)
    {
        $this->paths  = $config['paths'];
        $this->config = $config;

        return $this;
    }

    public function setActive($active)
    {
        if ( ! $active instanceof Theme )
        {
            $this->active = $this->resolveTheme($active);
        }

        return $this;
    }

    /** @return Theme */
    public function getActive()
    {
        if ( ! $this->active )
        {
            throw new RuntimeException('Could not get active theme because there isn\'t any defined');
        }

        return $this->active;
    }

    /** @return Theme */
    public function getDefault()
    {
        return $this->default;
    }

    public function setDefault($default)
    {
        $default       = $this->resolveTheme($default);
        $this->default = $default;
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

    public function addNamespace($id, $dirName)
    {
        $location = $this->getPath('namespaces') . '/' . $dirName;
        $view     = $this->app->make('view');
        $view->addLocation($location);
        $view->addNamespace($id, $location);
    }


    public function getPath($type)
    {
        return $this->paths[$type];
    }

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

    public function addPackagePublisher($package, $sourcePath, $theme = null)
    {
        $theme = is_null($theme) ? $this->getActive() : $this->resolveTheme($theme);

        $this->publishers[$package] = Publisher::create($this->getFiles())
            ->asPackage($package)
            ->from($sourcePath)
            ->toTheme($theme);
    }

    public function addNamespacePublisher($namespace, $sourcePath, $theme = null)
    {
        $theme = is_null($theme) ? $this->getActive() : $this->resolveTheme($theme);

        $this->publishers[$namespace] = Publisher::create($this->getFiles())
            ->asNamespace($namespace)
            ->from($sourcePath)
            ->toTheme($theme);
    }

    public function publish($namespaceOrPackage = null)
    {
        if(is_null($namespaceOrPackage))
        {
            foreach($this->publishers as $publisher)
            {
                $publisher->publish();
            }
        }
        else
        {
            if(isset($this->publishers[$namespaceOrPackage]))
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
    public function boot()
    {
    }

    //
    /* GETTERS AND SETTERS */
    //
    public function getApplication()
    {
        return $this->app;
    }

    public function setApplication(Application $app)
    {
        $this->app = $app;

        return $this;
    }

    /** @return ThemeViewFinder */
    public function getFinder()
    {
        return $this->finder;
    }

    public function setFinder(ThemeViewFinder $finder)
    {
        $this->finder = $finder;

        return $this;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    public function count()
    {
        return count($this->themes);
    }

    public function offsetExists($key)
    {
        return array_key_exists($key, $this->themes);
    }

    public function offsetGet($key)
    {
        return $this->themes[$key];
    }

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

    public function offsetUnset($key)
    {
        unset($this->themes[$key]);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->themes);
    }

}
