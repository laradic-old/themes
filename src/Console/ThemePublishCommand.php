<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Console;

use Laradic\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * This is the ThemePublishCommand class.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class ThemePublishCommand extends Command
{

    protected $name = 'themes:publish';

    protected $description = 'Publish ';

    public function fire()
    {
        $publisher = $this->argument('publisher');
        $theme = $this->option('theme');

        app('themes')->publish($publisher, $theme);
        $this->info('Published ' . (!is_null($publisher) ? $publisher : 'all') . (!is_null($theme) ? " to theme $theme" : null));
    }

    public function getArguments()
    {
        return [
            ['publisher', InputArgument::OPTIONAL, 'The namespace or package to publish. If not provided, everything will be published. Check themes:publishers for available options']
        ];
    }

    public function getOptions()
    {
        return [
            ['theme', 't', InputOption::VALUE_OPTIONAL, 'The theme you want to publish to', null]
        ];
    }
}
