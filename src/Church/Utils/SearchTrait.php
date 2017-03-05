<?php

namespace Church\Utils;

/**
 * Search Trait
 */
trait SearchTrait
{

    /**
     * Search through a colleciton and return the first instance.
     *
     * @param iterable $collection
     * @param callable $callback
     */
    protected function search(iterable $collection, callable $callback)
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
