<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Assets;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\NamespacedItemResolver;
use Illuminate\View\Factory as ViewFactory;
use Laradic\Themes\Contracts\AssetFactory as AssetFactoryContract;
use Laradic\Themes\Contracts\ThemeFactory;
use Laradic\Themes\Contracts\ThemeViewFinder;
use Stringy\Stringy;

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

    /**
     * @var string
     */
    protected $cacheDir;

    /** @var string */
    protected $assetClass;

    /**
     * @var AssetGroup[]
     */
    protected $assetGroups = [ ];


    /**
     * @param ThemeFactory                               $themes
     * @param \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     * @param \Illuminate\Filesystem\Filesystem          $files
     * @param \Illuminate\View\Factory                   $view
     * @internal param \Illuminate\Contracts\Foundation\Application $app
     * @internal param \Laradic\Themes\Contracts\ThemeViewFinder $viewFinder
     * @internal param \Illuminate\Contracts\View\Factory $view
     * @internal param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(ThemeFactory $themes)
    {
        /** @var \Laradic\Themes\ThemeFactory $themes */

        $this->themes = $themes;

        $this->assetClass = config('laradic/themes::assetClass');
        $this->cacheDir   = config('laradic/themes::paths.cache');

        $themes->setAssets($this);
    }

    /** @return Asset */
    public function make($assetPath)
    {
        return new $this->assetClass($this, $assetPath);
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
    public function style($assetPath = "", array $attributes = [ ], $secure = false)
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
    public function script($assetPath = "", array $attributes = [ ], $secure = false)
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
        if ( isset($this->assetGroups[ $name ]) )
        {
            return $this->assetGroups[ $name ];
        }
        else
        {
            $this->assetGroups[ $name ] = new AssetGroup($this, $this->files, $name);

            return $this->assetGroups[ $name ];
        }
    }



    //
    /* GETTERS & SETTERS */
    //

    /**
     * getThemes
     *
     * @return \Laradic\Themes\Contracts\ThemeFactory|\Laradic\Themes\ThemeFactory
     */
    public function getThemes()
    {
        return $this->themes;
    }

    public function getAssetClass()
    {
        return $this->assetClass;
    }

    /**
     * get cacheDir value
     *
     * @return mixed
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * get assetGroups value
     *
     * @return AssetGroup[]
     */
    public function getAssetGroups()
    {
        return $this->assetGroups;
    }

}
