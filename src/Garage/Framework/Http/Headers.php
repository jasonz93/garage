<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-5-8
 * Time: 上午11:11
 */

namespace Garage\Framework\Http;


use Garage\Framework\Collection;
use Garage\Framework\Interfaces\HeadersInterface;

class Headers extends Collection implements HeadersInterface
{
    protected static $special = [
        'CONTENT_TYPE' => 1,
        'CONTENT_LENGTH' => 1,
        'PHP_AUTH_USER' => 1,
        'PHP_AUTH_PW' => 1,
        'PHP_AUTH_DIGEST' => 1,
        'AUTH_TYPE' => 1
    ];

    public static function createFromEnvironment(Environment $environment) {
        $data = [];
        foreach ($environment as $key => $value) {
            $key = strtoupper($key);
            if (isset(self::$special[$key])) {
                $data[$key] = $value;
                continue;
            }
            if (strpos($key, 'HTTP_') === 0) {
                $data[substr($key, 5)] = $value;
            }

        }

        return new self($data);
    }

    public function normalizeKey($key)
    {
        $key = strtolower($key);
        if (strpos($key, 'http_') === 0) {
            $key = substr($key, 5);
        }
        return $key;
    }

    public function add($key, $value)
    {
        $oldValues = $this->get($key, []);
        $newValues = is_array($value) ? $value : [$value];
        $this->set($key, array_merge($oldValues, array_values($newValues)));
    }

    public function set($key, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        parent::set($this->normalizeKey($key), [
            'value' => $value,
            'originalKey' => $key
        ]);
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return array
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return parent::get($this->normalizeKey($key))['value'];
        }
        return $default;
    }

    public function has($key)
    {
        return parent::has($this->normalizeKey($key));
    }

    public function remove($key)
    {
        parent::remove($this->normalizeKey($key));
    }

    public function all()
    {
        $all = parent::all();
        $out = [];
        foreach ($all as $key => $value) {
            $out[$value['originalKey']] = $value['value'];
        }
        return $out;
    }
}