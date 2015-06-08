<?php
/**
 * Part of the Laradic packages.
 * MIT License and copyright information bundled with this package in the LICENSE file.
 *
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
namespace Laradic\Tests\Themes;

use Laradic\Dev\AbstractTestCase;
use Laradic\Dev\Traits\LaravelTestCaseTrait;
use Laradic\Dev\Traits\ServiceProviderTestCaseTrait;
use Laradic\Themes\Assets\Asset;
use Laradic\Themes\Assets\AssetFactory;
use Laradic\Themes\ThemeFactory;
use Mockery as m;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class StrTest
 *
 * @package Laradic\Test\Support
 */
class ThemeFactoryTest extends TestCase
{

    protected function getFactoryMock()
    {
        return new ThemeFactory(m::mock('Illuminate\Filesystem\Filesystem'), m::mock('Illuminate\Events\Dispatcher'));
        #VarDumper::dump($f);
    }

    public function testFactory()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;
        $app->register('Laradic\Themes\ThemeServiceProvider');
        $f = $app->make('themes');
        #$f->boot();
        VarDumper::dump($f->getThemeClass());
        $p = $f->count();
        $this->assertTrue(true);
    }

    public function testMakeAsset()
    {
        $assetFactory = new AssetFactory($themesMock = $this->getFactoryMock());

    }
}
