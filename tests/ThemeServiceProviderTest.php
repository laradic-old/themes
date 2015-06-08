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

/**
 * Class StrTest
 *
 * @package Laradic\Test\Support
 */
class ThemeSupportServiceProviderTest extends TestCase
{
    use ServiceProviderTestCaseTrait;

    protected function getServiceProviderClass($app)
    {
        return 'Laradic\Themes\ThemeServiceProvider';
    }
}
