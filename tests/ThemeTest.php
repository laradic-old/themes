<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Tests\Themes;

use Laradic\Themes\Theme;
use Mockery as m;

/**
 * This is the ThemeTest.
 *
 * @package        Laradic\Tests
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class ThemeTest extends TestCase
{
    protected $factory, $fs, $events, $path, $theme;

    protected function _getTheme(array $opts = [])
    {
        $this->factory->shouldReceive('getFiles')->twice()->andReturn($this->fs);
        $this->fs->shouldReceive('exists')->once()->andReturn(true);
        $this->fs->shouldReceive('getRequire')->once()->andReturn($this->_getThemeConfig($opts));
        return new Theme($this->factory, $this->events, $this->path);
    }

    public function setUp()
    {
        parent::setUp();
        $this->factory     = m::mock('Laradic\Themes\ThemeFactory');
        $this->fs          = m::mock('Illuminate\Filesystem\Filesystem');
        $this->events      = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $this->path        = public_path('themes/frontend/example');
    }

    public function testDefaultConstruct()
    {
        $theme = $this->_getTheme();
        $this->assertEquals('Frontend example', $theme->getName());
        $this->assertEquals('frontend/example', $theme->getSlug());
        $this->assertEquals('example', $theme->getSlugKey());
        $this->assertEquals('frontend', $theme->getSlugProvider());
        $this->assertInstanceOf(\vierbergenlars\SemVer\Internal\SemVer::class, $theme->getVersion());
        $this->assertFalse($theme->hasParent());
        $this->factory->shouldReceive('getActive')->once()->andReturn(null);
        $this->assertFalse($theme->isActive());
        $this->factory->shouldReceive('getDefault')->once()->andReturn(null);
        $this->assertFalse($theme->isDefault());
        $this->assertFalse($theme->isBooted());
    }

    public function testConstructWithRegisterClosure()
    {
        $that = $this;
        $this->_getTheme([
            'register' => function($app, $_theme) use ($that) {
                $that->assertInstanceOf('Illuminate\Contracts\Foundation\Application', $app);
                $that->assertInstanceOf(\Laradic\Themes\Theme::class, $_theme);
            }
        ]);
    }


    /**
     * testThemeConfigFileNotFoundException
     *
     * @expectedException \Symfony\Component\Filesystem\Exception\FileNotFoundException
     */
    public function testThemeConfigFileNotFoundException()
    {
        $this->factory->shouldReceive('getFiles')->once()->andReturn($this->fs);
        $this->fs->shouldReceive('exists')->once()->andReturn(false);
        new Theme($this->factory, $this->events, $this->path);
    }

    public function testBootThemeWithoutClosure()
    {
        $theme = $this->_getTheme();
        $this->events->shouldReceive('fire')->once()->andReturn();
        $theme->boot();
        $this->assertTrue($theme->isBooted());
    }


    public function testBootThemeWithClosure()
    {
        $that = $this;
        $theme = $this->_getTheme([
            'boot' => function($app, $_theme) use ($that) {
                $that->assertInstanceOf('Illuminate\Contracts\Foundation\Application', $app);
                $that->assertInstanceOf(\Laradic\Themes\Theme::class, $_theme);
            }
        ]);
        $this->events->shouldReceive('fire')->once()->andReturn();
        $theme->boot();
        $this->assertTrue($theme->isBooted());
    }

    public function testGetPaths(){
        $theme = $this->_getTheme();
        $this->assertEquals($this->path, $theme->getPath());

        $this->factory->shouldReceive('getPath')->once()->with(m::mustBe('namespaces'))->andReturn($this->paths['namespaces']);
        $this->assertEquals($this->path . '/' . $this->paths['namespaces'], $theme->getPath('namespaces'));

        $this->factory->shouldReceive('getPath')->once()->with(m::mustBe('packages'))->andReturn($this->paths['packages']);
        $this->assertEquals($this->path . '/' . $this->paths['packages'], $theme->getPath('packages'));

        $this->factory->shouldReceive('getPath')->once()->with(m::mustBe('views'))->andReturn($this->paths['views']);
        $this->assertEquals($this->path . '/' . $this->paths['views'], $theme->getPath('views'));

        $this->factory->shouldReceive('getPath')->once()->with(m::mustBe('assets'))->andReturn($this->paths['assets']);
        $this->assertEquals($this->path . '/' . $this->paths['assets'], $theme->getPath('assets'));

    }

    public function testConstructWithParentTheme()
    {
        $parent = m::mock('Laradic\Themes\Theme');

        $this->factory->shouldReceive('resolveTheme')->with(m::mustBe('frontend/parent'))->andReturn($parent);
        $theme = $this->_getTheme([
            'parent' => 'frontend/parent'
        ]);

        $this->assertTrue($theme->hasParent());
        $this->assertEquals('frontend/parent', $theme->getParentSlug());
        $this->assertInstanceOf(get_class($parent), $theme->getParentTheme());
    }


}