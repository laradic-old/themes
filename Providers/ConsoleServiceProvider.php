<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Providers;

use Laradic\Support\AbstractConsoleProvider;

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
class ConsoleServiceProvider extends AbstractConsoleProvider {

    protected $namespace = 'Laradic\Themes\Console';

    protected $commands = [
        'ThemePublish' => 'command.themes.publish',
        'ThemePublishers' => 'command.themes.publishers'
    ];

}
