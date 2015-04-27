<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Assets;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\NamespacedItemResolver;
use Laradic\Themes\Contracts\AssetFactory as AssetFactoryContract;
use Laradic\Themes\Contracts\ThemeFactory;
use Stringy\Stringy;
use Assetic\AssetManager;
use View;

/**
 * This is the AssetFactory class.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class AssetFactory implements AssetFactoryContract
{

    /** @var \Laradic\Themes\ThemeFactory */
    protected $themes;

    /** @var Container */
    protected $container;

    /** @var string */
    protected $assetClass;

    /**
     * The assetic asset manager instance
     * @var \Assetic\AssetManager
     */
    protected $assetManager;

    /**
     * @var AssetGroup[]
     */
    protected $assetGroups = [];

    /** @var UrlGenerator */
    protected $urlGenerator;

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param ThemeFactory                                 $themes
     * @param \Illuminate\Contracts\Routing\UrlGenerator   $urlGenerator
     * @internal param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Application $app, ThemeFactory $themes, UrlGenerator $urlGenerator)
    {
        $this->app          = $app;
        $this->themes       = $themes;
        $this->assetClass   = $app->make('config')->get('radic_themes.assetClass');
        $this->urlGenerator = $urlGenerator;
        $this->assetManager = new AssetManager();

        $themes->setAssets($this);
    }

    function ___call($name, $arguments)
    {
        if(in_array($name, ['url', 'uri', 'style', 'script']))
        {
            #call_user_func_array([$this->make($name),
        }
    }


    /** @return Asset */
    public function make($assetPath)
    {
        return new $this->assetClass($this, $this->getPath($assetPath));
    }

    /**
     * url
     *
     * @param string $assetPath
     * @return string
     */
    public function url($assetPath = '')
    {
        return $this->make($assetPath)->url();
    }

    /**
     * uri
     *
     * @param string $assetPath
     * @return string
     */
    public function uri($assetPath = '')
    {
        return $this->make($assetPath)->uri();
    }

    /**
     * style
     *
     * @param string $assetPath
     * @param array  $attributes
     * @param bool   $secure
     * @return string
     */
    public function style($assetPath = "", array $attributes = [], $secure = false)
    {
        return $this->make($assetPath)->style($attributes, $secure);
    }

    /**
     * script
     *
     * @param string $assetPath
     * @param array  $attributes
     * @param bool   $secure
     * @return string
     */
    public function script($assetPath = "", array $attributes = [], $secure = false)
    {
        return $this->make($assetPath)->script($attributes, $secure);
    }

    /**
     * group
     *
     * @param          $name
     * @param callable $cb
     * @return AssetGroup
     */
    public function group($name)
    {
        if(isset($this->assetGroups[$name]))
        {
            return $this->assetGroups[$name];
        }
        else
        {
            $this->assetGroups[$name] = new AssetGroup($this, $name);
            return $this->assetGroups[$name];
        }
    }

    /**
     * relativePath
     *
     * @param $path
     * @return string
     */
    public function relativePath($path)
    {
        $path = Stringy::create($path)->removeLeft(public_path());
        if ( $path->endsWith('.') )
        {
            $path = $path->removeRight('.');
        }

        return $path->__toString();
    }

    /**
     * toUrl
     *
     * @param $path
     * @return string
     */
    public function toUrl($path)
    {
        if ( Stringy::create($path)->startsWith(public_path()) )
        {
            $path = $this->relativePath($path);
        }

        return $this->urlGenerator->to($path);
    }

    /**
     * getPath
     *
     * @param null $key
     * @return string
     */
    public function getPath($key = null)
    {
        list($section, $relativePath, $extension) = with(new NamespacedItemResolver)->parseKey($key);

        if ( $key === null )
        {
            return $this->toUrl($this->themes->getActive()->getPath('assets'));
        }

        if ( $relativePath === null or strlen($relativePath) === 0 )
        {
            if ( array_key_exists($section, View::getFinder()->getHints()) )
            {
                return $this->toUrl($this->themes->getActive()->getCascadedPath('namespaces', $section, 'assets'));
            }

            return $this->toUrl($this->themes->getActive()->getCascadedPath('packages', $section, 'assets'));

        }

        if ( isset($section) )
        {
            if ( array_key_exists($section, View::getFinder()->getHints()) )
            {
                $paths = $this->themes->getCascadedPaths('namespaces', $section, 'assets');
            }
            else
            {
                $paths = $this->themes->getCascadedPaths('packages', $section, 'assets');
            }
        }
        else
        {
            $paths = $this->themes->getCascadedPaths(null, null, 'assets');
        }

        foreach ($paths as $path)
        {
            $file = rtrim($path, '/') . '/' . $relativePath . '.' . $extension;

            if ( $this->themes->getFiles()->exists($file) )
            {
                return $file;
            }
        }

        return $file;
    }

    /**
     * getThemes
     *
     * @return \Laradic\Themes\Contracts\ThemeFactory|\Laradic\Themes\ThemeFactory
     */
    public function getThemes()
    {
        return $this->themes;
    }

    public function setThemes($themes)
    {
        $this->themes = $themes;

        return $this;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    public function getAssetClass()
    {
        return $this->assetClass;
    }

    public function setAssetClass($assetClass)
    {
        $this->assetClass = $assetClass;

        return $this;
    }

    public function getUrlGenerator()
    {
        return $this->urlGenerator;
    }

    public function setUrlGenerator($urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;

        return $this;
    }


}
