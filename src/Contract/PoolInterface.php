<?php
namespace NeoP\Pool\Contract;

interface PoolInterface
{
    public function _createPool(array $config, string $name);
}