<?php

namespace JosKolenberg\Jory\Support;


use Illuminate\Support\Collection;
use JosKolenberg\Jory\Contracts\FilterInterface;
use JosKolenberg\Jory\Exceptions\JoryException;
use JosKolenberg\Jory\Support\Filter;

/**
 * Class for holding a collection of Filters
 *
 * Class GroupFilter
 * @package JosKolenberg\Jory\Support
 */
abstract class GroupFilter implements \Iterator, \Countable, FilterInterface
{

    protected $position = 0;
    protected $filters = [];

    public function push(FilterInterface $filter): void
    {
        $this->filters[] = $filter;
    }

    public function getIterator()
    {
        return $this->filters->getIterator();
    }

    public function current()
    {
        return $this->filters[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->filters[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function count()
    {
        return iterator_count($this);
    }

    public function getByIndex($index)
    {
        return $this->filters[$index];
    }

}