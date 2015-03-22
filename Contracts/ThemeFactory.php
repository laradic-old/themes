<?php namespace Laradic\Themes\Contracts;

/**
 * Part of the Radic packges.
 * Licensed under the MIT license.
 *
 * @package    Laradic\Themes
 * @author     Robin Radic
 * @license    MIT
 * @copyright  (c) 2011-2015, Robin Radic
 * @link       http://radic.mit-license.org
 * @see        \Laradic\Themes\ThemeFactory
 * @uses       \Laradic\Themes\ThemeFactory
 * {@inheritdoc}
 */
interface ThemeFactory {

    /** @return ThemeViewFinder */
    public function getFinder();

}
