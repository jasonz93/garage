<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-5-7
 * Time: 下午2:52
 */

namespace Garage\Framework\DI;


class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    private $container;

    public function setUp()
    {
        $this->container = new Container();
    }

    public function testSingleton() {
        $this->container->singleton('testSingleton', function () {
            return new \stdClass();
        });
        $obj1 = $this->container['testSingleton'];
        $this->assertInstanceOf('stdClass', $obj1);
        $obj1->test = 'hahaha';
        $obj2 = $this->container['testSingleton'];
        $this->assertEquals('hahaha', $obj2->test, 'Service is not singleton.');
    }

    public function testFactory() {
        $counter = new \stdClass();
        $counter->count = 0;
        $this->container->factory('testFactory', function () use ($counter) {
            $obj = new \stdClass();
            $obj->test = "Object {$counter->count}";
            $counter->count++;
            return $obj;
        });
        $obj0 = $this->container['testFactory'];
        $this->assertEquals('Object 0', $obj0->test);
        $obj1 = $this->container['testFactory'];
        $this->assertEquals('Object 1', $obj1->test);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFrozen() {
        $this->container['testFrozen'] = function () {return new \stdClass();};
        $obj = $this->container['testFrozen'];
        $this->container['testFrozen'] = function () {};
    }

    public function testProvider() {
        $this->container->register(new Provider());
        $obj = $this->container['testProvider'];
        $this->assertEquals('testProvider service', $obj->test, 'Service defined in provider is not registered.');
    }
}

class Provider implements ProviderInterface {

    public function register(Container $container)
    {
        $container['testProvider'] = function () {
            $obj = new \stdClass();
            $obj->test = 'testProvider service';
            return $obj;
        };
    }
}