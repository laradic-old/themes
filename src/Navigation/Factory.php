<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Navigation;

use Breadcrumbs;
use DaveJamesMiller\Breadcrumbs\Generator;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Laradic\Themes\Contracts\NavigationFactory;

/**
 * This is the Factory class.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class Factory implements NavigationFactory
{

    /** @var \Cartalyst\Sentry\Sentry */
    protected $sentry;

    /** @var \Illuminate\Contracts\Routing\UrlGenerator */
    protected $generator;

    /** @var \Illuminate\Support\Collection */
    protected $items;

    /** @var \Illuminate\Contracts\View\Factory */
    protected $view;

    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Instantiates the class
     *
     * @param \Cartalyst\Sentry\Sentry                   $sentry
     * @param \Illuminate\Contracts\Routing\UrlGenerator $generator
     * @param \Illuminate\Contracts\View\Factory         $view
     * @param \Illuminate\Routing\Router                 $router
     */
    public function __construct(UrlGenerator $generator, ViewFactory $view, Router $router)
    {
        #$this->tree      = new Node('root', $this, '');
        $this->items  = new Collection();
        $this->view   = $view;
        $this->router = $router;
        #$this->sentry    = $sentry;
        $this->generator = $generator;
    }

    /**
     * add
     *
     * @param        $id
     * @param        $value
     * @param null   $parent
     * @param string $link
     * @param bool   $authenticated
     * @param array  $permissions
     * @return \Laradic\Themes\Navigation\Node
     */
    public function add($id, $value, $parent = null, $link = '#', $authenticated = false, array $permissions = [])
    {
        $node = new Node($id, $this, $value);

        $node->addMeta('id', $id);
        if ( ! is_null($parent) and $this->items->has($parent) )
        {
            $parentNode = $this->items->get($parent);
            $parentNode->addChild($node);
            $node->addMeta('data-parent', $parent);
        }

        #\Route::getRoutes()->hasNamedRoute()

        $url = 'javascript:;';
        if ( is_string($link) )
        {
            if ( $this->router->getRoutes()->hasNamedRoute($link) )
            {
                $link = $this->router->getRoutes()->getByName($link);
            }
            else
            {
                $url = $link;
            }
        }

        if ( $link instanceof Route )
        {
            if ( ! is_null($link->getName()) )
            {
                $url = $this->generator->route($link->getName());
            }
            else
            {
                $url = $this->generator->to($link->uri());
            }
        }

        $node->addMeta('href', $url);

        $this->items->put($id, $node);

        return $node;
    }

    /**
     * get
     *
     * @param $id
     * @return Node
     */
    public function get($id)
    {
        $node = $this->items->get($id);

        return $node;
    }

    public function render($id, $view = null)
    {
        if ( ! $this->items->has($id) )
        {
            return '';
        }

        $node = $this->get($id);

        return $this->view
            ->make(is_null($view) ? 'theme::navigation.default' : $view)
            ->with('navigation', $node)
            ->render();
    }

    public function registerBreadcrumbs($crumbs, $parent = null)
    {
        foreach ($crumbs as $route => $crumb)
        {
            Breadcrumbs::register($route, function (Generator $breadcrumbs) use ($parent, $crumb, $route)
            {
                if ( ! is_null($parent) )
                {
                    $breadcrumbs->parent($parent);
                }
                $breadcrumbs->push($crumb[0], route($route));
            });
            if ( isset($crumb[1]) and is_array($crumb[1]) )
            {
                $this->registerBreadcrumbs($crumb[1], $route);
            }
        }
    }

    /**
     * Get the value of sentry
     *
     * @return \Cartalyst\Sentry\Sentry
     */
    public function getSentry()
    {
        return $this->sentry;
    }

    /**
     * Sets the value of sentry
     *
     * @param \Cartalyst\Sentry\Sentry $sentry
     * @return $this
     */
    public function setSentry($sentry)
    {
        $this->sentry = $sentry;

        return $this;
    }

    /**
     * Get the value of generator
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Sets the value of generator
     *
     * @param \Illuminate\Contracts\Routing\UrlGenerator $generator
     * @return $this
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;

        return $this;
    }
}
