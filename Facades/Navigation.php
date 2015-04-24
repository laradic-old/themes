<?php
/**
 * Part of the Laradic packages.
 */
namespace Laradic\Themes\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Navigation
 *
 * @package Themes
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class Navigation extends Facade
{
    /**
     * {@inheritDoc}
     */
    public static function getFacadeAccessor()
    {
        return 'navigation';
    }
}
