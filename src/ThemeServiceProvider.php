<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Themes;

use Illuminate\Foundation\Application;
use Illuminate\View\FileViewFinder;
use Laradic\Config\Traits\ConfigProviderTrait;
use Laradic\Support\ServiceProvider;
use Laradic\Themes\Assets\AssetFactory;
use View;

/**
 * This is the ThemeServiceProvider class.
 *
 * @package        Laradic\Themes
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class ThemeServiceProvider extends ServiceProvider
{
    protected $providers = [
        'Laradic\Themes\Providers\BusServiceProvider',
        'Laradic\Themes\Providers\EventServiceProvider',
        'Laradic\Themes\Providers\ConsoleServiceProvider',
        'Radic\BladeExtensions\BladeExtensionsServiceProvider',
        'DaveJamesMiller\Breadcrumbs\ServiceProvider',
        'Collective\Html\HtmlServiceProvider'
    ];

    protected $aliases = [
       // 'Breadcrumbs' => 'DaveJamesMiller\Breadcrumbs\Facade',
        #'Markdown'    => 'Radic\BladeExtensions\Facades\Markdown',
        'Form'        => 'Collective\Html\FormFacade',
        'HTML'        => 'Collective\Html\HtmlFacade'
    ];

    public function boot()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = parent::boot();

        $app->make('themes')
            ->setActive(config('laradic.themes.active'))
            ->setDefault(config('laradic.themes.default'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = parent::register();

        $this->linkConfig();
        $this->registerNavigation();
        $this->registerAssets();
        $this->registerThemes();
        $this->registerViewFinder();

        $app->make('events')->listen('creating: *', function (\Illuminate\Contracts\View\View $view) use ($app)
        {
            $app->make('themes')->boot();
        });
    }

    protected function linkConfig()
    {
        $configPath = realpath(__DIR__.'/../resources/config/config.php');
        $this->mergeConfigFrom($configPath, 'laradic.themes');
        $this->publishes([ $configPath => config_path('laradic.themes.php') ], 'config');
    }

    protected function registerAssets()
    {
        $this->app->singleton('assets', function(Application $app)
        {
            $assetFactory = new AssetFactory($app['themes']); //Laradic\Themes\Assets\AssetFactory
            $assetFactory->setAssetClass(config('laradic.themes.assetClass'));
            $assetFactory->setAssetGroupClass(config('laradic.themes.assetGroupClass'));
            $assetFactory->setCachePath(config('laradic.themes.paths.cache'));
            foreach ( config('laradic.themes.assets.globalFilters') as $extension => $filters )
            {
                foreach ( $filters as $filter )
                {
                    $assetFactory->addGlobalFilter($extension, $filter);
                }
            }
            return $assetFactory;
        });
        $this->app->alias('assets', 'Laradic\Themes\Contracts\AssetFactory');
    }

    protected function registerThemes()
    {
        $this->app->singleton('themes', function (Application $app)
        {
            $themeFactory = new ThemeFactory($app, $app->make('files'), $app->make('events'));
            $themeFactory->setPaths(config('laradic.themes.paths'));
            $themeFactory->setThemeClass(config('laradic.themes.themeClass'));
            $themeFactory->setNavigation($app->make('navigation'));
            $themeFactory->setBreadcrumbs($app->make('breadcrumbs'));
            return $themeFactory;
        });
        $this->app->alias('themes', 'Laradic\Themes\Contracts\ThemeFactory');
    }

    protected function registerNavigation()
    {


        $this->app->singleton('navigation', 'Laradic\Themes\Navigation\Factory');
        $this->app->bind('Laradic\Themes\Contracts\NavigationFactory', 'navigation');


        /** @var \Illuminate\View\Compilers\BladeCompiler $blade */
        $blade = $this->app->make('view')->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->extend(function ($value) use ($blade)
        {
            $matcher = $blade->createMatcher('navigation');
            $replace = '$1<?php echo app("navigation")->render$2 ?>';

            return preg_replace($matcher, $replace, $value);
        });
    }


    protected function registerViewFinder()
    {
        /**
         * @var $oldViewFinder FileViewFinder
         */
        $oldViewFinder = $this->app['view.finder'];

        $this->app->bind('view.finder', function ($app) use ($oldViewFinder)
        {
            $paths = array_merge(
                $app['config']['view.paths'],
                $oldViewFinder->getPaths()
            );

            $themesViewFinder = new ThemeViewFinder($app['files'], $paths, $oldViewFinder->getExtensions());
            $themesViewFinder->setThemes($app['themes']);

            foreach ($oldViewFinder->getPaths() as $location)
            {
                $themesViewFinder->addLocation($location);
            }

            foreach ($oldViewFinder->getHints() as $namespace => $hints)
            {
                $themesViewFinder->addNamespace($namespace, $hints);
            }

            return $themesViewFinder;
        });

        View::setFinder($this->app['view.finder']);
    }

}
