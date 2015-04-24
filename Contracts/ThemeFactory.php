<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Contracts;

/**
 * Interface ThemeFactory
 *
 * @package Laradic\Themes\Contracts
 */
interface ThemeFactory {

    /** @return ThemeViewFinder */
    public function getFinder();

}
