<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 04-09-18
 * Time: 21:53
 */

namespace JosKolenberg\Jory;


use JosKolenberg\Jory\Contracts\FilterInterface;
use JosKolenberg\Jory\Contracts\JoryInterface;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\FilterGroup;

class Jory
{

    protected $json;
    protected $filter;

    public function setFilter(FilterInterface $filter): Jory
    {
        $this->filter = $filter;
        return $this;
    }

    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }

    public function toJson(): string
    {
        return json_encode($this->data);
    }

}