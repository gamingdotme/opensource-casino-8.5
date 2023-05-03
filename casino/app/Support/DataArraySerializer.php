<?php

namespace VanguardLTE\Support;

use League\Fractal\Pagination\PaginatorInterface;

class DataArraySerializer extends \League\Fractal\Serializer\DataArraySerializer
{
    /**
     * {@inheritdoc}
     */
    public function item($resourceKey, array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function collection($resourceKey, array $data)
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }

    /**
     * Format pagination data according to Illiminate defined format.
     *
     * @param PaginatorInterface $paginator
     * @return array
     */
    public function paginator(PaginatorInterface $paginator)
    {
        $currentPage = $paginator->getCurrentPage();
        $lastPage = $paginator->getLastPage();

        $nextPage = $lastPage > $currentPage
            ? $paginator->getUrl($currentPage + 1)
            : null;

        $prevPage = $currentPage > 1
            ? $paginator->getUrl($currentPage - 1)
            : null;

        $data = [
            'total' => $paginator->getTotal(),
            'per_page' => $paginator->getPerPage(),
            'current_page' => $currentPage,
            'last_page' => $paginator->getLastPage(),
            'next_page_url' => $nextPage,
            'prev_page_url' => $prevPage,
            'from' => $this->firstItem($paginator),
            'to' => $this->lastItem($paginator),
        ];

        return ['meta' => $data];
    }

    /**
     * Get the number of the first item in the slice.
     *
     * @param PaginatorInterface $paginator
     * @return int
     */
    private function firstItem(PaginatorInterface $paginator)
    {
        if ($paginator->getCount() === 0) {
            return;
        }

        return ($paginator->getCurrentPage() - 1) * $paginator->getPerPage() + 1;
    }

    /**
     * Get the number of the last item in the slice.
     *
     * @param PaginatorInterface $paginator
     * @return int
     */
    private function lastItem(PaginatorInterface $paginator)
    {
        if ($paginator->getCount() === 0) {
            return;
        }

        return $this->firstItem($paginator) + $paginator->getCount() - 1;
    }

    public function meta(array $meta)
    {
        if (empty($meta)) {
            return [];
        }

        return $meta;
    }
}
