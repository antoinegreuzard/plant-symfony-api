<?php

namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Symfony\Component\Routing\RouterInterface;

class Paginator
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function paginate(
        $query,
        int $page = 1,
        int $limit = 10,
        string $routeName = '',
        array $routeParams = []
    ): array {
        $offset = ($page - 1) * $limit;
        $query->setFirstResult($offset)->setMaxResults($limit);

        $paginator = new DoctrinePaginator($query);
        $total = count($paginator);
        $lastPage = (int)ceil($total / $limit);

        $next = $page < $lastPage ? $this->router->generate(
            $routeName,
            array_merge($routeParams, ['page' => $page + 1]),
            0
        ) : null;
        $previous = $page > 1 ? $this->router->generate(
            $routeName,
            array_merge($routeParams, ['page' => $page - 1]),
            0
        ) : null;

        return [
            'count' => $total,
            'next' => $next,
            'previous' => $previous,
            'results' => iterator_to_array($paginator),
        ];
    }
}
