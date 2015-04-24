<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Contracts;

use Closure;

/**
 * Interface Widgets
 *
 * @package Laradic\Themes\Contracts
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
