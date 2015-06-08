<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Assets;

use Assetic\Asset\FileAsset;
use Assetic\Filter\FilterInterface;
use Assetic\Filter\HashableInterface;
use Laradic\Support\Contracts\Dependable;

/**
 * This is the Asset.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class Asset extends FileAsset implements Dependable
{
    protected $handle;
    protected $dependencies;
    protected $ext;
    /** Instantiates the class
     *
     * @param string $path
     * @param array  $name
     * @param array  $dependencies
     * @internal param array $filters
     * @internal param null $sourceRoot
     * @internal param null $sourcePath
     * @internal param array $vars
     */
    public function __construct($handle, $path, array $dependencies = [])
    {

        parent::__construct($path);
        $this->handle = $handle;
        $this->dependencies = $dependencies;
    }

    /**
     * Get the value of ext
     *
     * @return mixed
     */
    public function getExt()
    {
        return pathinfo($this->getSourcePath(), PATHINFO_EXTENSION);
    }


    /**
     * get dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    public function setDependencies(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * get item key/identifier
     *
     * @return string|mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    public function getCacheKey()
    {
        $key = $this->handle . $this->getSourcePath();
        foreach($this->getFilters() as $filter)
        {
            $key .= $filter instanceof HashableInterface ? $filter->hash() : serialize($filter);
        }
        return $key;
    }



    public function load(FilterInterface $additionalFilter = null)
    {
        parent::load($additionalFilter);
        return $this;
    }


    public function ensureFilter(FilterInterface $filter)
    {
        parent::ensureFilter($filter);
        return $this;
    }
}
