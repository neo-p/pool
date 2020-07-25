<?php

namespace NeoP\Pool\Contract;

interface PoolInterface
{
    public function _release(&$node): bool;
    public function _node();
}
