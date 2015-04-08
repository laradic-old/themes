<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Themes\Contracts;

use Closure;

/**
 * Widgets contract
 *
 * @package     Laradic\Themes\Contracts
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
interface Widgets
{

    /**
     * Registers a widget
     *
     * @param          $name
     * @param callable $callback
     */
    public function create($name, Closure $callback);

    /**
     * Registers the @widget directive
     */
    public function registerDirectives();

    /**
     * Renders a widget
     *
     * @param string $name the widget name
     * @param mixed  $param,...
     * @return string The rendered widget
     */
    public function render($name, $param);
}
