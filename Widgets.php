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

    /**
     * The view factory instance
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * The blade compiler instance
     * @var \Illuminate\View\Compilers\BladeCompiler
     */
    protected $blade;

    /**
     * The registered widgets
     * @var \Closure[]
     */
    protected $widgets = [];

    /**
     * Instanciates the class
     *
     * @param \Illuminate\Contracts\View\Factory       $view
     * @param \Illuminate\View\Compilers\BladeCompiler $blade
     */
    public function __construct(View $view, BladeCompiler $blade)
    {
        $this->view  = $view;
        $this->blade = $blade;
    }

    /**
     * Registers a widget
     *
     * @param          $name
     * @param callable $callback
     */
    public function create($name, Closure $callback)
    {
        $this->widgets[$name] = $callback;
    }

    /**
     * Checks if a widget exists, optionally throwing an exception if not found
     *
     * @param      $name
     * @param bool $throwException
     * @return bool
     */
    public function exists($name, $throwException = false)
    {
        $exists = isset($this->widgets[$name]);
        if ( ! $exists and $throwException )
        {
            throw new InvalidArgumentException("Could not find widget [$name]. The widget does not exist");
        }

        return $exists;
    }

    /**
     * Registers the @widget directive
     */
    public function registerDirectives()
    {
        $this->blade->extend(function ($value, BladeCompiler $blade)
        {
            $pattern = $blade->createMatcher('widget');
            $replace = '$1<?php echo app("themes.widgets")->render$2; ?>';

            return preg_replace($pattern, $replace, $value);
        });


    }

    /**
     * Renders a widget
     *
     * @param string $name the widget name
     * @param mixed $param,...
     * @return string The rendered widget
     */
    public function render($name, $param)
    {
        $arguments = array_slice(func_get_args(), 1);
        $this->exists($name, true);

        return call_user_func_array($this->widgets[$name], $arguments);
    }


    /**
     * Handle magic __call methods against the class.
     *
     * @param  string $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters = array())
    {
        $this->exists($method, true);
        return $this->render($method, $parameters);
    }
}
