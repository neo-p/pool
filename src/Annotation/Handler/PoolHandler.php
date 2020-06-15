<?php

namespace NeoP\Pool\Annotation\Handler;

use NeoP\DI\Container;
use NeoP\Annotation\Annotation\Handler\Handler;
use NeoP\Annotation\Annotation\Mapping\AnnotationHandler;
use NeoP\Pool\Annotation\Mapping\Pool;
use NeoP\Pool\Contract\PoolOriginInterface;
use NeoP\Pool\Exception\PoolException;
use ReflectionClass;

/**
 * @AnnotationHandler(Pool::class)
 */
class PoolHandler extends Handler
{
    public function handle(Pool $annotation, ReflectionClass $reflection)
    {
        $type = $annotation->getType();
        $pools = service('pool.' . $type, []);
        foreach ($pools as $name => $config) {
            $instance = $reflection->newInstance();
            if (! ($instance instanceof PoolOriginInterface)) {
                throw new PoolException("Pool：". $this->className . ' is not implements ' . PoolOriginInterface::class);
            }
            $instance->get($config, $name);
        }
    }
}