<?php namespace Laradic\Themes;

use Config;
use File;
use Illuminate\Http\Request;
use Illuminate\View\FileViewFinder;
use Laradic\Support\ServiceProvider;
use Laradic\Themes\Providers\BusServiceProvider;
use Laradic\Themes\Providers\EventServiceProvider;
use View;

class ThemeServiceProvider extends ServiceProvider
{

    /** @inheritdoc */
    protected $configFiles = ['radic_themes'];

    /** @inheritdoc */
    protected $dir = __DIR__;

    /** @inheritdoc */
    public function boot()
    {
        parent::boot();
        $this->app->make('themes')
            ->setConfig($this->app['config']['radic_themes'])
            ->setActive($this->app['config']['radic_themes.active']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->register('Radic\BladeExtensions\BladeExtensionsServiceProvider');
        $this->alias('Markdown', 'Radic\BladeExtensions\Facades\Markdown');

        $this->app->register('Collective\Html\HtmlServiceProvider');
        $this->alias('Form', 'Collective\Html\FormFacade');
        $this->alias('HTML', 'Collective\Html\HtmlFacade');

        $this->app->register(new BusServiceProvider($this->app));
        $this->app->register(new EventServiceProvider($this->app));

        $this->registerThemes();
        $this->registerViewFinder();
        $this->registerAssets();

        if($this->app->runningInConsole())
        {
            $this->app->register('Laradic\Themes\Providers\ConsoleServiceProvider');
        }

        $app = $this->app;
        $this->app->events->listen('creating: *', function(\Illuminate\Contracts\View\View $view) use ($app)
        {
            $app->themes->getActive()->boot();
        });
    }

    public function registerAssets()
    {
        $this->app->singleton('assets', 'Laradic\Themes\Assets\AssetFactory');
        $this->app->alias('assets', 'Laradic\Themes\Contracts\AssetFactory');
        $this->app->booting(function ()
        {
            $this->alias('Asset', 'Laradic\Themes\Facades\Asset');
        });
    }

    public function registerThemes()
    {
        $this->app->singleton('themes', 'Laradic\Themes\ThemeFactory');
        $this->app->alias('themes', 'Laradic\Themes\Contracts\ThemeFactory');

        $this->app->booting(function ()
        {
            $this->alias('Themes', 'Laradic\Themes\Facades\Themes');
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

            //
            //$this->app['themes']->setViewFactory
            return $themesViewFinder;
        });


        View::setFinder($this->app['view.finder']);
        //$viewServiceProvider = new ViewServiceProvider($this->app);
        //$viewServiceProvider->registerFactory();
    }
}
