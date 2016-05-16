<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-5-8
 * Time: 上午11:06
 */

namespace Garage\Framework\Interfaces;


interface HeadersInterface extends CollectionInterface
{
    public function normalizeKey($key);
    public function add($key, $value);
}