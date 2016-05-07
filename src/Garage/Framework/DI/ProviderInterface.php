<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-5-7
 * Time: 下午3:13
 */

namespace Garage\Framework\DI;


interface ProviderInterface
{
    public function register(Container $container);
}