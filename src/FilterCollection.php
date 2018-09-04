<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 04-09-18
 * Time: 21:04
 */

namespace JosKolenberg\Jory;


use Illuminate\Support\Collection;

class FilterCollection implements \IteratorAggregate
{

    protected $filters;

    public function __construct()
    {
        $this->filters = new Collection();
    }

    public function push(Filter $filter)
    {
        $this->filters->push($filter);
    }

    public function getIterator()
    {
        return $this->filters->getIterator();
    }
}