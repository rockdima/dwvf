<?php

namespace App\Core;

class DIContainer {
    private array $instances = [];

    public function set(string $class, object $instance): void {
        $this->instances[$class] = $instance;
    }

    public function get(string $class): object {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        $reflector = new \ReflectionClass($class);
        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            $object = new $class;
        } else {
            $parameters = $constructor->getParameters();
            $dependencies = [];

            foreach ($parameters as $parameter) {
                $dependency = $parameter->getType();

                if ($dependency instanceof \ReflectionNamedType && !$dependency->isBuiltin()) {
                    $dependencies[] = $this->get($dependency->getName());
                }
            }

            $object = $reflector->newInstanceArgs($dependencies);
        }

        $this->instances[$class] = $object;
        return $object;
    }
}
