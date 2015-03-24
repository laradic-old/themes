<?php
 /**
 * Part of the Radic packages.
 */
namespace Laradic\Themes;

use Illuminate\Filesystem\Filesystem;

/**
 * Class Publisher
 *
 * @package     Laradic\Themes
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class Publisher
{
    protected $type;

    protected $namespace;

    protected $package;

    /** @var \Laradic\Themes\Theme */
    protected $theme;

    protected $sourcePath;

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $files;

    /**
 * Instanciates the class
 */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function publish()
    {
        $destination = $this->theme->getCascadedPath(
            $this->type . 's',
            $this->type === 'namespace' ? $this->namespace : $this->package
        );

        if(!$this->files->exists($destination))
        {
            $this->files->makeDirectory($destination, 0755, true);
        }

        $this->files->copyDirectory($this->sourcePath, $destination);
    }

    public static function create(Filesystem $files)
    {
        return new static($files);
    }

    public function asNamespace($namespace)
    {
        $this->type = 'namespace';
        $this->namespace = $namespace;
        return $this;
    }

    public function asPackage($package)
    {
        $this->type = 'package';
        $this->package = $package;
        return $this;
    }

    public function toTheme(Theme $theme)
    {
        $this->theme = $theme;
        return $this;
    }

    public function from($sourcePath)
    {
        $this->sourcePath = $sourcePath;
        return $this;
    }

}
