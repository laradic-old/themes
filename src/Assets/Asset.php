<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Assets;

use Laradic\Themes\Contracts\AssetFactory as AssetFactoryContract;

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
    protected $assets;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var
     */
    protected $ext;

    /**
     * @param \Laradic\Themes\Contracts\AssetFactory $assets
     * @param                                        $assetPath
     */
    public function __construct(AssetFactoryContract $assets, $assetPath)
    {
        $this->assets    = $assets;
        $this->assetPath = $assetPath;
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
        return $this->assets->toUrl($this->assetPath);
    }

    /**
     * uri
     *
     * @return string
     */
    public function uri()
    {
        return $this->assets->relativePath($this->assetPath);
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
        return \HTML::script($this->url(), $attr, $secure);
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
        return \HTML::style($this->url(), $attr, $secure);
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
}
