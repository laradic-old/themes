<?php namespace Laradic\Themes;

/**
 * Part of the Radic packages.
 * Licensed under the MIT license.
 *
 * @package        themes
 * @author         Robin Radic
 * @license        MIT
 * @copyright  (c) 2011-2015, Robin Radic
 * @link           http://radic.mit-license.org
 */

use Illuminate\Support\NamespacedItemResolver;
use Illuminate\View\FileViewFinder;
use InvalidArgumentException;
use Laradic\Themes\Contracts\ThemeFactory;
use Laradic\Themes\Contracts\ThemeViewFinder as ThemeViewFinderContract;

/**
 * ThemesViewFinder
 *
 * @package Laradic\Themes
 */
class ThemeViewFinder extends FileViewFinder implements ThemeViewFinderContract
{

    /**
     * @var $themes \Laradic\Themes\ThemeFactory
     */
    protected $themes;

    /** @inheritdoc */
    public function find($name)
    {
        $name = str_replace('.', '/', $name);

        if (!isset($this->themes))
        {
            return parent::find($name);
        }

        $resolver = new NamespacedItemResolver;
        list($area, $view) = $resolver->parseKey($name);

        try
        {
            if (isset($area))
            {
                if (isset($this->hints[$area]))
                {
                    $sectionType = 'namespaces';
                    $paths       = $this->themes->getCascadedPaths('namespaces', $area, 'views');
                }
                else
                {
                    $sectionType = 'packages';
                    $paths       = $this->themes->getCascadedPaths('packages', $area, 'views');
                   # $paths       = $this->themes->getCascadedPackageViewPaths($area);
                }

                $view = $this->findInPaths($view, $paths);
            }
            else
            {
                $paths = $this->themes->getCascadedPaths('views');
                $view  = $this->findInPaths($view, $paths);
            }
        } // We couldn't find the view using our theming system
        catch (InvalidArgumentException $e)
        {
            try
            {
                return parent::find($name);
            }
            catch (InvalidArgumentException $e)
            {
                $active   = $this->themes->getActive();
                $fallback = $this->themes->getDefault();

                if (isset($area))
                {
                    $message = sprintf('Theme [%s] view [%s] could not be found in theme [%s]',
                        $sectionType, $name, $active->getSlug()
                    );
                }
                else
                {
                    $message = sprintf('Theme view [%s] could not be found in theme [%s]',
                        $name, $active->getSlug()
                    );
                }

                throw new InvalidArgumentException($message);
            }
        }
        return $view;
    }

    /** @inheritdoc */
    public function getThemes()
    {
        return $this->themes;
    }

    /** @inheritdoc */
    public function setThemes(ThemeFactory $themes)
    {
        $this->themes = $themes;

        return $this;
    }
}
