<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Contracts;

/**
 * Interface ThemeViewFinder
 *
 * @package Laradic\Themes\Contracts
 */
interface ThemeViewFinder
{

    public function getHints();

    /**
     * Find the key
     * @param $key
     * @return mixed
     */
    public function find($key);

    /** @return ThemeFactory */
    public function getThemes();

    /**
     * @param ThemeFactory $themes
     * @return $this
     */
    public function setThemes(ThemeFactory $themes);
}
