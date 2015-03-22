<?php namespace Laradic\Themes\Contracts;

/**
 * Part of the Radic packges.
 * Licensed under the MIT license.
 *
 * @package    dev9
 * @author     Robin Radic
 * @license    MIT
 * @copyright  (c) 2011-2015, Robin Radic
 * @link       http://radic.mit-license.org
 */

interface AssetFactory {
    /** @return \Illuminate\Routing\UrlGenerator */
    public function getUrlGenerator();
}
