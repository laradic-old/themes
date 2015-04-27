<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Traits;

/**
 * This is the ThemeProviderTrait trait.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
trait ThemeProviderTrait {

    /**
     * addPackagePublisher
     *
     * @param $package
     * @param $path
     */
    protected function addPackagePublisher($package, $path)
    {
        app('themes')->addPackagePublisher($package, $path);
    }

    /**
     * addNamespacePublisher
     *
     * @param $namespace
     * @param $path
     */
    protected function addNamespacePublisher($namespace, $path)
    {
        app('themes')->addNamespacePublisher($namespace, $path);
    }


}
