<?php


namespace Laradic\Tests\Themes;

use Collective\Html\HtmlServiceProvider;
use Laradic\Dev\AbstractTestCase;
use Laradic\Dev\Traits\BladeViewTestingTrait;

/**
 * Class ViewTest
 *
 * @author     Robin Radic
 * @inheritDoc
 */
abstract class TestCase extends AbstractTestCase
{
    protected function getConfig(){
        return require '../resources/config/config.php';
    }

    protected function assertTheme($theme){
        $this->assertInstanceOf(\Laradic\Themes\Theme::class, $theme);
        $this->assertInstanceOf(\vierbergenlars\SemVer\Internal\SemVer::class, $theme->getVersion());
    }

}
