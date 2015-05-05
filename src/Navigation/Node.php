<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes\Navigation;

use Laradic\Themes\Contracts\NavigationNode;
use Tree\Node\Node as BaseNode;

/**
 * This is the Node class.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class Node extends BaseNode
{

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var \Laradic\Themes\Navigation\Factory
     */
    protected $factory;

    /**
     * @var bool
     */
    protected $requiredPermissions;

    /**
     * @var bool
     */
    protected $requiresLogin = false;

    /**
     * @var \Cartalyst\Sentry\Sentry
     */
    protected $sentry;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var bool
     */
    protected $hidden = false;


    public function __construct($id, Factory $factory, $value = null, array $children = [])
    {
        parent::__construct($value, $children);
        $this->id      = $id;
        $this->factory = $factory;
        $this->sentry  = $factory->getSentry();
    }

    /**
     * hasAccess
     *
     * @param $permissions
     * @return bool
     */
    public function hasAccess($permissions)
    {
        if ( isset($this->sentry) and $this->requiresLogin === true )
        {
            if ( ! $this->sentry->check() )
            {
                return false;
            }
            if ( isset($this->requiredPermissions) )
            {
                if ( ! $this->sentry->getUser()->hasAccess($permissions) )
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Adds a meta
     *
     * @param string|array $name
     * @param null         $value
     * @return $this
     */
    public function addMeta($name, $value = null)
    {
        if ( is_array($name) )
        {
            foreach ($name as $n => $v)
            {
                $this->addMeta($n, $v);
            }
        }
        else
        {
            $this->meta[$name] = $value;
        }

        return $this;
    }

    public function render($view)
    {
        return $this->factory->render($this->id, $view);
    }

    /**
     * Get the value of id
     *
     * @return mixed|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id
     *
     * @param mixed|null $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


    /**
     * Get the value of meta
     *
     * @return array
     */
    public function getMeta($name = null)
    {
        if ( ! is_null($name) )
        {
            return isset($this->meta[$name]) ? $this->meta[$name] : null;
        }

        return $this->meta;
    }

    public function meta()
    {
        $meta = '';
        foreach ($this->meta as $k => $v)
        {
            $meta .= " $k='$v'";
        }

        return $meta;
    }

    /**
     * Sets the value of meta
     *
     * @param array $meta
     * @return $this
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get the value of requiredPermissions
     *
     * @return boolean
     */
    public function isRequiredPermissions()
    {
        return $this->requiredPermissions;
    }

    /**
     * Sets the value of requiredPermissions
     *
     * @param boolean $requiredPermissions
     * @return $this
     */
    public function setRequiredPermissions($requiredPermissions)
    {
        $this->requiredPermissions = $requiredPermissions;

        return $this;
    }

    /**
     * Get the value of requiresLogin
     *
     * @return boolean
     */
    public function isRequiresLogin()
    {
        return $this->requiresLogin;
    }

    /**
     * Sets the value of requiresLogin
     *
     * @param boolean $requiresLogin
     * @return $this
     */
    public function setRequiresLogin($requiresLogin)
    {
        $this->requiresLogin = $requiresLogin;

        return $this;
    }

    /**
     * hasChildren
     *
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->getChildren()) > 0;
    }

    /**
     * Get the value of hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * Sets the value of hidden
     *
     * @param boolean $hidden
     * @return $this
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }
}
