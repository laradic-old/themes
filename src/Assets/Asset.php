<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Assets;

use Illuminate\Support\NamespacedItemResolver;
use Laradic\Support\String;
use Laradic\Themes\Contracts\AssetFactory as AssetFactoryContract;
use File;
use HTML;
use Themes;
use URL;
use View;

/**
 * This is the Asset class.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class Asset
{

    /** @var string */
    protected $assetPath;

    /** @var \Laradic\Themes\Assets\AssetFactory */
    protected $factory;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var
     */
    protected $ext;

    /**
     * @param \Laradic\Themes\Contracts\AssetFactory $factory
     * @param                                        $assetPath
     */
    public function __construct(AssetFactoryContract $factory, $assetPath)
    {
        $this->factory   = $factory;
        $this->assetPath = $this->getPath($assetPath);
        $this->ext       = $this->resolveExtension($assetPath);
        $this->type      = $this->resolveType($this->ext);
    }

    /**
     * path
     *
     * @return string
     */
    public function path()
    {
        return $this->assetPath;
    }

    /**
     * url
     *
     * @return string
     */
    public function url()
    {
        return $this->toUrl($this->assetPath);
    }

    /**
     * uri
     *
     * @return string
     */
    public function uri()
    {
        return $this->relativePath($this->assetPath);
    }

    /**
     * script
     *
     * @param array $attr
     * @param bool  $secure
     * @return string
     */
    public function script($attr = [ ], $secure = false)
    {
        return HTML::script($this->url(), $attr, $secure);
    }

    /**
     * style
     *
     * @param array $attr
     * @param bool  $secure
     * @return string
     */
    public function style($attr = [ ], $secure = false)
    {
        return HTML::style($this->url(), $attr, $secure);
    }

    /**
     * Get the value of type
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the value of ext
     *
     * @return mixed
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * resolveExtension
     *
     * @param $path
     * @return mixed
     */
    protected function resolveExtension($path)
    {
        $arr = preg_split('/\./', $path);

        return end($arr);
    }

    /**
     * resolveType
     *
     * @param $ext
     * @return string
     */
    protected function resolveType($ext)
    {
        $style  = [ 'css', 'scss', 'sass', 'less' ];
        $script = [ 'js', 'ts', 'cs' ];

        if ( in_array($ext, $style) )
        {
            return 'style';
        }
        if ( in_array($ext, $script) )
        {
            return 'script';
        }

        return 'other';
    }


    /**
     * relativePath
     *
     * @param $path
     * @return string
     */
    public function relativePath($path)
    {
        $path = String::create($path)->removeLeft(public_path());
        if ( $path->endsWith('.') )
        {
            $path = $path->removeRight('.');
        }

        return (string)$path;
    }

    /**
     * toUrl
     *
     * @param $path
     * @return string
     */
    public function toUrl($path)
    {
        if ( String::create($path)->startsWith(public_path()) )
        {
            $path = $this->relativePath($path);
        }

        return URL::to($path);
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
            return $this->toUrl(Themes::getActive()->getPath('assets'));
        }

        if ( $relativePath === null or strlen($relativePath) === 0 )
        {
            if ( array_key_exists($section, View::getFinder()->getHints()) )
            {
                return $this->toUrl(Themes::getActive()->getCascadedPath('namespaces', $section, 'assets'));
            }

            return $this->toUrl(Themes::getActive()->getCascadedPath('packages', $section, 'assets'));
        }

        if ( isset($section) )
        {
            if ( array_key_exists($section, View::getFinder()->getHints()) )
            {
                $paths = Themes::getCascadedPaths('namespaces', $section, 'assets');
            }
            else
            {
                $paths = Themes::getCascadedPaths('packages', $section, 'assets');
            }
        }
        else
        {
            $paths = Themes::getCascadedPaths(null, null, 'assets');
        }

        foreach ( $paths as $path )
        {
            $file = rtrim($path, '/') . '/' . $relativePath . '.' . $extension;

            if ( File::exists($file) )
            {
                return $file;
            }
        }

        return $file;
    }
}
