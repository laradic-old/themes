<?php namespace Laradic\Themes\Traits;
 /**
 * Part of the Radic packages.
 */

trait ThemeProviderTrait {

    protected function addPackagePublisher($package, $path)
    {
        app('themes')->addPackagePublisher($package, $path);
    }

    protected function addNamespacePublisher($namespace, $path)
    {
        app('themes')->addNamespacePublisher($namespace, $path);
    }


}
