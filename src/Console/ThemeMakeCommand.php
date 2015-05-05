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
        $this->files = app('files');
        $config = app('config')->get('laradic/themes::config');
        $path = Path::join(head($config['paths']['themes']), $slug);

        if($this->files->exists($path))
        {
            return $this->error('theme already exists');
        }

        $this->mkdir($path);
        $dirs = [
            $config['paths']['assets'], $config['paths']['namespaces'], $config['paths']['packages'], $config['paths']['views']
        ];

        foreach($dirs as $dir)
        {
            $this->mkdir(Path::join($path, $dir));
        }

        $this->files->copy(__DIR__ . '/../../resources/theme.php', $path . '/theme.php');
        $this->info('');
    }

    protected function mkdir($path)
    {
        $this->files->makeDirectory($path, 0755, true);
    }

    public function getArguments()
    {
        return [
            ['slug', InputArgument::REQUIRED, 'The slug of the theme']
        ];
    }
}
