<?php

namespace tdp\di;

use Closure;
use Exception;
use LogicException;
use ReflectionClass;
use ReflectionException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var array[] The container's bindings.
     */
    protected $bindings = [];

    /**
     * @var object[] The container's shared instances.
     */
    protected $instances = [];

    /**
     * @var string[] The registered type mappings.
     */
    protected $mappings = [];

    /**
     *  {@inheritdoc}
     */
    public function get($id)
    {
        try {
            return $this->resolve($id);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Register a binding with the container.
     *
     * @param  string  $id
     * @param  \Closure|string|null  $vallue
     * 
     * @return void
     */
    public function bind($id, $value = null, array $params = [])
    {
        unset($this->instances[$id], $this->mappings[$id]);

        if ($vallue == null) {
            $value = $id;
        }

        $this->bindings[$id] = [$value, $params];
    }

    /**
     * Map a type to a different name.
     *
     * @param  string  $id
     * @param  string  $alias
     * 
     * @return void
     *
     * @throws \LogicException
     */
    public function map($id, $alias)
    {
        if ($alias === $id) {
            throw new LogicException('[' . $id . '] is aliased to itself.');
        }

        $this->mappings[$id] = $alias;
    }

    /**
     *  {@inheritdoc}
     */
    public function has($id)
    {
        return isset($this->bindings[$id]) ||
               isset($this->instances[$id]) ||
               isset($this->mappings[$id]);
    }

    /**
     * Extends an object definition.
     *
     * @param string   $id
     * @param callable $callable
     *
     * @return void
     *
     * @throws \tdp\di\EntryNotFoundException
     */
    public function extend($id, Closure $callable)
    {
        if (!isset($this->instances[$id])) {
            throw new EntryNotFoundException($id);
        }

        $this->instances[$id] = $callable($this->instances[$id], $this);
    }

    /**
     * Get a new instance of a class.
     *
     * @param string|callable $class
     * @param array $params
     * 
     * @return object
     * 
     * @throws \tdp\di\NotInstantiableException
     */
    public function create($class, $params = [])
    {
        if (is_callable($class)) {
            return call_user_func_array($class, $params);
        }

        if (is_object($class)) {
            return $class;
        }
        
        switch (count($params)) {
            case 0:
                return new $class();
            case 1:
                return new $class($params[0]);
            case 2:
                return new $class($params[0], $params[1]);
            case 3:
                return new $class($params[0], $params[1], $params[2]);
            case 4:
                return new $class($params[0], $params[1], $params[2], $params[3]);
            case 5:
                return new $class($params[0], $params[1], $params[2], $params[3], $params[4]);
            default:
                try {
                    $ref = new ReflectionClass($class);
                    return $ref->newInstanceArgs($params);
                } catch (ReflectionException $e) {
                    throw new NotInstantiableException($class);
                }
        }
    }

    /**
     * Resolve the given type from the container.
     *
     * @param  string  $id
     * @param  array   $parameters
     * 
     * @return mixed
     *
     * @throws \tdp\di\EntryNotFoundException
     */
    protected function resolve($id, array $parameters = [])
    {
        $id = $this->getRequestedId($id);

        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->bindings[$id])) {
            throw new EntryNotFoundException($id);
        }

        list($class, $params) = $this->bindings[$id];

        $params = array_merge($params, $parameters);
        $object = $this->create($class, $params);

        $this->instances[$id] = $object;

        return $object;
    }

    /**
     * Get the alias for an abstract if available.
     * 
     * @param  string  $id
     * 
     * @return string
     */
    protected function getRequestedId($id)
    {
        if (isset($this->mappings[$id])) {
            return $this->mappings[$id];
        }

        return $id;
    }
}
