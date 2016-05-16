<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-5-7
 * Time: 下午5:50
 */

namespace Garage\Framework;


class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testReadConfigurationFromProjectRoot() {
        $application = new Application();
        $application->readConfigurationFromProjectRoot(__DIR__);
        $config = $application->getConfiguration();
        $this->assertEquals('haha', $config->haha);
        $this->assertEquals('hehe', $config->hehe);
    }
}