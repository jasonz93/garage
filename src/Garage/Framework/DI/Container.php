<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-5-7
 * Time: 下午2:35
 */

namespace Garage\Framework\DI;


class Container implements \ArrayAccess
{
    private $values = [];
    private $instances = [];
    private $factories;
    private $frozen = [];

    public function __construct(array $values = [])
    {
        $this->factories = new \SplObjectStorage();
        foreach ($values as $name => $value) {
            $this->offsetSet($name, $value);
        }
    }

    public function singleton($name, $callable) {
        $this->offsetSet($name, $callable);
    }

    public function factory($name, $callable) {
        if (!method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
        }
        $this->factories->attach($callable);
        $this->values[$name] = $callable;
        return $callable;
    }

    public function freeze($name) {
        $this->frozen[$name] = true;
    }

    public function unfreeze($name) {
        unset($this->frozen[$name]);
    }

    public function get($name) {
        return $this->offsetGet($name);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        //An simple value is registered as a service, just return.
        if (!is_object($this->values[$offset]) ||
            !method_exists($this->values[$offset], '__invoke')) {
            return $this->values[$offset];
        }
        //Service is registered in factory mode, return an new instance.
        if ($this->factories->contains($this->values[$offset])) {
            return $this->values[$offset]($this);
        }
        //Service is registered in singleton mode and has already been instantiated, return this instance.
        if (isset($this->instances[$offset])) {
            return $this->instances[$offset];
        }

        $instance = $this->values[$offset]($this);
        $this->instances[$offset] = $instance;
        $this->frozen[$offset] = true;
        return $instance;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (isset($this->frozen[$offset])) {
            throw new \RuntimeException(sprintf('Cannot override frozen service %s, maybe already instantiated?', $offset));
        }
        $this->values[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if (isset($this->values[$offset])) {
            if (is_object($this->values[$offset])) {
                unset($this->factories[$this->values[$offset]]);
            }
            unset($this->instances[$offset], $this->values[$offset], $this->frozen[$offset]);
        }
    }

    public function register(ProviderInterface $provider) {
        $provider->register($this);
    }
}