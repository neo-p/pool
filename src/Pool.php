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

    public function _pool($origin, int $maxConnect = 5, int $maxIdle = 5)
    {
        $this->_origin = $origin;
        $this->_setMaxConnect($maxConnect);
        $this->_setMaxIdle($maxIdle);
        $this->_pool = new Channel($this->_maxIdle);
    }

    public function _release(&$node): bool
    {
        if (is_object($node)) {
            if (! $this->_pool->isFull()) {
                $this->_pool->push($node);
            } else {
                unset($node);
                $this->_decr();
            }
            return true;
        }
        return false;
    }

    public function _node()
    {
        if($this->_pool->isEmpty() && $this->_length() < $this->_maxConnect()) {
            $node = clone $this->_origin;
            $this->_incr();
        } else {
            $node = $this->_pool->pop();
        }
        
        return $node;
    }

    protected function _setMaxConnect(int $maxConnect = 5) 
    {
        $this->_maxConnect = $maxConnect;
    }

    protected function _setMaxIdle(int $maxIdle = 5)
    {
        $this->_maxIdle = $maxIdle;
    }

    protected function _length(): int
    {
        return $this->length;
    }

    protected function _incr(int $step = 1)
    {
        $this->length += $step;
    }

    protected function _decr(int $step = 1)
    {
        $this->length -= $step;
    }

    protected function _maxConnect(): int
    {
        return $this->_maxConnect;
    }

    public function __call($name, $arguments)
    {
        $result = $this->_node()->$name(...$arguments);
        return $result;
    }

    abstract public function _create(array $config);
}