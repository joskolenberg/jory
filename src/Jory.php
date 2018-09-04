<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 04-09-18
 * Time: 21:53
 */

namespace JosKolenberg\Jory;


use JosKolenberg\Jory\Contracts\JoryInterface;

class Jory implements JoryInterface
{

    protected $filters;

    public function __construct()
    {
        $this->filters = new FilterCollection();
    }

    public function addFilter(Filter $filter)
    {
        $this->filters->push($filter);
    }

    public function getFilters(): FilterCollection
    {
        return $this->filters;
    }
}