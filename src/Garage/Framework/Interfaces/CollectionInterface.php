<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-5-8
 * Time: 上午11:00
 */

namespace Garage\Framework\Interfaces;


interface CollectionInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    public function set($key, $value);
    public function get($key);
    public function replace(array $items);
    public function all();
    public function has($key);
    public function remove($key);
    public function clear();
}