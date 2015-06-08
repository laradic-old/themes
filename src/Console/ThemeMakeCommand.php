<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Console;

use Laradic\Console\Command;
use Laradic\Console\Traits\SlugPackageTrait;
use Laradic\Support\Path;
use Laradic\Themes\ThemeGenerator;
use Symfony\Component\Console\Input\InputArgument;

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
class ThemeMakeCommand extends Command
{

    use SlugPackageTrait;

    protected $name = 'themes:make';

    protected $description = 'Publish ';

    /**
     * @var \Laradic\Support\Filesystem
     */
    protected $files;

    public function fire()
    {

        if ( ! $this->validateSlug($slug = $this->argument('slug')) )
        {
            return $this->error('Invalid slug');
        }

        $gen = new ThemeGenerator(app('blade.compiler'));
        $success = $gen->generateTheme($slug, $slug . ' Theme');

        if(!$success)
        {
            return $this->error('theme already exists');
        }

        $this->info('Successfully created theme');
    }

    public function getArguments()
    {
        return [
            ['slug', InputArgument::REQUIRED, 'The slug of the theme']
        ];
    }
}
