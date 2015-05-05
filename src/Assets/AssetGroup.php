<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Assets;

use File;
use Laradic\Support\Sorter;
use Laradic\Support\String;
use Laradic\Themes\Contracts\AssetFactory as AssetFactoryContract;
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

    public function __construct(AssetFactoryContract $factory, $name)
    {
        $this->name    = $name;
        $this->factory = $factory;
        $this->debug   = config('laradic/themes::debug');
    }

    public function add($name, $cascadedPath, $depends = [ ])
    {
        $asset = $this->factory->make($cascadedPath);
        $type  = $asset->getType();

        $this->{"{$type}s"}[ $name ] = [
            'name'    => $name,
            'asset'   => $asset,
            'type'    => $type,
            'depends' => $depends
        ];

        return $this;
    }

    public function get($type, $name)
    {
        return $this->{"{$type}s"}[ $name ];
    }

    public function render($type, $debug = null)
    {
        if ( is_null($debug) )
        {
            $debug = $this->debug;
        }

        return $debug ? $this->renderPlain($type) : $this->renderCombinedMinified($type);
    }

    protected function renderPlain($type)
    {
        $assets = [ ];
        foreach ( $this->getSorted($type) as $asset )
        {
            $assets[ ] = $type === 'styles' ? '<link href="' . $asset->url() . '" type="text/css" rel="stylesheet">' : '<script type="text/javascript" src="' . $asset->url() . '"></script>';
        }

        return implode("\n", $assets);
    }

    protected function renderCombinedMinified($type)
    {
        $minifier = null;
        if ( $type === 'scripts' )
        {
            $minifier = new Minify\JS();
        }
        elseif ( $type === 'styles' )
        {
            $minifier = new Minify\CSS();
        }
        else
        {
            throw new \Exception('Invalid asset group render type specified');
        }

        $lastModified = '';
        foreach ( $this->getSorted($type) as $asset )
        {
            $lastModified .= (string)File::lastModified($asset->path());
            $minifier->add($asset->path());
        }

        $fileName = md5($lastModified);
        $webPath  = $this->factory->getCacheDir() . '/' . $fileName . ($type === 'scripts' ? '.js' : '.css');
        $filePath = public_path($webPath);
        if ( ! File::exists($filePath) )
        {
            File::put($filePath, $minifier->minify());
        }

        return $type === 'scripts' ? \HTML::script($webPath) : \HTML::style($webPath);
    }

    /**
     * getSorted
     *
     * @param $type
     * @return Asset[]
     */
    protected function getSorted($type)
    {
        $sorter = new Sorter();
        foreach ( $this->{"{$type}"} as $name => $asset )
        {
            $sorter->addItem($name, $asset[ 'depends' ]);
        }

        $assets = [ ];
        foreach ( $sorter->sort() as $name )
        {
            $assets[ ] = $this->get(String::singular($type), $name)[ 'asset' ];
        }

        return $assets;
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
