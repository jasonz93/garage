<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-5-7
 * Time: 下午4:37
 */

namespace Garage\Framework\Configuration;


use Garage\Framework\Config\Configuration;
use Garage\Framework\DI\Container;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testConfig() {
        $json = '
        {
            "db": {
                "host": "127.0.0.1",
                "_obj": {
                    "class": "Garage\\\Framework\\\DI\\\Container",
                    "args": [
                        {
                            "test": "haha"
                        }
                    ],
                    "call": [
                        {
                            "name": "offsetSet",
                            "args": [
                                "test2", "hehe"
                            ]
                        }
                    ]
                },
                "haha": "_obj"
            },
            "_parentObj": {
                "class": "stdClass"
            }
        }
        ';
        $config = Configuration::readFromJSON($json);
        $this->assertInstanceOf('stdClass', $config->_parentObj);
        $this->assertEquals('127.0.0.1', $config->db->host);
        $this->assertInstanceOf(Container::class, $config->db->_obj);
        $this->assertEquals('haha', $config->db->_obj['test']);
        $this->assertEquals('hehe', $config->db->_obj['test2']);
        $this->assertInstanceOf(Container::class, $config->db->haha);
        $this->assertEquals('haha', $config->db->haha['test']);
    }
}