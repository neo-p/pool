<?php
namespace NeoP\Pool\Contract;

interface PoolOriginInterface
{
    public function _get(array $config, string $name): PoolInterface;
}