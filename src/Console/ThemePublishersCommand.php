<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Console;

use Laradic\Console\Command;

/**
 * This is the ThemePublishersCommand class.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class ThemePublishersCommand extends Command
{

    protected $name = 'themes:publishers';

    protected $description = 'List all available publishers.';

    public function fire()
    {
        $publishers = array_keys(app('themes')->getPublishers());
        $this->comment('Available publishers:');
        foreach($publishers as $publisher)
        {
            $this->line($publisher);
        }
    }
}
