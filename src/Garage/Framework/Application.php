<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-5-7
 * Time: 下午5:45
 */

namespace Garage\Framework;


use Garage\Framework\Configuration;

class Application
{
    protected $di;
    protected $config;

    public function readConfigurationFromFile($path) {
        $this->config = Configuration::readFromFile($path);
    }

    public function readConfigurationFromProjectRoot($projectRoot) {
        $folder = opendir($projectRoot.'/config');
        $configuration = new Configuration();
        while ($file = readdir($folder)) {
            if (mb_substr($file, mb_strlen($file) - 5) === '.json') {
                $configuration::readFromFile($projectRoot.'/config/'.$file, $configuration);
            }
        }
        $this->config = $configuration;
    }

    public function getConfiguration() {
        return $this->config;
    }
}