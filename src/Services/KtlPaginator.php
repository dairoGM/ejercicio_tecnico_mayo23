<?php

namespace App\Services;

class KtlPaginator
{
    protected $itemsPerpage;
    protected $currentPage;
    protected $pageRange;
    protected $count;

    public function setItemCountPerPage($items_per_page)
    {
        $this->itemsPerpage = $items_per_page;
    }

    public function getItemCountPerPage()
    {
        return $this->itemsPerpage;
    }

    public function setPageRange($page_range)
    {
        $this->pageRange = $page_range;
    }

    public function getPageRange()
    {
        return $this->pageRange;
    }

    public function setCurrentPageNumber($current_page_number)
    {
        $this->currentPage = filter_var($current_page_number, FILTER_VALIDATE_INT, [
            'options' => [
                'default' => 1,
                'min_range' => 1,
                'max_range' => $this->count(),
            ],
        ]);
    }

    public function getCurrentPageNumber()
    {
        return $this->currentPage;
    }

    public function setTotalItemsCount($items_count)
    {
        $this->count = $items_count;
    }

    public function getTotalItemsCount()
    {
        return $this->count;
    }

    public function getAdjacentLinks()
    {
        $current_page_number = $this->getCurrentPageNumber();
        $range_pages = $this->getPageRange();

        $result = range(1, $this->count());

        if (($range_pages = floor($range_pages / 2) * 2 + 1) >= 1) {
            $result = array_slice($result, max(0, min(count($result) - $range_pages, intval($current_page_number) - ceil($range_pages / 2))), $range_pages);
        }

        return $result;
    }

    public function count()
    {
        return ceil($this->getTotalItemsCount() / $this->getItemCountPerPage());
    }

    public function hasPrevious()
    {
        return ($this->count() > 1 && $this->getCurrentPageNumber() > 1) ? true : false;
    }

    public function hasNext()
    {
        return ($this->count() > 1 && $this->getCurrentPageNumber() < $this->count()) ? true : false;
    }

    public function hasItems()
    {
        return ($this->getTotalItemsCount() == 0) ? false : true;
    }
}
