<?php

class Pagination
{

    public $current_page;
    public $per_page;
    public $total_count;

    public function __construct($page = 1, $per_page = 32, $total_count = 0)
    {

        $this->current_page = (int) $page;
        $this->per_page     = (int) $per_page;
        $this->total_count  = (int) $total_count;
    }

    public function offset()
    {
        return ($this->current_page - 1) * $this->per_page;
    }

    public function totalPages()
    {
        return ceil($this->total_count / $this->per_page);
    }

    // right function to check if previous or next exists
    public function previousPage()
    {
        return $this->current_page - 1;
    }

    public function nextPage()
    {
        return $this->current_page + 1;
    }

    public function hasPreviousPage()
    {
        return $this->previousPage() >= 1 ? true : false;
    }

    public function hasNextPage()
    {
        return $this->nextPage() <= $this->totalPages() ? true : false;
    }

}
