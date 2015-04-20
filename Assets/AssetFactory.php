<?php namespace Laradic\Themes\Assets;

/**
 * Part of the Radic packges.
 * Licensed under the MIT license.
 *
 * @package        dev7
 * @author         Robin Radic
 * @license        MIT
 * @copyright  (c) 2011-2015, Robin Radic
 * @link           http://radic.mit-license.org
 */
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
 * Asset
 *
 * @package Laradic\Themes\Assets${NAME}
 */
class AssetFactory implements AssetFactoryContract
{

    /** @var \Laradic\Themes\ThemeFactory */
    protected $themes;

    /** @var Container */
    protected $container;

    protected $assetClass;

    protected $assetManager;

    protected $assetGroups = [];

    /** @var UrlGenerator */
    protected $urlGenerator;

    /**
     * @param Container    $container
     * @param ThemeFactory $themes
     */
    public function __construct(Application $app, ThemeFactory $themes, UrlGenerator $urlGenerator)
    {
        $this->app          = $app;
        $this->themes       = $themes;
        $this->assetClass   = $app->config->get('radic_themes.assetClass');
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

    public function url($assetPath = "")
    {
        return $this->make($assetPath)->url();
    }

    public function uri($assetPath = "")
    {
        return $this->make($assetPath)->uri();
    }

    public function style($assetPath = "", $attributes = [], $secure = false)
    {
        return $this->make($assetPath)->style($attributes, $secure);
    }

    public function script($assetPath = "", $attributes = [], $secure = false)
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

    public function relativePath($path)
    {
        $path = Stringy::create($path)->removeLeft(public_path());
        if ( $path->endsWith('.') )
        {
            $path = $path->removeRight('.');
        }

        return $path->__toString();
    }

    public function toUrl($path)
    {
        if ( Stringy::create($path)->startsWith(public_path()) )
        {
            $path = $this->relativePath($path);
        }

        return $this->urlGenerator->to($path);
    }

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
                return $this->toUrl($this->themes->getActive()->getCascadedPath('namespaces', $section, 'assets')); #getNamespaceAssetsPath($section));
            }

            return $this->toUrl($this->themes->getActive()->getCascadedPath('packages', $section, 'assets')); #getPackageAssetsPath($section));

        }

        if ( isset($section) )
        {
            if ( array_key_exists($section, View::getFinder()->getHints()) )
            {
                $paths = $this->themes->getCascadedPaths('namespaces', $section, 'assets'); # getCascadedNamespaceAssetPaths
            }
            else
            {
                $paths = $this->themes->getCascadedPaths('packages', $section, 'assets'); # getCascadedPackageAssetPaths
            }
        }
        else
        {
            $paths = $this->themes->getCascadedPaths(null, null, 'assets'); #getCascadedAssetPaths();
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
