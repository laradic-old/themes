<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes;

use Laradic\Support\Path;
use Laradic\Support\StubGenerator;
use Symfony\Component\VarDumper\VarDumper;

/**
 * This is the ThemeGenerator.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class ThemeGenerator extends StubGenerator
{

    public $themeStubPath = __DIR__ . '/../resources/stubs';

    public $themeDirPath;

    public function generateTheme($slug, $name, $parent = null, $viewFile = null)
    {
        $path = Path::join($this->getThemeDirPath(), $slug);

        if ( $this->files->exists($path) )
        {
            return false;
        }

        #VarDumper::dump($path);
        $this->generateDirStruct($path);
        $themeStub        = $this->files->get(realpath(Path::join($this->themeStubPath, 'theme.php.stub')));
        $themeFileContent = "<?php \n" . $this->render($themeStub, compact('slug', 'name', 'parent'));
        $this->files->put(Path::join($path, 'theme.php'), $themeFileContent);
        if ( ! is_null($viewFile) )
        {
            $from = Path::join($this->themeStubPath, $viewFile);
            $to = Path::join($path, config('laradic.themes.paths.views'),$viewFile);
            #VarDumper::dump(compact('from', 'to'));
            $this->files->copy($from, $to);

        }

        return true;
    }

    protected function getThemeDirPath()
    {
        return isset($this->themeDirPath) ? $this->themeDirPath : head(config('laradic.themes.paths.themes'));
    }

    protected function generateDirStruct($path)
    {
        $this->files->makeDirectory($path, 0775, true);
        $types = [ 'assets', 'namespaces', 'packages', 'views' ];

        foreach ( $types as $pathType )
        {
            $this->files->makeDirectory(Path::join($path, config('laradic.themes.paths.' . $pathType)), 0775, true);
        }
    }
}
