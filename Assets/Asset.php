<?php
/**
 * Part of the Laradic packages.
 * MIT License and copyright information bundled with this package in the LICENSE file.
 */
namespace Laradic\Themes\Assets;

use Laradic\Themes\Contracts\AssetFactory as AssetFactoryContract;

/**
 * Class Asset
 *
 * @package     Laradic\Themes\Assets
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class Asset
{

    /** @var string */
    protected $assetPath;

    /** @var \Laradic\Themes\Assets\AssetFactory */
    protected $assets;

    protected $type;

    protected $ext;

    public function __construct(AssetFactoryContract $assets, $assetPath)
    {
        $this->assets    = $assets;
        $this->assetPath = $assetPath;
        $this->ext       = $this->resolveExtension($assetPath);
        $this->type      = $this->resolveType($this->ext);
    }

    public function path()
    {
        return $this->assetPath;
    }

    public function url()
    {
        return $this->assets->toUrl($this->assetPath);
    }

    public function uri()
    {
        return $this->assets->relativePath($this->assetPath);
    }

    public function script($attr = [], $secure = false)
    {
        return \HTML::script($this->url(), $attr, $secure);
    }

    public function style($attr = [], $secure = false)
    {
        return \HTML::style($this->url(), $attr, $secure);
    }

    protected function resolveExtension($path)
    {
        $arr = preg_split('/\./', $path);

        return end($arr);
    }

    protected function resolveType($ext)
    {
        $style  = ['css', 'scss', 'sass', 'less'];
        $script = ['js', 'ts', 'cs'];

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
