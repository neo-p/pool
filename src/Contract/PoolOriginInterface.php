<?php
namespace NeoP\Pool\Contract;

interface PoolOriginInterface
{
    public function get(array $config, string $name): PoolInterface;
}