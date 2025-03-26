<?php

namespace Stub;

use ApiPlatform\State\Pagination\PaginatorInterface;
use ArrayIterator;
use IteratorAggregate;
use stdClass;
use Traversable;

class StubPaginator implements PaginatorInterface, IteratorAggregate
{
    public function getCurrentPage(): float
    {
        return 2.0;
    }

    public function getLastPage(): float
    {
        return 3.0;
    }

    public function getItemsPerPage(): float
    {
        return 10.0;
    }

    public function getTotalItems(): float
    {
        return 100.0;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator([
            new stdClass(),
            new stdClass(),
        ]);
    }

    public function count(): int
    {
        return 2;
    }
}
