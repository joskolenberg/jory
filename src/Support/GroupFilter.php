<?php

namespace JosKolenberg\Jory\Support;

use JosKolenberg\Jory\Contracts\FilterInterface;

/**
 * Class for holding a collection of Filters.
 *
 * Class GroupFilter
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
        $this->position++;
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

    /**
     * Tell if this filter contains a filter on the given field.
     *
     * @param string $field
     * @return bool
     */
    public function hasFilter(string $field): bool
    {
        foreach ($this->filters as $filter) {
            if ($filter->hasFilter($field)) {
                return true;
            }
        }

        return false;
    }
}
