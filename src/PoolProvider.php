<?php

namespace NeoP\Pool;

use NeoP\Pool\Contract\PoolInterface;

class PoolProvider
{
    /**
     * array
     *
     * @var array(Pool)
     */
    protected static $_poos = [];

    public static $line = 0;

    public static function getPools()
    {
        return self::$_poos;
    }

    public static function getPool($name)
    {
        return self::$_poos[$name];
    }

    public static function hasPool($name)
    {
        return isset(self::$_poos[$name]);
    }

    public static function setPool($name, PoolInterface $pool)
    {
        self::$_poos[$name] = $pool;
    }
}
