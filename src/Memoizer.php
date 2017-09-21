<?php

namespace drupol\Memoize;

/**
 * Class Memoizer.
 *
 * @package drupol\Memoize
 */
class Memoizer extends Memoize
{
    /**
     * The callable.
     *
     * @var null|\ReflectionFunction
     */
    private $callable = null;

    /**
     * The time to live.
     *
     * @var int
     */
    private $ttl = null;

    /**
     * Memoizer constructor.
     *
     * @param callable $callable
     *   The callable.
     * @param int $ttl
     *   The time to live.
     */
    public function __construct(Callable $callable, $ttl = null)
    {
        $this->callable = new \ReflectionFunction($callable);
        $this->ttl = $ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return $this->memoize($this->callable->getClosure(), func_get_args(), $this->ttl);
    }
}
