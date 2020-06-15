<?php

namespace NeoP\Pool\Contract;

interface PoolInterface
{
    public function release(&$node): bool;
    public function node();
}
