<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Assets;

use Laradic\Support\Sorter;
use Laradic\Support\String;
use Laradic\Themes\Contracts\AssetFactory as AssetFactoryContract;

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

    protected $scripts = [];

    protected $styles = [];

    protected $sorter;

    public function __construct(AssetFactoryContract $factory, $name)
    {
        $this->name    = $name;
        $this->factory = $factory;
        $this->sorter  = new Sorter();
    }

    public function add($name, $cascadedPath, $depends = [])
    {
        $asset = $this->factory->make($cascadedPath);
        $type  = $asset->getType();

        $this->{"{$type}s"}[$name] = [
            'name'    => $name,
            'asset'   => $asset,
            'type'    => $type,
            'depends' => $depends
        ];

        return $this;
    }

    public function get($type, $name)
    {
        return $this->{"{$type}s"}[$name];
    }

    public function render($type)
    {
        # $this->scripts, $this->styles
        foreach ($this->{"{$type}"} as $name => $asset)
        {
            $this->sorter->addItem($name, $asset['depends']);
        }

        $assets = [];
        foreach ($this->sorter->sort() as $name)
        {
            /** @var Asset $asset */
            $asset    = $this->get(String::singular($type), $name)['asset'];
            $assets[] = $type == 'styles' ? '<link href="' . $asset->url() . '" type="text/css" rel="stylesheet">' : '<script type="text/javascript" src="' . $asset->url() . '"></script>';
        }

        return implode("\n",$assets);
    }

    public function requires($names)
    {
        # group dependency
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
