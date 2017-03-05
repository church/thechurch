<?php

namespace Church\Utils;

/**
 * Array Trait
 */
class ArrayUtils
{

    /**
     * Search through a colleciton and return the first instance.
     *
     * @param iterable $collection
     * @param callable $callback
     */
    public static function search(iterable $collection, callable $callback)
    {
        $item = reset($collection);
        while ($item !== false) {
            if ($callback($item)) {
                return $item;
            };
            $item = next($collection);
        }

        return null;
    }
}
