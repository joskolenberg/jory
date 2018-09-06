<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 04-09-18
 * Time: 21:04
 */

namespace JosKolenberg\Jory\Support;


use Illuminate\Support\Collection;
use JosKolenberg\Jory\Contracts\FilterInterface;
use JosKolenberg\Jory\Support\Filter;

abstract class FilterGroup implements \IteratorAggregate, FilterInterface
{

    protected $filters;

    public function __construct()
    {
        $this->filters = new Collection();
    }

    public function push(FilterInterface $filter)
    {
        $this->filters->push($filter);
    }

    public function getIterator()
    {
        return $this->filters->getIterator();
    }
}