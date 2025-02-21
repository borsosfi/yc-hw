<?php
declare(strict_types=1);

namespace YoCierge;

use Iterator;
use SplHeap;

class SortedIterator extends SplHeap
{
    public function __construct(Iterator $iterator)
    {
        foreach($iterator as $item) {
            $this->insert($item);
        }
    }

    public function compare(mixed $b, mixed $a): int
    {
        return strcmp($a->getRealpath(), $b->getRealpath());
    }
}
