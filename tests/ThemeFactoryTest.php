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

use Illuminate\Support\NamespacedItemResolver;
use Laradic\Support\String;
use Laradic\Themes\Assets\AssetFactory;

use Laradic\Themes\Theme;
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
    protected $fs, $factory;
    public function setUp()
    {
        parent::setUp();
        $this->fs = m::mock('Illuminate\Filesystem\Filesystem');
        $this->factory = new ThemeFactory($this->fs, $this->app->make('events'));
        $this->factory->setPaths($this->paths);
        $this->factory->setThemeClass(\Laradic\Themes\Theme::class);
    }

    public function tearDown()
    {
        m::close();
    }

    protected function _resolveTheme($slug = 'frontend/example', array $config = []){
        $config['slug'] = $slug;
        $this->fs->shouldReceive('getRequire')->andReturn($this->_getThemeConfig($config));
        return $this->factory->resolveTheme($slug);
    }

    protected function _getThemePath($slug = 'frontend/example'){
        list($area, $key) = with(new NamespacedItemResolver)->parseKey($slug);
        return $this->factory->getThemePath(public_path('themes'), $key, $area);
    }

    public function testResolveTheme()
    {
        $this->fs->shouldReceive('isDirectory')->once()->andReturn(true);
        $this->fs->shouldReceive('exists')->once()->andReturn(true);
        $this->assertTheme($this->_resolveTheme());
    }

    public function testActiveTheme()
    {
        $this->fs->shouldReceive('isDirectory')->once()->andReturn(true);
        $this->fs->shouldReceive('exists')->once()->andReturn(true);
        $this->_resolveTheme();
        $this->factory->setActive('frontend/example');
        $this->assertTheme($this->factory->getActive());
    }

    /**
     * testThemePath
     * @expectedException \RuntimeException
     *
     */
    public function testGetActiveIfNotSetThrowsException()
    {
        $this->factory->getActive();
    }

    public function testDefaultTheme()
    {
        $this->fs->shouldReceive('isDirectory')->once()->andReturn(true);
        $this->fs->shouldReceive('exists')->once()->andReturn(true);
        $this->_resolveTheme();
        $this->factory->setDefault('frontend/example');
        $this->assertTheme($this->factory->getDefault());
    }

    public function testHasGetAllCountMethods()
    {
        $this->fs->shouldReceive('isDirectory')->once()->andReturn(true);
        $this->fs->shouldReceive('exists')->once()->andReturn(true);
        $this->_resolveTheme();
        $this->assertTrue($this->factory->has('frontend/example'));
        $this->assertTrue(is_array($this->factory->all()));
        $this->assertInArray('frontend/example', $this->factory->all());
        $this->assertEquals(1, $this->factory->count());
        $this->assertTheme($this->factory->get('frontend/example'));
        $this->assertEquals('namespaces', $this->factory->getPath('namespaces'));
    }

    /**
     * testThemePath
     * @expectedException \RuntimeException
     *
     */
    public function testGetDefaultIfNotSetThrowsException()
    {
        $this->factory->getDefault();
    }

    /**
     * testFactoryCannotResolveTheme
     * @expectedException \Symfony\Component\Filesystem\Exception\FileNotFoundException
     */
    public function testResolvedThemeNoConfigFoundException()
    {
        $this->fs->shouldReceive('isDirectory')->once()->andReturn(true);
        $this->fs->shouldReceive('exists')->once()->andReturn(false);
        $this->_resolveTheme();
    }

    public function testResolveThemeReturnsNone()
    {
        $this->fs->shouldReceive('isDirectory')->twice()->andReturn(false);
        $this->assertNull($this->_resolveTheme());
    }

    public function testArrayAccess(){
        $this->fs->shouldReceive('isDirectory')->once()->andReturn(true);
        $this->fs->shouldReceive('exists')->once()->andReturn(true);
        $this->_resolveTheme();
        $this->assertTheme($this->factory['frontend/example']);
    }

    /**
     * testThemePath
     * RuntimeException
     *
     */
    public function testThemePath()
    {
        $this->assertEquals(public_path('themes/proper/slug'), $this->_getThemePath('proper/slug'));
        $this->assertEquals(public_path('themes/slug'), $this->_getThemePath('slug'));
    }

    /**
     * testThemePath
     * @expectedException \RuntimeException
     *
     */
    public function testInvalidThemePathWith3Segments()
    {
        $this->_getThemePath('this/should/fail');
    }

    // @todo: to do..
    public function testAddNamespace()
    {
        $this->factory->addNamespace('namespace', 'directory');
        /**
         * @var \Illuminate\View\FileViewFinder $finder
         */
        $finder = $this->app->make('view.finder');
        $hints = $finder->getHints();
        $paths = $finder->getPaths();
        $a = 'c';
    }

    public function testBoot()
    {
        $active = m::mock(\Laradic\Themes\Theme::class);
        $parent = m::mock(\Laradic\Themes\Theme::class);
        $default = m::mock(\Laradic\Themes\Theme::class);
        $active->shouldReceive('getSlug')->twice()->andReturn('frontend/example');
        $default->shouldReceive('getSlug')->twice()->andReturn('frontend/default');
        $this->factory
            ->setActive($active)
            ->setDefault($default);
        $active->shouldReceive('boot')->once()->andReturn();
        $active->shouldReceive('hasParent')->once()->andReturn(true);
        $active->shouldReceive('getParentTheme')->once()->andReturn($parent);
        $parent->shouldReceive('boot')->once()->andReturn();
        $default->shouldReceive('boot')->once()->andReturn();
        $this->factory->boot(true, true);
    }

    public function testGettersSetters(){
        $this->app->register(\Laradic\Themes\ThemeServiceProvider::class);
        /** @var \Laradic\Themes\ThemeFactory $themes */
        $themes = $this->app['themes'];
        $this->assertInstanceOf(\Laradic\Themes\Contracts\ThemeViewFinder::class, $themes->getFinder());
        $this->assertTrue(is_array($themes->getPublishers()));
        $themes->setThemeClass(\Laradic\Themes\Theme::class);
        $this->assertEquals(\Laradic\Themes\Theme::class, $themes->getThemeClass());
        $this->assertInstanceOf(\ArrayIterator::class, $themes->getIterator());


    }
    public function testFactory()
    {

        //$fsm = m::mock('Illuminate\Filesystem\Filesystem');
        #$factory = $this->factory($fs);
        #list($area, $key) = with(new NamespacedItemResolver)->parseKey($slug);
        #$themePath = $factory->getThemePath(public_path('themes'), $key, $area);
        //call_user_func_array([$factory, 'getThemePath'], );
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;
        $app->register('Laradic\Themes\ThemeServiceProvider');
        $f = $app->make('themes');
        #$f->boot();
        VarDumper::dump($f->getThemeClass());
        $p = $f->count();
        $this->assertTrue(true);
    }

}
