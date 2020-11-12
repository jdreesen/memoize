<?php

declare(strict_types=1);

namespace drupol\memoize;

use __PHP_Incomplete_Class;
use Amp\Parallel\Worker\Environment;
use Amp\Parallel\Worker\Task;
use Error;
use function is_array;

class MemoizeTask implements Task
{
    /**
     * @var mixed[]
     */
    private $args;

    private static $cache = [];

    /**
     * @var string
     */
    private $function;

    /**
     * @param string $function Serialized function.
     * @param array  $args Arguments to pass to the function. Must be serializable.
     */
    public function __construct(string $function, array $args)
    {
        $this->function = $function;
        $this->args = $args;
    }

    public function run(Environment $environment)
    {
        $callable = unserialize($this->function, ['allowed_classes' => true]);

        if ($callable instanceof __PHP_Incomplete_Class) {
            throw new Error('When using a class instance as a callable, the class must be autoloadable');
        }

        if (is_array($callable) && $callable[0] instanceof __PHP_Incomplete_Class) {
            throw new Error('When using a class instance method as a callable, the class must be autoloadable');
        }

        $id = json_encode(
            [
                $this->function,
                $this->args,
            ]
        );

        return Memoizer::fromClosure($callable->getClosure(), $id)(...$this->args);
    }
}
