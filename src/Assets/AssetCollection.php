<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Assets;

use Assetic\Filter\HashableInterface;

/**
 * This is the AssetCollection.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class AssetCollection extends \Assetic\Asset\AssetCollection
{
    /** Instantiates the class */
    public function getCacheKey()
    {
        $key = '';
        foreach($this->all() as $asset)
        {
            if(!$asset instanceof Asset) continue;
            $key .= $asset->getCacheKey();
        }
        return 'col_'.$key;
    }

    public function getHandle()
    {
        return 'col_';
    }
}
