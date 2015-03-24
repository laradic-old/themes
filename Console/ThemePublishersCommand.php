<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Themes\Console;

use Laradic\Support\AbstractConsoleCommand;

/**
 * Class ThemePackageCommand
 *
 * @package     Laradic\Themes\Console
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class ThemePublishersCommand extends AbstractConsoleCommand
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
