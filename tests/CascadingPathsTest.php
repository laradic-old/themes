<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Tests\Themes;

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
class CascadingPathsTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->app->instance('path.public', realpath(__DIR__ . '/fixture/public'));
        $this->app->register(\Laradic\Themes\ThemeServiceProvider::class);
    }

    protected function assertViewContent($view,$expected){
        $content = $this->app[ 'view' ]->make($view)->render();
        $this->assertEquals($expected, $content);
    }
    public function testCascade()
    {
        /**
         * @var \Laradic\Themes\ThemeFactory $themes
         */
        $themes = $this->app[ 'themes' ];

        /**
         * @var \Illuminate\View\Factory $view
         */
        $view = $this->app[ 'view' ];

        $this->assertEquals('frontend/example', $themes->getActive()->getSlug());
        $this->assertEquals('frontend/default', $themes->getDefault()->getSlug());
        $this->assertEquals('frontend/parent', $themes->getActive()->getParentTheme()->getSlug());



        #$themes->addNamespace('nstest', 'nstest');
        $view->addNamespace('nstest', $themes->getPath('namespaces') . '/nstest');
        $this->assertViewContent('index', 'index of frontend/example');
        $this->assertViewContent('nstest::index', 'index of frontend/example::nstest');
        $this->assertViewContent('testvendor/testpkg::index', 'index of frontend/example::testvendor/testpkg');

        // test parent and default fallbacks
        $this->assertViewContent('parent-fallback', 'parent-fallback content');
        $this->assertViewContent('nstest::parent-fallback', 'nstest parent-fallback content');
        $this->assertViewContent('testvendor/testpkg::parent-fallback', 'testvendor/testpkg parent-fallback content');

        $this->assertViewContent('default-fallback', 'default-fallback content');
        $this->assertViewContent('nstest::default-fallback', 'nstest default-fallback content');
        $this->assertViewContent('testvendor/testpkg::default-fallback', 'testvendor/testpkg default-fallback content');


        #$themes->addNamespace('nstestaa', 'nstest');

        #$this->assertViewContent('nstestaa::index', 'index of frontend/example::nstest');
    }


}