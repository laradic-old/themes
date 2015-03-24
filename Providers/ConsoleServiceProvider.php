<?php namespace Laradic\Themes\Providers;

use Laradic\Support\AbstractConsoleProvider;

class ConsoleServiceProvider extends AbstractConsoleProvider {

    protected $namespace = 'Laradic\Themes\Console';

    protected $commands = [
        'ThemePublish' => 'command.themes.publish',
        'ThemePublishers' => 'command.themes.publishers'
    ];

}
