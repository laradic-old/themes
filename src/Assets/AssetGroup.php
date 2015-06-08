<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Assets;


use Closure;
use File;
use HTML;
use InvalidArgumentException;
use Laradic\Support\Sorter;
use Laradic\Support\String;
use MatthiasMullie\Minify;

/**
 * This is the AssetGroup class.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class AssetGroup
{

    protected $name;

    /**
     * @var \Laradic\Themes\Assets\AssetFactory
     */
    protected $factory;

    protected $scripts = [ ];

    protected $styles = [ ];

    protected $sorter;

    protected $debug;

    protected $filters = [ ];

    public function __construct(AssetFactory $factory, $name)
    {
        $this->name       = $name;
        $this->factory    = $factory;
        $this->debug      = config('laradic/themes::debug');
        $this->collection = new AssetCollection();
    }

    public function add($handle, $path = null, array $dependencies = [ ])
    {

        if ( $handle instanceof Asset )
        {
            $asset  = $handle;
            $handle = $asset->getHandle();
        }
        elseif ( ! is_null($path) )
        {
            $asset = $this->factory->make($handle, $path);

        }
        else
        {
            throw new \InvalidArgumentException("Parameter path was null: $path");
        }




        $type = $this->resolveType($asset->getExt());

        if(count($dependencies) > 0 and false == true)
        {
            $_deps = [ ];
            foreach ( $dependencies as $dep )
            {
                if ( isset($this->{"{$type}s"}[ $dep ]) )
                {
                    $_deps [ ] = $this->{"{$type}s"}[ $dep ][ 'asset' ];
                }
            }
            $asset->setDependencies($dependencies);
        }
        $asset->setDependencies($dependencies);
        $this->{"{$type}s"}[ $handle ] = [
            'handle'  => $handle,
            'asset'   => $asset,
            'type'    => $type,
            'depends' => $dependencies
        ];

        #$this->collection->add($asset);

        return $this;
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


    public function addFilter($extension, $callback)
    {
        if ( is_string($callback) )
        {
            $callback = function () use ($callback)
            {
                return new $callback;
            };
        }
        elseif ( ! $callback instanceof Closure )
        {
            throw new InvalidArgumentException('Callback is not a closure or reference string.');
        }
        $this->filters[ $extension ][ ] = $callback;
        return $this;
    }

    public function getFilters($extension)
    {
        $filters = array();
        if ( ! isset($this->filters[ $extension ]) )
        {
            return array();
        }
        foreach ( $this->filters[ $extension ] as $cb )
        {
            $filters[ ] = new $cb();
        }

        return $filters;
    }

    public function render($type, $combine = true)
    {
        $assets = $this->getSorted($type);
        $assets = $combine ? new AssetCollection($assets) : $assets;

        foreach ( ($combine ? $assets->all() : $assets) as $asset )
        {
            if ( ! $asset instanceof Asset )
            {
                continue;
            }
            foreach ( $this->getFilters($asset->getExt()) as $filter )
            {
                $asset->ensureFilter($filter);
            }
        }
        if ( $combine )
        {
            $assets = array( $assets );
        }

        $urls = [];
        $cachePath = $this->factory->getCachePath();
        $cachedAssets = \File::files($this->factory->getCachePath());
        $theme = $this->factory->getThemes()->getActive();
        $renderExt = $type === 'styles' ? 'css' : 'js';

        foreach ($assets as $asset)
        {

            $filename = String::replace($theme->getSlug(), '/', '.') . '.' . $asset->getHandle() . '.' . md5($asset->getCacheKey()) . '.' . $renderExt;
            $path = $cachePath.'/'.$filename;
            if (! File::exists($path))
            {
                $asset->load();
                preg_match('/.*_/', $filename, $pattern);
                $pattern = head($pattern);
                foreach ($cachedAssets as $cachedAsset)
                {
                    if (strpos($cachedAsset, $pattern) !== false)
                    {
                        //File::delete($cachedAsset);
                    }
                }
                File::put($path, $asset->dump());
            }
            $urls[] = String::removeLeft($path, public_path());
        }

        $htmlTags = '';
        foreach($urls as $url)
        {
            $htmlTags .= $type === 'scripts' ? HTML::script($url) : HTML::style($url);
        }
        return $htmlTags;
    }

    public function get($type, $handle)
    {
        return $this->{"{$type}s"}[ $handle ];
    }

    /**
     * getSorted
     *
     * @param string $type 'scripts' or 'styles'
     * @return Asset[]
     */
    public function getSorted($type)
    {
        $sorter = new Sorter();
        foreach ( $this->{"{$type}"} as $handle => $assetData )
        {
            $sorter->addItem($assetData['asset']);
        }

        $assets = [ ];
        foreach ( $sorter->sort() as $handle )
        {
            $assets[ ] = $this->get(String::singular($type), $handle)[ 'asset' ];
        }

        return $assets;
    }

    /**
     * getAssets
     *
     * @param string $type 'scripts' or 'styles'
     * @return mixed
     */
    public function getAssets($type)
    {
        return $this->{"{$type}"};
    }

    /**
     * Get the value of name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
