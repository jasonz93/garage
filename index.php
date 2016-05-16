<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-5-8
 * Time: 下午12:09
 */

require 'vendor/autoload.php';

$environment = new \Garage\Framework\Http\Environment($_SERVER);
$uri = \Garage\Framework\Http\Uri::createFromEnvironment($environment);
$headers = \Garage\Framework\Http\Headers::createFromEnvironment($environment);
//var_dump($uri);
var_dump($headers->all());