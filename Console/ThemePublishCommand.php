<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Themes\Console;

use Laradic\Support\AbstractConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class ThemePackageCommand
 *
 * @package     Laradic\Themes\Console
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class ThemePublishCommand extends AbstractConsoleCommand
{

    protected $name = 'themes:publish';

    protected $description = 'Publish ';

    public function fire()
    {
        $publisher = $this->argument('publisher');
    #    $this->dump($publisher);
        app('themes')->publish($publisher);
        $this->info("Published " . (!is_null($publisher) ? $publisher : 'all'));
    }

    public function getArguments()
    {
        return [
            ['publisher', InputArgument::OPTIONAL, 'The namespace or package to publish. If not provided, everything will be published. Check themes:publishers for available options']
        ];
    }
}
