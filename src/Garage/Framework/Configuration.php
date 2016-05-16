<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-5-7
 * Time: 下午3:49
 */

namespace Garage\Framework;


class Configuration
{
    /**
     * @var Configuration|null
     */
    private $parent;
    private $objectDefinitions = [];
    private $objects = [];
    private $values;

    public function __construct($parent = null)
    {
        $this->values = new \stdClass();
        $this->parent = $parent;
    }

    public static function readFromFile($path, $configuration = null) {
        $jsonStr = file_get_contents($path);
        return self::readFromJSON($jsonStr, $configuration);
    }

    public static function readFromJSON($string, $configuration = null) {
        $json = json_decode($string);
        if ($json === null) {
            throw new \InvalidArgumentException('Illegal JSON string.');
        }
        return self::readFromObject($json, $configuration);
    }

    public static function readFromObject($obj, $configuration = null, $parent = null) {
        if ($configuration === null) {
            $configuration = new Configuration($parent);
        }
        $configuration->addValues($obj);
        return $configuration;
    }

    public function addValues($values) {
        foreach ($values as $k => $v) {
            if ($k[0] === '_') {
                $this->addObjectDefinition($k, $v);
                continue;
            }
            $this->values->$k = is_object($v) ? self::readFromObject($v, null, $this) : $v;
        }
    }

    public function addObjectDefinition($name, $definition) {
        if (!isset($definition->class)) {
            throw new \InvalidArgumentException('Object definition must contains \'class\' field.');
        }
        $this->objectDefinitions[$name] = $definition;
    }

    public function getObject($name) {
        if (isset($this->objects[$name])) {
            return $this->objects[$name];
        }
        if (isset($this->objectDefinitions[$name])) {
            $this->objects[$name] = $this->institateObject($this->objectDefinitions[$name]);
            return $this->objects[$name];
        } else if ($this->parent !== null) {
            $obj = $this->parent->getObject($name);
            if ($obj !== null) {
                return $obj;
            }
            throw new \RuntimeException(sprintf('Object %s is not defined.', $name));
        } else {
            return null;
        }
    }

    public function institateObject($objectDefinition) {
        $class = new \ReflectionClass($objectDefinition->class);
        $args = [];
        if (isset($objectDefinition->args)) {
            if (!is_array($objectDefinition->args)) {
                throw new \InvalidArgumentException('\'args\' field of an object definition must be an array.');
            }
            $args = $this->parseArgs($objectDefinition->args);
        }
        if ($class->getConstructor() === null) {
            $obj = $class->newInstance();
        } else {
            $obj = $class->newInstanceArgs($args);
        }
        //Call methods described in definitions
        //TODO: Solve circular references
        if (isset($objectDefinition->call)) {
            if (!is_array($objectDefinition->call)) {
                throw new \InvalidArgumentException('\'call\' field of an object definition must be an array.');
            }
            foreach ($objectDefinition->call as $method) {
                if (!isset($method->name)) {
                    throw new \InvalidArgumentException('Method name is not defined in \'name\' field.');
                }
                if (!method_exists($obj, $method->name)) {
                    throw new \RuntimeException(sprintf('Method %s is not defined in class %s', $method->name, $objectDefinition->class));
                }
                $methodArgs = [];
                if (isset($method->args)) {
                    if (!is_array($method->args)) {
                        throw new \InvalidArgumentException('\'args\' field in a call definition must be an array.');
                    }
                    $methodArgs = $this->parseArgs($method->args);
                }
                call_user_func_array([$obj, $method->name], $methodArgs);
            }
        }
        return $obj;
    }

    private function parseArgs($args) {
        $result = [];
        foreach ($args as $arg) {
            if (is_string($arg) && strlen($arg) > 0 && $arg[0] === '_') {
                $result[] = $this->getObject($arg);
            } else {
                if (is_object($arg)) {
                    $arr = [];
                    foreach ($arg as $k => $v) {
                        $arr[$k] = $v;
                    }
                    $result[] = $arr;
                } else {
                    $result[] = $arg;
                }
            }
        }
        return $result;
    }

    public function __get($name)
    {
        if ($name[0] === '_') {
            return $this->getObject($name);
        }
        if (!isset($this->values->$name)) {
            throw new \RuntimeException(sprintf('Configuration key %s is not defined.', $name));
        }
        $val = $this->values->$name;
        if (is_string($val) && strlen($val) > 0 && $val[0] === '_') {
            return $this->getObject($val);
        }
        return $val;
    }
}