<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Themes;

use Closure;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\View\Compilers\BladeCompiler;
use InvalidArgumentException;
use Laradic\Themes\Contracts\Widgets as WidgetsContract;

/**
 * Class Widgets
 *
 * @package     Laradic\Themes
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class Widgets implements WidgetsContract
{

    protected $view;

    protected $blade;

    protected $widgets = [];

    /**
     * Instanciates the class
     */
    public function __construct(View $view, BladeCompiler $blade)
    {
        $this->view  = $view;
        $this->blade = $blade;
    }

    public function create($name, Closure $callback)
    {
        $this->widgets[$name] = $callback;
    }

    public function exists($name, $throwException = false)
    {
        #\Debugger::log('widgets', $this->widgets);
        $exists = isset($this->widgets[$name]);
        if ( !$exists and $throwException )
        {
            throw new InvalidArgumentException("Could not find widget [$name]. The widget does not exist");
        }

        return $exists;
    }

    public function registerDirectives()
    {
        $this->blade->extend(function ($value, BladeCompiler $blade)
        {
            $pattern = $blade->createMatcher('widget');
            $replace = '$1<?php echo app("themes.widgets")->render$2; ?>';

            return preg_replace($pattern, $replace, $value);
        });
    }

    public function render()
    {
        $args      = func_get_args();
        $name      = head($args);
        $arguments = array_slice($args, 1);
        $this->exists($name, true);

        return call_user_func_array($this->widgets[$name], $arguments);
    }
}
