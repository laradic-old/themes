<?php namespace Laradic\Themes;

use Illuminate\View\FileViewFinder;
use Laradic\Support\ServiceProvider;
use View;

class ThemeServiceProvider extends ServiceProvider
{

    /** @inheritdoc */
    protected $configFiles = ['radic_themes'];

    /** @inheritdoc */
    protected $dir = __DIR__;

    protected $providers = [
        'Laradic\Themes\Providers\BusServiceProvider',
        'Laradic\Themes\Providers\EventServiceProvider',
        'Radic\BladeExtensions\BladeExtensionsServiceProvider',
        'Collective\Html\HtmlServiceProvider'
    ];

    protected $aliases = [
        'Markdown' => 'Radic\BladeExtensions\Facades\Markdown',
        'Form'     => 'Collective\Html\FormFacade',
        'HTML'     => 'Collective\Html\HtmlFacade'
    ];

    public function boot()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = parent::boot();

        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app->make('config');

        $themes = $app->make('themes');

        $themes->setConfig($config->get('radic_themes'));
        $themes->setActive($config->get('radic_themes.active'));
      #  $themes->boot();
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

        $this->registerThemes();
       # $this->registerWidgets();
        $this->registerViewFinder();
        $this->registerAssets();

        if ( $app->runningInConsole() )
        {
            $app->register('Laradic\Themes\Providers\ConsoleServiceProvider');
        }

        $app->make('events')->listen('creating: *', function (\Illuminate\Contracts\View\View $view) use ($app)
        {
            $app->make('themes')->boot();
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

    public function registerWidgets()
    {
        $this->app->singleton('themes.widgets', $this->app->make('config')->get('radic_themes.widgetsClass'));
        $this->app->alias('themes.widgets', 'Laradic\Themes\Contracts\Widgets');

        $this->app->booting(function ()
        {
            $this->alias('Widgets', 'Laradic\Themes\Facades\Widgets');
        });

        $this->app->make('themes.widgets')->registerDirectives();
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
