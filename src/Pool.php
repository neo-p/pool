<?php

namespace NeoP\Pool;

use Swoole\Coroutine\Channel;

use NeoP\Pool\Node;
use NeoP\Pool\Contract\PoolOriginInterface;
use NeoP\Pool\Contract\PoolInterface;
use NeoP\Pool\Exception\PoolException;

abstract class Pool implements PoolInterface
{
    /**
     * 源
     *
     * @var object
     */
    protected $_origin;

    /**
     * 连接池名称
     *
     * @var string
     */
    protected $name;

    /**
     * 连接池
     *
     * @var Channel
     */
    protected $_pool;

    /**
     * 最多可空闲连接数
     * @var int
     */
    protected $_maxIdle = 5;

    /**
     * 当前连接数
     * @var int
     */
    protected $length = 0;

    /**
     * 最大连接数
     * @var int
     */
    protected $_maxConnect = 5;

    public function pool($origin, int $maxConnect = 5, int $maxIdle = 5)
    {
        $this->_origin = $origin;
        $this->setMaxConnect($maxConnect);
        $this->setMaxIdle($maxIdle);
        $this->_pool = new Channel($this->_maxIdle);
    }

    public function release(&$node): bool
    {
        if (is_object($node)) {
            if (! $this->_pool->isFull()) {
                $this->_pool->push($node);
            } else {
                unset($node);
                $this->decr();
            }
            return true;
        }
        return false;
    }

    public function node()
    {
        if($this->_pool->isEmpty() && $this->length() < $this->maxConnect()) {
            $node = clone $this->_origin;
            $this->incr();
        } else {
            $node = $this->_pool->pop();
        }
        
        return $node;
    }

    protected function setMaxConnect(int $maxConnect = 5) 
    {
        $this->_maxConnect = $maxConnect;
    }

    protected function setMaxIdle(int $maxIdle = 5)
    {
        $this->_maxIdle = $maxIdle;
    }

    protected function length(): int
    {
        return $this->length;
    }

    protected function incr(int $step = 1)
    {
        $this->length += $step;
    }

    protected function decr(int $step = 1)
    {
        $this->length -= $step;
    }

    protected function maxConnect(): int
    {
        return $this->_maxConnect;
    }

    public function __call($name, $arguments)
    {
        $result = $this->node()->$name(...$arguments);
        return $result;
    }

    abstract public function _create(array $config);
}