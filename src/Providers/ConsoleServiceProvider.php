<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Providers;

use Laradic\Console\AggregateConsoleProvider;

/**
 * This is the ConsoleServiceProvider class.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class ConsoleServiceProvider extends AggregateConsoleProvider {

    protected $namespace = 'Laradic\Themes\Console';

    protected $commands = [
        'ThemePublish' => 'commands.themes.publish',
        'ThemePublishers' => 'commands.themes.publishers',
        'ThemeMake' => 'commands.themes.make',
        'ThemeInit' => 'commands.themes.init'
    ];

}
